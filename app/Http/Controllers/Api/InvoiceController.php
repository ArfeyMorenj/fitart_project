<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['client', 'invoiceType'])->get();

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->role, ['finance_admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only finance_admin or super_admin can create invoice.',
            ], 403);
        }

        $validated = $request->validate([
            'number' => 'required|unique:invoices,number',
            'invoice_type_id' => 'required|exists:invoice_types,id',
            'client_id' => 'required|exists:clients,id',
            'bank_id' => 'nullable|exists:banks,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'period' => 'nullable|string|size:7',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'tax_date' => 'nullable|date',
            'tax_number' => 'nullable|string',
            'tax_note' => 'nullable|string',
            'invoice_note' => 'nullable|string',
            'client_address' => 'nullable|string',
            'description' => 'nullable|string',
            'invoice_category' => 'nullable|string',
            'invoice_mode' => 'nullable|string|in:NORMAL,DP,PELUNASAN_DP',
            'tax_type' => 'nullable|string',
            'instance' => 'nullable|string',
            'invoice_bm_km' => 'nullable|string|max:50',
            'invoice_bm_km_date' => 'nullable|date',
            'bruto' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'dpp' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'ppn_percentage' => 'nullable|numeric',
            'dp' => 'nullable|numeric',
            'other' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'include_ppn' => 'nullable|boolean',
            'use_old_letterhead' => 'nullable|boolean',
            'without_payment_posting' => 'nullable|boolean',
            'stamp_and_signature' => 'nullable|boolean',
            'auto_journal' => 'nullable|boolean',
            'pass_protelasi' => 'nullable|boolean',
            'is_paid' => 'nullable|boolean',
        ]);

        if ($user->role !== 'super_admin') {
            unset($validated['pass_protelasi']);
        }

        $validated['is_posted'] = false;
        $validated['posted_date'] = null;

        $invoice = Invoice::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully',
            'data' => $invoice->load(['client', 'invoiceType', 'workOrder']),
        ], 201);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'invoiceType', 'workOrder', 'items', 'payments', 'bank']);

        return response()->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        if ((bool) $invoice->is_posted === true && $user->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice already posted. Update is not allowed.',
            ], 409);
        }

        $validated = $request->validate([
            'number' => 'required|unique:invoices,number,' . $invoice->id,
            'invoice_type_id' => 'required|exists:invoice_types,id',
            'client_id' => 'required|exists:clients,id',
            'bank_id' => 'nullable|exists:banks,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'period' => 'nullable|string|size:7',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'tax_date' => 'nullable|date',
            'tax_number' => 'nullable|string',
            'tax_note' => 'nullable|string',
            'invoice_note' => 'nullable|string',
            'client_address' => 'nullable|string',
            'description' => 'nullable|string',
            'invoice_category' => 'nullable|string',
            'invoice_mode' => 'nullable|string|in:NORMAL,DP,PELUNASAN_DP',
            'tax_type' => 'nullable|string',
            'instance' => 'nullable|string',
            'invoice_bm_km' => 'nullable|string|max:50',
            'invoice_bm_km_date' => 'nullable|date',
            'bruto' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'dpp' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'ppn_percentage' => 'nullable|numeric',
            'dp' => 'nullable|numeric',
            'other' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'include_ppn' => 'nullable|boolean',
            'use_old_letterhead' => 'nullable|boolean',
            'without_payment_posting' => 'nullable|boolean',
            'stamp_and_signature' => 'nullable|boolean',
            'auto_journal' => 'nullable|boolean',
            'pass_protelasi' => 'nullable|boolean',
            'is_paid' => 'nullable|boolean',
        ]);

        if ($user->role !== 'super_admin') {
            unset($validated['pass_protelasi']);
        }

        $invoice->update($validated);
        $invoice->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated successfully',
            'data' => $invoice->load(['client', 'invoiceType', 'workOrder']),
            'was_changed' => $invoice->wasChanged(),
            'changes' => $invoice->getChanges(),
        ]);
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        if (((bool) $invoice->is_posted === true || (bool) $invoice->is_paid === true) && $user->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice posted/paid. Delete is not allowed.',
            ], 409);
        }

        $force = $request->query('force');
        if (($force === 'true' || $force === true) && $user->role === 'super_admin') {
            $invoice->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice force-deleted successfully',
            ]);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully',
        ]);
    }

    public function licenseIndex(Request $request)
    {
        return $this->invoiceFlowList($request, true);
    }

    public function productIndex(Request $request)
    {
        return $this->invoiceFlowList($request, false);
    }

    public function licenseSearch(Request $request)
    {
        return $this->invoiceFlowList($request, true, true);
    }

    public function productSearch(Request $request)
    {
        return $this->invoiceFlowList($request, false, true);
    }

    public function licenseShow(Invoice $invoice)
    {
        return $this->flowShow($invoice, true);
    }

    public function productShow(Invoice $invoice)
    {
        return $this->flowShow($invoice, false);
    }

    public function licenseSummaryJournal(Invoice $invoice)
    {
        return $this->summaryJournalResponse($invoice, true);
    }

    public function productSummaryJournal(Invoice $invoice)
    {
        return $this->summaryJournalResponse($invoice, false);
    }

    private function invoiceFlowList(Request $request, bool $isLicense, bool $isSearch = false)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'number' => 'nullable|string|max:50',
            'client_name' => 'nullable|string|max:255',
            'q' => 'nullable|string|max:255',
        ]);

        $query = $this->flowQuery($isLicense);

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

        if (!empty($validated['q'])) {
            $q = $validated['q'];
            $query->where(function (Builder $builder) use ($q) {
                $builder->where('number', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%')
                    ->orWhereHas('client', function (Builder $clientQuery) use ($q) {
                        $clientQuery->where('name', 'like', '%' . $q . '%')
                            ->orWhere('code', 'like', '%' . $q . '%');
                    });
            });
        }

        $rows = $query->orderByDesc('date')->orderByDesc('id')->get()->map(function (Invoice $invoice) use ($isLicense) {
            return $this->transformListRow($invoice, $isLicense);
        })->values();

        return response()->json([
            'success' => true,
            'flow' => $isLicense ? 'invoice_license' : 'invoice_product',
            'mode' => $isSearch ? 'search' : 'list',
            'filter' => [
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
                'number' => $validated['number'] ?? null,
                'client_name' => $validated['client_name'] ?? null,
                'q' => $validated['q'] ?? null,
            ],
            'total' => $rows->count(),
            'data' => $rows,
        ]);
    }

    private function flowShow(Invoice $invoice, bool $isLicense)
    {
        $invoice->load([
            'client',
            'invoiceType',
            'bank',
            'workOrder',
            'items.masterItemProduct',
        ]);

        if (!$this->matchesInvoiceFlow($invoice, $isLicense)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found in this flow.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'flow' => $isLicense ? 'invoice_license' : 'invoice_product',
            'form' => [
                'title' => $isLicense ? 'Entri Invoice [Edit] - INVOICE LICENSE' : 'Entri Invoice Produk [Edit] - INVOICE PRODUK / NON LICENSE',
                'tabs' => $isLicense
                    ? ['Detail', 'List / Daftar', 'Summary Jurnal']
                    : ['Detail', 'List', 'Summary Jurnal'],
                'sub_tabs' => $isLicense
                    ? ['Detail Item', 'Jurnal']
                    : ['Detail Item', 'Pajak', 'Jurnal'],
            ],
            'data' => $this->transformDetail($invoice, $isLicense),
        ]);
    }

    private function summaryJournalResponse(Invoice $invoice, bool $isLicense)
    {
        $invoice->load(['invoiceType']);

        if (!$this->matchesInvoiceFlow($invoice, $isLicense)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found in this flow.',
            ], 404);
        }

        $summary = $this->transformSummaryJournal($invoice);

        return response()->json([
            'success' => true,
            'flow' => $isLicense ? 'invoice_license' : 'invoice_product',
            'invoice_number' => $invoice->number,
            'total_debit' => $summary['total_debit'],
            'total_credit' => $summary['total_credit'],
            'data' => $summary['rows'],
        ]);
    }

    private function flowQuery(bool $isLicense): Builder
    {
        return Invoice::query()
            ->with(['client:id,code,name', 'invoiceType:id,code,name,is_license'])
            ->where(function (Builder $builder) use ($isLicense) {
                $builder->whereHas('invoiceType', function (Builder $invoiceTypeQuery) use ($isLicense) {
                    $invoiceTypeQuery->where('is_license', $isLicense);
                });

                if ($isLicense) {
                    $builder->orWhere(function (Builder $categoryQuery) {
                        $categoryQuery->whereNotNull('invoice_category')
                            ->whereRaw('UPPER(invoice_category) = ?', ['LICENSE']);
                    });
                } else {
                    $builder->orWhere(function (Builder $categoryQuery) {
                        $categoryQuery->whereNotNull('invoice_category')
                            ->whereRaw('UPPER(invoice_category) IN (?, ?, ?, ?)', ['NON_LICENSE', 'NON-LICENSE', 'PRODUK', 'PRODUCT']);
                    });
                }
            });
    }

    private function matchesInvoiceFlow(Invoice $invoice, bool $isLicense): bool
    {
        if ($invoice->invoiceType) {
            return (bool) $invoice->invoiceType->is_license === $isLicense;
        }

        $category = strtoupper((string) $invoice->invoice_category);

        if ($isLicense) {
            return $category === 'LICENSE';
        }

        return in_array($category, ['NON_LICENSE', 'NON-LICENSE', 'PRODUK'], true);
    }

    private function transformListRow(Invoice $invoice, bool $isLicense): array
    {
        if ($isLicense) {
            return [
                'invoice_id' => $invoice->id,
                'number' => $invoice->number,
                'date' => optional($invoice->date)->format('Y-m-d'),
                'description' => $invoice->description,
                'client' => $invoice->client->name ?? null,
                'jumlah' => (float) $invoice->dpp,
                'ppn' => (float) $invoice->ppn,
                'total' => (float) $invoice->total,
                'invoice_type_code' => $invoice->invoiceType->code ?? null,
                'invoice_type_name' => $invoice->invoiceType->name ?? null,
            ];
        }

        return [
            'invoice_id' => $invoice->id,
            'number' => $invoice->number,
            'date' => optional($invoice->date)->format('Y-m-d'),
            'description' => $invoice->description,
            'client' => $invoice->client->name ?? null,
            'netto' => (float) $invoice->dpp,
            'no_bm_km' => $invoice->invoice_bm_km,
            'tgl_bm_km' => optional($invoice->posted_date)->format('Y-m-d'),
            'invoice_type_code' => $invoice->invoiceType->code ?? null,
            'invoice_type_name' => $invoice->invoiceType->name ?? null,
        ];
    }

    private function transformDetail(Invoice $invoice, bool $isLicense): array
    {
        $summary = $this->transformSummaryJournal($invoice);

        return [
            'header' => [
                'invoice_id' => $invoice->id,
                'jenis_invoice_code' => $invoice->invoiceType->code ?? null,
                'jenis_invoice_name' => $invoice->invoiceType->name ?? null,
                'number' => $invoice->number,
                'date' => optional($invoice->date)->format('Y-m-d'),
                'client_code' => $invoice->client->code ?? null,
                'client_name' => $invoice->client->name ?? null,
                'address' => $invoice->client_address ?: ($invoice->client->address ?? null),
                'due_date' => optional($invoice->due_date)->format('Y-m-d'),
                'description' => $invoice->description,
                'jenis_faktur' => $invoice->tax_type,
                'instansi' => $invoice->instance,
                'no_f_pajak' => $invoice->tax_number,
                'tgl_f_pajak' => optional($invoice->tax_date)->format('Y-m-d'),
                'note_faktur_pajak' => $invoice->tax_note,
                'note_invoice_nota' => $invoice->invoice_note,
                'no_bm_km' => $invoice->invoice_bm_km,
                'tgl_bm_km' => optional($invoice->invoice_bm_km_date)->format('Y-m-d'),
                'invoice_mode' => $invoice->invoice_mode,
                'jumlah' => (float) $invoice->bruto,
                'discount' => (float) $invoice->discount,
                'dpp' => (float) $invoice->dpp,
                'ppn_percentage' => (float) $invoice->ppn_percentage,
                'ppn' => (float) $invoice->ppn,
                'dp' => (float) $invoice->dp,
                'other' => (float) $invoice->other,
                'total' => (float) $invoice->total,
                'include_ppn' => (bool) $invoice->include_ppn,
                'auto_journal' => (bool) $invoice->auto_journal,
                'use_old_letterhead' => (bool) $invoice->use_old_letterhead,
                'without_payment_posting' => (bool) $invoice->without_payment_posting,
                'stamp_and_signature' => (bool) $invoice->stamp_and_signature,
                'is_paid' => (bool) $invoice->is_paid,
                'is_posted' => (bool) $invoice->is_posted,
            ],
            'detail_items' => $invoice->items->map(function ($item) {
                return [
                    'invoice_item_id' => $item->id,
                    'kd_item' => $item->item_code,
                    'nama_item' => $item->item_name,
                    'keterangan' => $item->description,
                    'qty' => (int) $item->qty,
                    'satuan' => $item->unit,
                    'harga' => (float) $item->price,
                    'bruto' => (float) $item->bruto,
                    'bulan' => $item->months,
                ];
            })->values(),
            'pajak' => [
                'no_faktur_pajak' => $invoice->tax_number,
                'tgl_faktur_pajak' => optional($invoice->tax_date)->format('Y-m-d'),
                'ppn' => (float) $invoice->ppn,
                'ppn_percentage' => (float) $invoice->ppn_percentage,
            ],
            'summary_jurnal' => $summary['rows'],
            'summary_jurnal_totals' => [
                'debet' => $summary['total_debit'],
                'kredit' => $summary['total_credit'],
            ],
            'flow_hint' => $isLicense ? 'license' : 'non_license',
        ];
    }

    private function transformSummaryJournal(Invoice $invoice): array
    {
        $journals = Journal::query()
            ->where('document_number', $invoice->number)
            ->orderBy('date')
            ->orderBy('sequence')
            ->orderBy('id')
            ->get();

        $rows = $journals->map(function (Journal $journal) {
            return [
                'no_bukti' => $journal->document_number,
                'tgl' => optional($journal->date)->format('Y-m-d'),
                'seq' => $journal->sequence,
                'kode_acc' => $journal->acc_code,
                'keterangan' => $journal->description,
                'debet' => (float) $journal->debit,
                'kredit' => (float) $journal->credit,
            ];
        })->values();

        return [
            'rows' => $rows,
            'total_debit' => (float) $journals->sum('debit'),
            'total_credit' => (float) $journals->sum('credit'),
        ];
    }
}
