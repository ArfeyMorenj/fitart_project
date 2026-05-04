<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostingPayment;
use App\Models\PostingPaymentCost;
use Illuminate\Http\Request;

class PostingPaymentCostController extends Controller
{
    public function index(PostingPayment $postingPayment)
    {
        return response()->json([
            'success' => true,
            'data' => $postingPayment->costs()->orderBy('id')->get(),
            'total_cost' => (float) $postingPayment->costs()->sum('amount'),
        ]);
    }

    public function store(Request $request, PostingPayment $postingPayment)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $cost = $postingPayment->costs()->create($validated);
        $this->syncPostingTotals($postingPayment);

        return response()->json([
            'success' => true,
            'message' => 'Posting payment cost created successfully',
            'data' => $cost,
            'totals' => $this->currentTotals($postingPayment),
        ], 201);
    }

    public function update(Request $request, PostingPayment $postingPayment, PostingPaymentCost $cost)
    {
        if ($cost->posting_payment_id !== $postingPayment->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cost does not belong to this posting payment.',
            ], 422);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $cost->update($validated);
        $this->syncPostingTotals($postingPayment);

        return response()->json([
            'success' => true,
            'message' => 'Posting payment cost updated successfully',
            'data' => $cost->fresh(),
            'totals' => $this->currentTotals($postingPayment),
        ]);
    }

    public function destroy(PostingPayment $postingPayment, PostingPaymentCost $cost)
    {
        if ($cost->posting_payment_id !== $postingPayment->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cost does not belong to this posting payment.',
            ], 422);
        }

        $cost->delete();
        $this->syncPostingTotals($postingPayment);

        return response()->json([
            'success' => true,
            'message' => 'Posting payment cost deleted successfully',
            'totals' => $this->currentTotals($postingPayment),
        ]);
    }

    private function syncPostingTotals(PostingPayment $postingPayment): void
    {
        $postingPayment->refresh();
        $totalCost = (float) $postingPayment->costs()->sum('amount');
        $newTotalPaid = max(0, (float) $postingPayment->debet - $totalCost);

        $postingPayment->update([
            'kredit' => $totalCost,
            'total_paid' => $newTotalPaid,
        ]);
    }

    private function currentTotals(PostingPayment $postingPayment): array
    {
        $postingPayment->refresh();

        return [
            'total_invoice' => (float) $postingPayment->total_invoice,
            'debet' => (float) $postingPayment->debet,
            'kredit' => (float) $postingPayment->kredit,
            'total_paid' => (float) $postingPayment->total_paid,
        ];
    }
}
