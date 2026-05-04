<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DebitCreditNote;
use App\Models\DebitCreditNoteItem;
use App\Models\Invoice;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebitCreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'number' => 'nullable|string|max:50',
            'client_name' => 'nullable|string|max:255',
            'q' => 'nullable|string|max:255',
        ]);

        $query = $this->baseQuery();

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

        $notes = $query->orderByDesc('date')->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'filter' => [
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
                'number' => $validated['number'] ?? null,
                'client_name' => $validated['client_name'] ?? null,
                'q' => $validated['q'] ?? null,
            ],
            'total' => $notes->count(),
            'data' => $notes->map(fn (DebitCreditNote $note) => $this->transformListRow($note))->values(),
        ]);
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $note = DB::transaction(function () use ($validated) {
            $invoice = !empty($validated['invoice_id']) ? Invoice::find($validated['invoice_id']) : null;

            $payload = $this->prepareNotePayload($validated, $invoice);
            $items = $validated['items'] ?? [];

            $note = DebitCreditNote::create($payload);
            $this->syncItems($note, $items);

            return $note->fresh()->load(['invoice', 'client', 'items']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Debit/Credit Note created successfully',
            'data' => $this->transformDetail($note),
        ], 201);
    }

    public function show(DebitCreditNote $debitCreditNote)
    {
        $debitCreditNote->load(['invoice', 'client', 'items']);

        return response()->json([
            'success' => true,
            'data' => $this->transformDetail($debitCreditNote),
        ]);
    }

    public function update(Request $request, DebitCreditNote $debitCreditNote)
    {
        $validated = $this->validatePayload($request, $debitCreditNote);

        $note = DB::transaction(function () use ($validated, $debitCreditNote) {
            $invoice = !empty($validated['invoice_id']) ? Invoice::find($validated['invoice_id']) : $debitCreditNote->invoice;

            $payload = $this->prepareNotePayload($validated, $invoice);
            $items = $validated['items'] ?? [];

            $debitCreditNote->update($payload);
            $this->syncItems($debitCreditNote, $items);

            return $debitCreditNote->fresh()->load(['invoice', 'client', 'items']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Debit/Credit Note updated successfully',
            'data' => $this->transformDetail($note),
        ]);
    }

    public function destroy(Request $request, DebitCreditNote $debitCreditNote)
    {
        $force = $request->query('force');

        if ($force === 'true' || $force === true) {
            $debitCreditNote->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Debit/Credit Note force-deleted successfully',
                'deleted' => true,
            ]);
        }

        $debitCreditNote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Debit/Credit Note deleted successfully',
        ]);
    }

    public function summaryJournal(DebitCreditNote $debitCreditNote)
    {
        $journals = Journal::query()
            ->where('document_number', $debitCreditNote->number)
            ->orderBy('date')
            ->orderBy('sequence')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'note_number' => $debitCreditNote->number,
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

    private function baseQuery(): Builder
    {
        return DebitCreditNote::query()->with([
            'invoice:id,number',
            'client:id,code,name',
            'items:id,debit_credit_note_id,item_code,item_name,description,dpp_amount,ppn_amount',
        ]);
    }

    private function validatePayload(Request $request, ?DebitCreditNote $note = null): array
    {
        $numberRule = 'required|unique:debit_credit_notes,number';
        if ($note) {
            $numberRule .= ',' . $note->id;
        }

        return $request->validate([
            'type' => 'required|string|in:D,C',
            'number' => $numberRule,
            'date' => 'required|date',
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'auto_journal' => 'nullable|boolean',
            'is_posted' => 'nullable|boolean',
            'dpp_amount' => 'nullable|numeric|min:0',
            'ppn_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer|exists:debit_credit_note_items,id',
            'items.*.sequence' => 'nullable|integer|min:1',
            'items.*.item_code' => 'nullable|string|max:20',
            'items.*.item_name' => 'required_with:items|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.dpp_amount' => 'required_with:items|numeric|min:0',
            'items.*.ppn_amount' => 'nullable|numeric|min:0',
        ]);
    }

    private function prepareNotePayload(array $validated, ?Invoice $invoice): array
    {
        if (empty($validated['client_id']) && $invoice) {
            $validated['client_id'] = $invoice->client_id;
        }

        $dpp = array_key_exists('dpp_amount', $validated) ? (float) $validated['dpp_amount'] : null;
        $ppn = array_key_exists('ppn_amount', $validated) ? (float) $validated['ppn_amount'] : null;

        if (isset($validated['items'])) {
            $dpp = collect($validated['items'])->sum(fn ($row) => (float) ($row['dpp_amount'] ?? 0));
            $ppn = collect($validated['items'])->sum(fn ($row) => (float) ($row['ppn_amount'] ?? 0));
        }

        $validated['dpp_amount'] = $dpp ?? 0;
        $validated['ppn_amount'] = $ppn ?? 0;
        $validated['total_amount'] = array_key_exists('total_amount', $validated)
            ? (float) $validated['total_amount']
            : ((float) $validated['dpp_amount'] + (float) $validated['ppn_amount']);

        $validated['auto_journal'] = (bool) ($validated['auto_journal'] ?? true);
        $validated['is_posted'] = (bool) ($validated['is_posted'] ?? false);

        unset($validated['items']);

        return $validated;
    }

    private function syncItems(DebitCreditNote $note, array $items): void
    {
        if ($items === []) {
            $note->items()->delete();
            return;
        }

        $keepIds = [];
        foreach ($items as $index => $item) {
            $payload = [
                'item_code' => $item['item_code'] ?? null,
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'dpp_amount' => (float) $item['dpp_amount'],
                'ppn_amount' => (float) ($item['ppn_amount'] ?? 0),
            ];

            if (!empty($item['id'])) {
                $row = $note->items()->where('id', $item['id'])->first();
                if ($row) {
                    $row->update($payload);
                    $keepIds[] = $row->id;
                }
            } else {
                $row = $note->items()->create($payload);
                $keepIds[] = $row->id;
            }
        }

        $note->items()->whereNotIn('id', $keepIds)->delete();
    }

    private function transformListRow(DebitCreditNote $note): array
    {
        return [
            'note_id' => $note->id,
            'type' => $note->type,
            'number' => $note->number,
            'date' => optional($note->date)->format('Y-m-d'),
            'client_code' => $note->client->code ?? null,
            'client_name' => $note->client->name ?? null,
            'description' => $note->description,
            'nominal_dpp' => (float) $note->dpp_amount,
            'nominal_ppn' => (float) $note->ppn_amount,
        ];
    }

    private function transformDetail(DebitCreditNote $note): array
    {
        return [
            'header' => [
                'id' => $note->id,
                'jenis' => $note->type,
                'number' => $note->number,
                'date' => optional($note->date)->format('Y-m-d'),
                'client_code' => $note->client->code ?? null,
                'client_name' => $note->client->name ?? null,
                'description' => $note->description,
                'auto_journal' => (bool) $note->auto_journal,
                'detail_invoice' => $note->invoice->number ?? null,
                'total_dpp' => (float) $note->dpp_amount,
                'total_ppn' => (float) $note->ppn_amount,
                'total_amount' => (float) $note->total_amount,
                'is_posted' => (bool) $note->is_posted,
            ],
            'items' => $note->items->values()->map(function (DebitCreditNoteItem $item, int $index) {
                return [
                    'id' => $item->id,
                    'sequence' => $index + 1,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'dpp_nota' => (float) $item->dpp_amount,
                    'ppn_nota' => (float) $item->ppn_amount,
                ];
            }),
        ];
    }
}
