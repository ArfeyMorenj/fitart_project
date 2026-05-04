<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostingOmzetController extends Controller
{
    public function postInvoiceRevenue(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->role, ['finance_admin', 'super_admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $invoice = Invoice::with(['items.masterItemProduct'])->findOrFail($validated['invoice_id']);

        if ((bool)$invoice->is_posted === true) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice already posted',
                'invoice_number' => $invoice->number,
            ], 409);
        }

        // Tentukan akun omzet & piutang dari invoice item mapping
        $firstItem = $invoice->items->first();
        $accOmzet = $firstItem?->masterItemProduct?->acc_omzet;
        $accPiutang = $firstItem?->masterItemProduct?->acc_piutang;

        if (!$accOmzet || !$accPiutang) {
            // fallback dari items table berdasarkan item_code
            if ($firstItem?->item_code) {
                $item = Item::where('code', $firstItem->item_code)->first();
                $accOmzet = $accOmzet ?: ($item?->acc_omzet);
                $accPiutang = $accPiutang ?: ($item?->acc_piutang);
            }
        }

        if (!$accOmzet || !$accPiutang) {
            return response()->json([
                'success' => false,
                'message' => 'Account mapping missing. Set acc_omzet & acc_piutang di master_item_products atau items.',
                'hint' => [
                    'invoice_id' => $invoice->id,
                    'item_code' => $firstItem?->item_code,
                ],
            ], 422);
        }

        $setting = CompanySetting::first();
        $accPpnKeluaran = $setting?->acc_ppn_kes; // output VAT

        $debitAR = (float)$invoice->total;
        $creditRevenue = (float)$invoice->dpp + (float)$invoice->other;
        $creditVat = (float)$invoice->ppn;

        if (round($debitAR, 2) !== round($creditRevenue + $creditVat, 2)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not balanced (total != dpp+other+ppn). Fix invoice calculation first.',
                'calc' => [
                    'total' => $debitAR,
                    'dpp_plus_other_plus_ppn' => $creditRevenue + $creditVat,
                ]
            ], 422);
        }

        $journals = DB::transaction(function () use ($invoice, $accPiutang, $accOmzet, $accPpnKeluaran, $debitAR, $creditRevenue, $creditVat) {

            $rows = [];
            $journalSequence = 1;

            // Dr Piutang
            $rows[] = Journal::create([
                'document_number' => $invoice->number,
                'date' => $invoice->date,
                'sequence' => str_pad((string) $journalSequence++, 3, '0', STR_PAD_LEFT),
                'acc_code' => $accPiutang,
                'description' => 'Posting invoice (AR) ' . $invoice->number,
                'debit' => $debitAR,
                'credit' => 0,
                'document_type' => 'invoice',
                'reference_id' => $invoice->id,
            ]);

            // Cr Omzet
            $rows[] = Journal::create([
                'document_number' => $invoice->number,
                'date' => $invoice->date,
                'sequence' => str_pad((string) $journalSequence++, 3, '0', STR_PAD_LEFT),
                'acc_code' => $accOmzet,
                'description' => 'Posting invoice (Revenue) ' . $invoice->number,
                'debit' => 0,
                'credit' => $creditRevenue,
                'document_type' => 'invoice',
                'reference_id' => $invoice->id,
            ]);

            // Cr PPN Keluaran
            if ($creditVat > 0 && $accPpnKeluaran) {
                $rows[] = Journal::create([
                    'document_number' => $invoice->number,
                    'date' => $invoice->date,
                    'sequence' => str_pad((string) $journalSequence++, 3, '0', STR_PAD_LEFT),
                    'acc_code' => $accPpnKeluaran,
                    'description' => 'Posting invoice (VAT Output) ' . $invoice->number,
                    'debit' => 0,
                    'credit' => $creditVat,
                    'document_type' => 'invoice',
                    'reference_id' => $invoice->id,
                ]);
            }

            $invoice->update([
                'is_posted' => true,
                'posted_date' => now()->toDateString(),
            ]);

            return $rows;
        });

        return response()->json([
            'success' => true,
            'message' => 'Invoice revenue posted successfully',
            'invoice' => $invoice->fresh(),
            'journals' => $journals,
        ]);
    }
}
