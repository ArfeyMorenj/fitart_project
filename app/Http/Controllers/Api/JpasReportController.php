<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JpasReportController extends Controller
{
    public function invoiceHistory(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:50',
        ]);

        $invoice = Invoice::with([
            'client:id,code,name',
            'invoiceType:id,code,name',
            'bank:id,code,name',
            'workOrder:id,number,item_id',
            'workOrder.item:id,code,name',
            'items:id,invoice_id,item_code,item_name,description,qty,unit,price,bruto',
            'payments:id,invoice_id,number,date,amount,description',
            'postingPaymentInvoices:id,posting_payment_id,invoice_id,amount,ppn',
            'postingPaymentInvoices.postingPayment:id,number,date,bank_id,description',
            'debitCreditNotes:id,number,type,date,invoice_id,dpp_amount,ppn_amount,total_amount,description',
            'debitCreditNotes.items:id,debit_credit_note_id,item_code,item_name,description,dpp_amount,ppn_amount',
        ])->where('number', $validated['number'])->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        $paymentTotal = (float) $invoice->payments->sum('amount');
        $outstanding = max(0, (float) $invoice->total - $paymentTotal);

        $journals = Journal::where('document_number', $invoice->number)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'HISTORIS INVOICE',
                'invoice_number' => $invoice->number,
            ],
            'data' => [
                'invoice' => $invoice,
                'summary' => [
                    'invoice_total' => (float) $invoice->total,
                    'payment_total' => $paymentTotal,
                    'outstanding' => $outstanding,
                    'is_paid' => $invoice->is_paid,
                    'is_posted' => $invoice->is_posted,
                ],
                'posting_payments' => $invoice->postingPaymentInvoices->map(function ($row) {
                    return [
                        'posting_number' => $row->postingPayment->number ?? null,
                        'posting_date' => optional($row->postingPayment->date)->format('Y-m-d'),
                        'description' => $row->postingPayment->description ?? null,
                        'amount' => (float) $row->amount,
                        'ppn' => (float) $row->ppn,
                        'total' => (float) $row->amount + (float) $row->ppn,
                    ];
                })->values(),
                'journals' => $journals,
            ],
        ]);
    }

    public function fiscalCommercial(Request $request)
    {
        $validated = $request->validate([
            'period' => ['required', 'date_format:Y-m'],
            'excel' => 'nullable|boolean',
        ]);

        $period = $validated['period'];
        $periodStart = Carbon::createFromFormat('Y-m-d', $period . '-01')->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $invoices = Invoice::with(['client:id,code,name', 'invoiceType:id,code,name'])
            ->where(function ($q) use ($period, $periodStart, $periodEnd) {
                $q->where('period', $period)
                    ->orWhereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                    ->orWhereBetween('tax_date', [$periodStart->toDateString(), $periodEnd->toDateString()]);
            })
            ->orderBy('date')
            ->get();

        $rows = $invoices->map(function (Invoice $invoice) use ($period, $periodStart, $periodEnd) {
            $inCommercial = $invoice->period === $period
                || optional($invoice->date)->between($periodStart, $periodEnd);

            $inFiscal = optional($invoice->tax_date)->between($periodStart, $periodEnd);

            return [
                'invoice_number' => $invoice->number,
                'invoice_date' => optional($invoice->date)->format('Y-m-d'),
                'tax_date' => optional($invoice->tax_date)->format('Y-m-d'),
                'tax_number' => $invoice->tax_number,
                'client_code' => $invoice->client->code ?? null,
                'client_name' => $invoice->client->name ?? null,
                'invoice_type' => $invoice->invoiceType->name ?? null,
                'commercial_dpp' => $inCommercial ? (float) $invoice->dpp : 0.0,
                'commercial_ppn' => $inCommercial ? (float) $invoice->ppn : 0.0,
                'fiscal_dpp' => $inFiscal ? (float) $invoice->dpp : 0.0,
                'fiscal_ppn' => $inFiscal ? (float) $invoice->ppn : 0.0,
                'status' => $inCommercial && $inFiscal
                    ? 'MATCH'
                    : ($inCommercial ? 'COMMERCIAL_ONLY' : 'FISCAL_ONLY'),
            ];
        })->values();

        $summary = [
            'commercial_dpp' => (float) $rows->sum('commercial_dpp'),
            'commercial_ppn' => (float) $rows->sum('commercial_ppn'),
            'fiscal_dpp' => (float) $rows->sum('fiscal_dpp'),
            'fiscal_ppn' => (float) $rows->sum('fiscal_ppn'),
        ];

        $summary['gap_dpp'] = $summary['commercial_dpp'] - $summary['fiscal_dpp'];
        $summary['gap_ppn'] = $summary['commercial_ppn'] - $summary['fiscal_ppn'];

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'LAPORAN FISKAL & KOMERSIAL',
                'period' => $period,
                'excel' => (bool) ($validated['excel'] ?? false),
            ],
            'summary' => $summary,
            'data' => $rows,
        ]);
    }

    public function receivableByItem(Request $request)
    {
        $validated = $request->validate([
            'period_from' => ['required', 'date_format:Y-m'],
            'period_to' => ['required', 'date_format:Y-m'],
            'item_code' => 'nullable|string|max:20',
        ]);

        $start = Carbon::createFromFormat('Y-m-d', $validated['period_from'] . '-01')->startOfMonth();
        $end = Carbon::createFromFormat('Y-m-d', $validated['period_to'] . '-01')->startOfMonth();

        if ($end->lt($start)) {
            return response()->json([
                'success' => false,
                'message' => 'period_to must be greater than or equal to period_from',
            ], 422);
        }

        $months = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        $query = Invoice::with([
            'client:id,code,name',
            'payments:id,invoice_id,amount',
            'workOrder:id,item_id',
            'workOrder.item:id,code,name',
        ])->whereIn('period', $months);

        if (!empty($validated['item_code'])) {
            $query->whereHas('workOrder.item', function ($q) use ($validated) {
                $q->where('code', $validated['item_code']);
            });
        }

        $invoices = $query->orderBy('period')->orderBy('id')->get();

        $rows = [];
        foreach ($invoices as $invoice) {
            if (!$invoice->client || !$invoice->period) {
                continue;
            }

            $clientId = $invoice->client->id;
            if (!isset($rows[$clientId])) {
                $rows[$clientId] = [
                    'client_id' => $clientId,
                    'client_code' => $invoice->client->code,
                    'client_name' => $invoice->client->name,
                    'months' => collect($months)->mapWithKeys(fn ($m) => [$m => [
                        'nominal' => 0.0,
                        'status' => null,
                    ]])->all(),
                ];
            }

            $paid = (float) $invoice->payments->sum('amount');
            $status = $paid >= (float) $invoice->total ? 'L' : 'B';

            $rows[$clientId]['months'][$invoice->period]['nominal'] += (float) $invoice->total;
            $existingStatus = $rows[$clientId]['months'][$invoice->period]['status'];
            $rows[$clientId]['months'][$invoice->period]['status'] = $existingStatus === 'B' ? 'B' : $status;
        }

        $totals = collect($months)->mapWithKeys(fn ($m) => [$m => 0.0])->all();
        foreach ($rows as $row) {
            foreach ($months as $month) {
                $totals[$month] += (float) ($row['months'][$month]['nominal'] ?? 0);
            }
        }

        return response()->json([
            'success' => true,
            'filter' => $validated,
            'months' => $months,
            'total_clients' => count($rows),
            'totals' => $totals,
            'data' => array_values($rows),
        ]);
    }
}
