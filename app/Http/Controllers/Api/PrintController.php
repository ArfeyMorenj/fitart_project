<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\PostingPayment;
use App\Models\PrintHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function batchInvoices(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'number' => 'nullable|string|max:50',
            'client_name' => 'nullable|string|max:255',
            'invoice_type' => 'nullable|in:license,non_license,all',
            'is_paid' => 'nullable|boolean',
            'check_all' => 'nullable|boolean',
        ]);

        $query = Invoice::with(['client:id,code,name', 'invoiceType:id,code,name,is_license'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        $this->applyInvoicePrintFilter($query, $validated);

        $rows = $query->get()->map(function (Invoice $invoice) use ($validated) {
            return [
                'checked' => (bool) ($validated['check_all'] ?? false),
                'invoice_id' => $invoice->id,
                'number' => $invoice->number,
                'date' => optional($invoice->date)->format('Y-m-d'),
                'client_name' => $invoice->client->name ?? null,
                'nominal' => (float) $invoice->total,
                'description' => $invoice->description,
                'invoice_type_code' => $invoice->invoiceType->code ?? null,
                'invoice_type_name' => $invoice->invoiceType->name ?? null,
                'is_paid' => (bool) $invoice->is_paid,
                'pdf_url' => url('/api/print/invoices/' . $invoice->id . '/pdf'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'form' => [
                'title' => 'Cetak Invoice',
                'type' => 'batch_print',
                'filters' => [
                    'date_from' => $validated['date_from'] ?? null,
                    'date_to' => $validated['date_to'] ?? null,
                    'number' => $validated['number'] ?? null,
                    'client_name' => $validated['client_name'] ?? null,
                    'invoice_type' => $validated['invoice_type'] ?? 'all',
                    'is_paid' => $validated['is_paid'] ?? null,
                    'check_all' => (bool) ($validated['check_all'] ?? false),
                ],
            ],
            'total' => $rows->count(),
            'data' => $rows,
        ]);
    }

    public function queueBatchInvoices(Request $request)
    {
        $validated = $request->validate([
            'invoice_ids' => 'required|array|min:1',
            'invoice_ids.*' => 'required|integer|exists:invoices,id',
            'note' => 'nullable|boolean',
            'use_old_letterhead' => 'nullable|boolean',
        ]);

        $invoices = Invoice::with(['client:id,code,name', 'invoiceType:id,code,name'])
            ->whereIn('id', $validated['invoice_ids'])
            ->orderBy('date')
            ->get();

        foreach ($invoices as $invoice) {
            PrintHistory::create([
                'user_id' => $request->user()->id,
                'report_type' => 'invoice_batch',
                'document_number' => $invoice->number,
                'parameters' => [
                    'invoice_id' => $invoice->id,
                    'note' => (bool) ($validated['note'] ?? false),
                    'use_old_letterhead' => (bool) ($validated['use_old_letterhead'] ?? false),
                ],
                'printed_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Batch print queue prepared',
            'data' => [
                'count' => $invoices->count(),
                'note' => (bool) ($validated['note'] ?? false),
                'use_old_letterhead' => (bool) ($validated['use_old_letterhead'] ?? false),
                'invoices' => $invoices->map(function (Invoice $invoice) {
                    return [
                        'invoice_id' => $invoice->id,
                        'number' => $invoice->number,
                        'date' => optional($invoice->date)->format('Y-m-d'),
                        'client_name' => $invoice->client->name ?? null,
                        'invoice_type' => $invoice->invoiceType->name ?? null,
                        'pdf_url' => url('/api/print/invoices/' . $invoice->id . '/pdf'),
                    ];
                })->values(),
            ],
        ]);
    }

    public function invoicePdf(Request $request, Invoice $invoice)
    {
        $invoice->load(['client', 'invoiceType', 'items']);
        $setting = CompanySetting::first();

        PrintHistory::create([
            'user_id' => $request->user()->id,
            'report_type' => 'invoice',
            'document_number' => $invoice->number,
            'parameters' => ['invoice_id' => $invoice->id],
            'printed_at' => now(),
        ]);

        $pdf = Pdf::loadView('print.invoice', compact('invoice', 'setting'));

        return $pdf->stream("invoice-{$invoice->number}.pdf");
    }

    public function receiptPdf(Request $request, PostingPayment $posting_payment)
    {
        $posting_payment->load(['bank', 'client', 'invoices.invoice']);

        PrintHistory::create([
            'user_id' => $request->user()->id,
            'report_type' => 'receipt',
            'document_number' => $posting_payment->number,
            'parameters' => ['posting_payment_id' => $posting_payment->id],
            'printed_at' => now(),
        ]);

        $pdf = Pdf::loadView('print.receipt', ['posting' => $posting_payment]);

        return $pdf->stream("receipt-{$posting_payment->number}.pdf");
    }

    private function applyInvoicePrintFilter(Builder $query, array $validated): void
    {
        if (!empty($validated['date_from'])) {
            $query->whereDate('date', '>=', $validated['date_from']);
        }
        if (!empty($validated['date_to'])) {
            $query->whereDate('date', '<=', $validated['date_to']);
        }
        if (!empty($validated['number'])) {
            $query->where('number', 'like', '%' . $validated['number'] . '%');
        }
        if (!empty($validated['client_name'])) {
            $query->whereHas('client', function (Builder $builder) use ($validated) {
                $builder->where('name', 'like', '%' . $validated['client_name'] . '%');
            });
        }
        if (array_key_exists('is_paid', $validated) && $validated['is_paid'] !== null) {
            $query->where('is_paid', (bool) $validated['is_paid']);
        }
        if (($validated['invoice_type'] ?? 'all') !== 'all') {
            $isLicense = $validated['invoice_type'] === 'license';
            $query->whereHas('invoiceType', function (Builder $builder) use ($isLicense) {
                $builder->where('is_license', $isLicense);
            });
        }
    }
}
