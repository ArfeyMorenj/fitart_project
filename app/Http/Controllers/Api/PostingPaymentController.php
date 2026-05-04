<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\PostingPayment;
use App\Models\PostingPaymentCost;
use App\Models\PostingPaymentInvoice;
use App\Models\Receivable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostingPaymentController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'invoice_date_from' => 'nullable|date',
            'invoice_date_to' => 'nullable|date',
            'posting_date_from' => 'nullable|date',
            'posting_date_to' => 'nullable|date',
            'number' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'q' => 'nullable|string|max:255',
        ]);

        $query = $this->baseQuery();

        if (!empty($validated['posting_date_from'])) {
            $query->whereDate('date', '>=', $validated['posting_date_from']);
        }
        if (!empty($validated['posting_date_to'])) {
            $query->whereDate('date', '<=', $validated['posting_date_to']);
        }
        if (!empty($validated['invoice_date_from'])) {
            $query->whereHas('invoices.invoice', function (Builder $builder) use ($validated) {
                $builder->whereDate('date', '>=', $validated['invoice_date_from']);
            });
        }
        if (!empty($validated['invoice_date_to'])) {
            $query->whereHas('invoices.invoice', function (Builder $builder) use ($validated) {
                $builder->whereDate('date', '<=', $validated['invoice_date_to']);
            });
        }
        if (!empty($validated['number'])) {
            $query->where('number', 'like', '%' . $validated['number'] . '%');
        }
        if (!empty($validated['description'])) {
            $query->where('description', 'like', '%' . $validated['description'] . '%');
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

        $rows = $query->orderByDesc('date')->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'filter' => $validated,
            'total' => $rows->count(),
            'data' => $rows->map(fn (PostingPayment $posting) => $this->transformListRow($posting))->values(),
        ]);
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function show(PostingPayment $postingPayment)
    {
        $postingPayment->load([
            'bank',
            'client',
            'invoices.invoice.client',
            'costs',
        ]);

        return response()->json([
            'success' => true,
            'form' => [
                'title' => 'Posting Invoice [Edit] - POSTING PEMBAYARAN INVOICE',
                'tabs' => ['Detail', 'List'],
                'sub_tabs' => ['Detail Invoice', 'Detail Biaya', 'Jurnal'],
            ],
            'data' => $this->transformDetail($postingPayment),
        ]);
    }

    public function summaryJournal(PostingPayment $postingPayment)
    {
        $journals = Journal::query()
            ->where('document_number', $postingPayment->number)
            ->orderBy('date')
            ->orderBy('sequence')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'posting_number' => $postingPayment->number,
            'total_debit' => (float) $journals->sum('debit'),
            'total_credit' => (float) $journals->sum('credit'),
            'data' => $journals->map(function (Journal $journal) {
                return [
                    'no_bukti' => $journal->document_number,
                    'tanggal' => optional($journal->date)->format('Y-m-d'),
                    'acc' => $journal->acc_code,
                    'keterangan' => $journal->description,
                    'debet' => (float) $journal->debit,
                    'kredit' => (float) $journal->credit,
                ];
            })->values(),
        ]);
    }

    public function postBatch(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->role, ['finance_admin', 'ar_collector', 'super_admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'number' => 'nullable|string|max:50|unique:posting_payments,number',
            'date' => 'required|date',
            'bank_id' => 'required|exists:banks,id',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'print_receipt' => 'nullable|boolean',
            'auto_journal' => 'nullable|boolean',
            'without_stamp' => 'nullable|boolean',
            'invoices' => 'required|array|min:1',
            'invoices.*.invoice_id' => 'required|exists:invoices,id',
            'invoices.*.amount' => 'required|numeric|min:0',
            'invoices.*.ppn' => 'nullable|numeric|min:0',
            'costs' => 'nullable|array',
            'costs.*.description' => 'required_with:costs|string|max:255',
            'costs.*.amount' => 'required_with:costs|numeric|min:0',
        ]);

        $bank = Bank::findOrFail($validated['bank_id']);
        if (!$bank->acc_code) {
            return response()->json([
                'success' => false,
                'message' => 'Bank acc_code is required for journal posting.',
            ], 422);
        }

        $number = $validated['number'] ?? ('BP-' . now()->format('Ym') . '-' . str_pad((PostingPayment::withTrashed()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT));
        $posting = DB::transaction(function () use ($validated, $bank, $number) {
            $posting = PostingPayment::create([
                'number' => $number,
                'date' => $validated['date'],
                'bank_id' => $validated['bank_id'],
                'acc_code' => $bank->acc_code,
                'cdf_code' => $bank->cdf_code,
                'client_id' => $validated['client_id'],
                'description' => $validated['description'] ?? null,
                'print_receipt' => (bool) ($validated['print_receipt'] ?? false),
                'auto_journal' => (bool) ($validated['auto_journal'] ?? true),
                'without_stamp' => (bool) ($validated['without_stamp'] ?? false),
                'is_posted' => false,
            ]);

            $totalPaid = 0;
            $totalInvoice = 0;
            $journalSequence = 1;

            foreach ($validated['invoices'] as $row) {
                $invoice = Invoice::with(['items.masterItemProduct'])->findOrFail($row['invoice_id']);

                $ppn = (float) ($row['ppn'] ?? 0);
                $amount = (float) $row['amount'];
                $paid = $amount + $ppn;

                $totalPaid += $paid;
                $totalInvoice += (float) $invoice->total;

                PostingPaymentInvoice::create([
                    'posting_payment_id' => $posting->id,
                    'invoice_id' => $invoice->id,
                    'client_id' => $validated['client_id'],
                    'amount' => $amount,
                    'ppn' => $ppn,
                ]);

                Payment::create([
                    'number' => $posting->number . '-' . str_pad((Payment::withTrashed()->max('id') ?? 0) + 1, 3, '0', STR_PAD_LEFT),
                    'date' => $validated['date'],
                    'invoice_id' => $invoice->id,
                    'client_id' => $validated['client_id'],
                    'description' => 'Auto from posting ' . $posting->number,
                    'amount' => $paid,
                    'is_posted' => true,
                ]);

                $firstItem = $invoice->items->first();
                $accPiutang = $firstItem?->masterItemProduct?->acc_piutang;

                if (!$accPiutang && $firstItem?->item_code) {
                    $item = Item::where('code', $firstItem->item_code)->first();
                    $accPiutang = $item?->acc_piutang;
                }
                if (!$accPiutang) {
                    throw new \RuntimeException("Missing acc_piutang mapping for invoice {$invoice->number}");
                }

                Journal::create([
                    'document_number' => $posting->number,
                    'date' => $validated['date'],
                    'sequence' => str_pad((string) $journalSequence++, 3, '0', STR_PAD_LEFT),
                    'acc_code' => $accPiutang,
                    'description' => 'Receipt posting (AR) inv ' . $invoice->number,
                    'debit' => 0,
                    'credit' => $paid,
                    'document_type' => 'posting_payment',
                    'reference_id' => $posting->id,
                ]);

                $period = date('Y-m', strtotime($validated['date']));
                $recv = Receivable::where('period', $period)->where('invoice_id', $invoice->id)->first();
                if ($recv) {
                    $recv->payment = (float) $recv->payment + $paid;
                    $recv->ending_balance = max(0, (float) $recv->ending_balance - $paid);
                    $recv->status = $recv->ending_balance <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                    $recv->save();
                }

                $paidSum = Payment::where('invoice_id', $invoice->id)->sum('amount');
                if ($paidSum >= (float) $invoice->total) {
                    $invoice->update(['is_paid' => true]);
                }
            }

            Journal::create([
                'document_number' => $posting->number,
                'date' => $validated['date'],
                'sequence' => str_pad((string) $journalSequence++, 3, '0', STR_PAD_LEFT),
                'acc_code' => $bank->acc_code,
                'description' => 'Receipt posting (Bank) ' . $posting->number,
                'debit' => $totalPaid,
                'credit' => 0,
                'document_type' => 'posting_payment',
                'reference_id' => $posting->id,
            ]);

            $totalCost = 0;
            foreach (($validated['costs'] ?? []) as $costRow) {
                PostingPaymentCost::create([
                    'posting_payment_id' => $posting->id,
                    'description' => $costRow['description'],
                    'amount' => (float) $costRow['amount'],
                ]);
                $totalCost += (float) $costRow['amount'];
            }

            $posting->update([
                'total_invoice' => $totalInvoice,
                'debet' => $totalPaid,
                'kredit' => $totalCost,
                'total_paid' => max(0, $totalPaid - $totalCost),
                'is_posted' => true,
            ]);

            return $posting->fresh()->load([
                'bank',
                'client',
                'invoices.invoice.client',
                'costs',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Posting payment batch success',
            'data' => $this->transformDetail($posting),
        ]);
    }

    private function baseQuery(): Builder
    {
        return PostingPayment::query()->with([
            'bank:id,code,name',
            'client:id,code,name',
            'invoices.invoice:id,number,date,total,ppn,client_id',
        ]);
    }

    private function transformListRow(PostingPayment $posting): array
    {
        return [
            'posting_id' => $posting->id,
            'number' => $posting->number,
            'posting_date' => optional($posting->date)->format('Y-m-d'),
            'description' => $posting->description,
            'client_name' => $posting->client->name ?? null,
            'total_invoice' => (float) $posting->total_invoice,
            'debet' => (float) $posting->debet,
            'kredit' => (float) $posting->kredit,
            'total_bayar' => (float) $posting->total_paid,
        ];
    }

    private function transformDetail(PostingPayment $posting): array
    {
        $journals = Journal::query()
            ->where('document_number', $posting->number)
            ->orderBy('date')
            ->orderBy('sequence')
            ->orderBy('id')
            ->get()
            ->map(function (Journal $journal) {
                return [
                    'no_bukti' => $journal->document_number,
                    'tanggal' => optional($journal->date)->format('Y-m-d'),
                    'acc' => $journal->acc_code,
                    'keterangan' => $journal->description,
                    'debet' => (float) $journal->debit,
                    'kredit' => (float) $journal->credit,
                ];
            })->values();

        return [
            'header' => [
                'id' => $posting->id,
                'bank_code' => $posting->bank->code ?? null,
                'bank_name' => $posting->bank->name ?? null,
                'acc_code' => $posting->acc_code,
                'cdf_code' => $posting->cdf_code,
                'date' => optional($posting->date)->format('Y-m-d'),
                'number' => $posting->number,
                'description' => $posting->description,
                'client_code' => $posting->client->code ?? null,
                'client_name' => $posting->client->name ?? null,
                'total_invoice' => (float) $posting->total_invoice,
                'debet' => (float) $posting->debet,
                'kredit' => (float) $posting->kredit,
                'total_bayar' => (float) $posting->total_paid,
                'cetak_kwitansi' => (bool) $posting->print_receipt,
                'jurnal_otomatis' => (bool) $posting->auto_journal,
                'tt_dan_stempel' => !$posting->without_stamp,
                'is_posted' => (bool) $posting->is_posted,
            ],
            'detail_invoice' => $posting->invoices->map(function (PostingPaymentInvoice $row) {
                return [
                    'id' => $row->id,
                    'no_invoice' => $row->invoice->number ?? null,
                    'tgl_invoice' => optional($row->invoice->date)->format('Y-m-d'),
                    'klien' => $row->client->name ?? ($row->invoice->client->name ?? null),
                    'nilai' => (float) $row->amount,
                    'ppn' => (float) $row->ppn,
                    'total' => (float) $row->amount + (float) $row->ppn,
                ];
            })->values(),
            'detail_biaya' => $posting->costs->map(function (PostingPaymentCost $cost) {
                return [
                    'id' => $cost->id,
                    'description' => $cost->description,
                    'amount' => (float) $cost->amount,
                ];
            })->values(),
            'jurnal' => $journals,
        ];
    }
}
