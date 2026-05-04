<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PostingPayment;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));
        $monthStart = $period . '-01';

        $invoiceTotal = Invoice::where('period', $period)->sum('total');
        $invoiceUnpaid = Invoice::where('period', $period)->where('is_paid', false)->sum('total');
        $paymentTotal = Payment::whereBetween('date', [$monthStart, now()->endOfMonth()->toDateString()])->sum('amount');

        $arEnding = Receivable::where('period', $period)->sum('ending_balance');

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'invoice_total' => (float)$invoiceTotal,
                'invoice_unpaid' => (float)$invoiceUnpaid,
                'payment_total' => (float)$paymentTotal,
                'ar_ending_balance' => (float)$arEnding,
            ]
        ]);
    }

    public function arSummary(Request $request)
    {
        $period = $request->input('period', now()->format('Y-m'));

        $rows = Receivable::select(
                'client_id',
                'client_code',
                'client_name',
                DB::raw('SUM(ending_balance) as ending_balance')
            )
            ->where('period', $period)
            ->groupBy('client_id', 'client_code', 'client_name')
            ->orderBy('client_name')
            ->get();

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => $rows
        ]);
    }

    public function arLedger(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'from' => 'required|string|size:7',
            'to' => 'required|string|size:7',
        ]);

        $rows = Receivable::with('invoice')
            ->where('client_id', $validated['client_id'])
            ->whereBetween('period', [$validated['from'], $validated['to']])
            ->orderBy('period')
            ->get();

        $totalEnding = $rows->sum('ending_balance');

        return response()->json([
            'success' => true,
            'filter' => $validated,
            'total_ending_balance' => (float)$totalEnding,
            'data' => $rows,
        ]);
    }

    /**
     * Invoice Register Report - detailed or summary
     */
    public function invoiceRegister(Request $request)
    {
        $validated = $request->validate([
            'invoice_type' => 'nullable|in:license,non_license,all',
            'status' => 'nullable|in:paid,unpaid,all',
            'period_from' => 'required|date',
            'period_to' => 'required|date',
            'save_to_excel' => 'nullable|boolean',
            'detailed' => 'nullable|boolean',
        ]);

        $invoices = Invoice::with(['client', 'invoiceType', 'items'])
            ->whereBetween('date', [$validated['period_from'], $validated['period_to']])
            ->orderBy('date')
            ->get();

        if (($validated['invoice_type'] ?? 'all') !== 'all') {
            $isLicense = $validated['invoice_type'] === 'license';
            $invoices = $invoices->filter(function ($invoice) use ($isLicense) {
                return (bool) optional($invoice->invoiceType)->is_license === $isLicense;
            })->values();
        }

        if (($validated['status'] ?? 'all') !== 'all') {
            $wantPaid = $validated['status'] === 'paid';
            $invoices = $invoices->filter(function ($invoice) use ($wantPaid) {
                return (bool) $invoice->is_paid === $wantPaid;
            })->values();
        }

        if ($validated['detailed'] ?? false) {
            $data = $invoices->map(function($inv) {
                return [
                    'tgl' => optional($inv->date)->format('Y-m-d'),
                    'no_invoice' => $inv->number,
                    'jth_tempo' => optional($inv->due_date)->format('Y-m-d'),
                    'no_fkt_pajak' => $inv->tax_number,
                    'items' => $inv->items->map(function($item) {
                        return [
                            'item' => $item->item_name,
                            'deskripsi' => $item->description,
                            'qty' => (float)$item->qty,
                            'satuan' => $item->unit,
                            'harga' => (float)$item->price,
                            'jumlah' => (float)$item->bruto,
                            'discount' => 0.0,
                            'dpp' => (float)$item->bruto,
                            'ppn' => 0.0,
                        ];
                    }),
                    'total' => (float)$inv->total,
                    'no_km_bm' => $inv->invoice_bm_km,
                    'tgl_km_bm' => optional($inv->invoice_bm_km_date)->format('Y-m-d'),
                    'client_code' => $inv->client->code ?? 'N/A',
                    'client_name' => $inv->client->name ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'filter' => $validated,
                'mode' => 'detail',
                'total_invoices' => $invoices->count(),
                'total_dpp' => (float)$invoices->sum('dpp'),
                'total_ppn' => (float)$invoices->sum('ppn'),
                'total_amount' => (float)$invoices->sum('total'),
                'data' => $data
            ]);
        } else {
            $summary = $invoices->groupBy('client_id')->map(function($group) {
                return [
                    'kd_klien' => $group->first()->client->code ?? null,
                    'nama_klien' => $group->first()->client->name ?? null,
                    'jumlah' => (float)$group->sum('bruto'),
                    'discount' => (float)$group->sum('discount'),
                    'dpp' => (float)$group->sum('dpp'),
                    'ppn' => (float)$group->sum('ppn'),
                    'netto' => (float)$group->sum('total'),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'filter' => $validated,
                'mode' => 'rekap',
                'total_invoices' => $invoices->count(),
                'grand_total' => [
                    'jumlah' => (float)$invoices->sum('bruto'),
                    'discount' => (float)$invoices->sum('discount'),
                    'dpp' => (float)$invoices->sum('dpp'),
                    'ppn' => (float)$invoices->sum('ppn'),
                    'netto' => (float)$invoices->sum('total'),
                ],
                'data' => $summary
            ]);
        }
    }

    /**
     * Tax Report - PPN summary per period
     */
    public function taxReport(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|string|size:7'
        ]);

        $invoices = Invoice::where('period', $validated['period'])
            ->where('is_posted', true)
            ->with(['client', 'invoiceType'])
            ->orderBy('tax_date')
            ->get();

        $summary = [
            'total_invoices' => $invoices->count(),
            'total_dpp' => (float)$invoices->sum('dpp'),
            'total_ppn_collectible' => (float)$invoices->sum('ppn'),
            'total_ppn_percentage' => (float)$invoices->average('ppn_percentage'),
        ];

        $byType = $invoices->groupBy('invoice_type_id')->map(function($group) {
            return [
                'type' => $group->first()->invoiceType->name,
                'count' => $group->count(),
                'dpp' => (float)$group->sum('dpp'),
                'ppn' => (float)$group->sum('ppn')
            ];
        })->values();

        return response()->json([
            'success' => true,
            'period' => $validated['period'],
            'summary' => $summary,
            'by_type' => $byType,
            'data' => $invoices->map(function($inv) {
                return [
                    'date' => $inv->date,
                    'tax_date' => $inv->tax_date,
                    'tax_number' => $inv->tax_number,
                    'number' => $inv->number,
                    'client_name' => $inv->client->name,
                    'dpp' => (float)$inv->dpp,
                    'ppn' => (float)$inv->ppn,
                    'invoice_type' => $inv->invoiceType->name
                ];
            })
        ]);
    }

    /**
     * Sales Report - grouped by item, account, or invoice type
     */
    public function salesReport(Request $request)
    {
        $validated = $request->validate([
            'period_from' => 'required|string|size:7',
            'period_to' => 'required|string|size:7',
            'group_by' => 'nullable|in:item,type'
        ]);

        $groupBy = $validated['group_by'] ?? 'type';

        $invoices = Invoice::with(['items.masterItemProduct', 'invoiceType'])
            ->whereBetween('period', [$validated['period_from'], $validated['period_to']])
            ->where('is_posted', true)
            ->get();

        if ($groupBy === 'item') {
            // Group per item
            $result = [];
            foreach ($invoices as $invoice) {
                foreach ($invoice->items as $item) {
                    $key = $item->item_code . ' - ' . $item->item_name;
                    if (!isset($result[$key])) {
                        $result[$key] = [
                            'item_code' => $item->item_code,
                            'item_name' => $item->item_name,
                            'qty' => 0,
                            'total_amount' => 0
                        ];
                    }
                    $result[$key]['qty'] += (float)($item->qty ?? 0);
                    $result[$key]['total_amount'] += (float)($item->bruto ?? 0);
                }
            }
            usort($result, function($a, $b) {
                return $b['total_amount'] <=> $a['total_amount'];
            });
            $data = array_values($result);
        } else {
            // Group per invoice type
            $data = $invoices->groupBy('invoice_type_id')->map(function($invoices) {
                return [
                    'type' => $invoices->first()->invoiceType->name,
                    'count' => $invoices->count(),
                    'total_dpp' => (float)$invoices->sum('dpp'),
                    'total_ppn' => (float)$invoices->sum('ppn'),
                    'total_amount' => (float)$invoices->sum('total')
                ];
            })->values();
        }

        return response()->json([
            'success' => true,
            'filter' => $validated,
            'group_by' => $groupBy,
            'total_invoices' => $invoices->count(),
            'total_amount' => (float)$invoices->sum('total'),
            'data' => $data
        ]);
    }

    /**
     * Cash/Bank Report - payment activity per bank
     */
    public function cashReport(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date_format:Y-m-d',
            'date_to' => 'required|date_format:Y-m-d',
            'bank_id' => 'required|exists:banks,id'
        ]);

        $payments = PostingPayment::where('bank_id', $validated['bank_id'])
            ->whereBetween('date', [$validated['date_from'], $validated['date_to']])
            ->with(['client', 'bank'])
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'filter' => $validated,
            'bank_name' => $payments->first()?->bank->name ?? 'Unknown',
            'total_in' => (float)$payments->sum('total_paid'),
            'payment_count' => $payments->count(),
            'data' => $payments->map(function($payment) {
                return [
                    'date' => $payment->date,
                    'reference_number' => $payment->number,
                    'client_code' => $payment->client->code ?? 'N/A',
                    'client_name' => $payment->client->name,
                    'debet' => (float)$payment->debet,
                    'kredit' => (float)$payment->kredit,
                    'amount' => (float)$payment->total_paid,
                    'description' => $payment->description
                ];
            })
        ]);
    }
}
