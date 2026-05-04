<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index()
    {
        $receivables = Receivable::with(['client', 'invoice'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $receivables->map(function (Receivable $row) {
                return [
                    'id' => $row->id,
                    'period' => $row->period,
                    'invoice_number' => $row->invoice->number ?? null,
                    'client_code' => $row->client_code,
                    'client_name' => $row->client_name,
                    'beginning_balance' => (float) $row->beginning_balance,
                    'netto' => (float) $row->netto,
                    'ppn' => (float) $row->ppn,
                    'biaya' => (float) $row->biaya,
                    'nota_debet' => (float) $row->nota_debet,
                    'payment' => (float) $row->payment,
                    'ending_balance' => (float) $row->ending_balance,
                    'status' => $row->status,
                ];
            })->values()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|string|max:7',
            'invoice_id' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'client_code' => 'required|string|max:20',
            'client_name' => 'required|string',
            'beginning_balance' => 'nullable|numeric',
            'netto' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'biaya' => 'nullable|numeric',
            'nota_debet' => 'nullable|numeric',
            'payment' => 'nullable|numeric',
            'ending_balance' => 'nullable|numeric',
            'status' => 'nullable|string|max:20',
        ]);

        $receivable = Receivable::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Receivable created successfully',
            'data' => $receivable->load(['client', 'invoice'])
        ], 201);
    }

    public function show(Receivable $receivable)
    {
        $receivable->load(['client', 'invoice']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $receivable->id,
                'period' => $receivable->period,
                'invoice_number' => $receivable->invoice->number ?? null,
                'client_code' => $receivable->client_code,
                'client_name' => $receivable->client_name,
                'beginning_balance' => (float) $receivable->beginning_balance,
                'netto' => (float) $receivable->netto,
                'ppn' => (float) $receivable->ppn,
                'biaya' => (float) $receivable->biaya,
                'nota_debet' => (float) $receivable->nota_debet,
                'payment' => (float) $receivable->payment,
                'ending_balance' => (float) $receivable->ending_balance,
                'status' => $receivable->status,
            ]
        ]);
    }

    public function update(Request $request, Receivable $receivable)
    {
        $validated = $request->validate([
            'period' => 'required|string|max:7',
            'invoice_id' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'client_code' => 'required|string|max:20',
            'client_name' => 'required|string',
            'beginning_balance' => 'nullable|numeric',
            'netto' => 'nullable|numeric',
            'ppn' => 'nullable|numeric',
            'biaya' => 'nullable|numeric',
            'nota_debet' => 'nullable|numeric',
            'payment' => 'nullable|numeric',
            'ending_balance' => 'nullable|numeric',
            'status' => 'nullable|string|max:20',
        ]);

        $receivable->update($validated);
        $receivable->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Receivable updated successfully',
            'data' => $receivable->load(['client', 'invoice'])
        ]);
    }

    public function destroy(Receivable $receivable)
    {
        $receivable->forcedelete();

        return response()->json([
            'success' => true,
            'message' => 'Receivable deleted successfully',
            'deleted' => true
        ]);
    }

    /**
     * Process/batch create receivables for a given period
     * Calculate outstanding balance from invoices and payments
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|string|size:7'
        ]);

        $period = $validated['period'];

        // Get all posted invoices for this period
        $invoices = \App\Models\Invoice::where('period', $period)
            ->where('is_posted', true)
            ->with(['client', 'payments'])
            ->get();

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($invoices as $invoice) {
            try {
                // Calculate total amount paid for this invoice
                $totalPaid = $invoice->payments->sum('amount');

                // Calculate outstanding balance
                $outstandingBalance = ($invoice->dpp + $invoice->ppn) - $totalPaid;

                // Only create receivable if there's outstanding balance
                if ($outstandingBalance > 0) {
                    // Check existing receivable
                    $existing = Receivable::where('period', $period)
                        ->where('invoice_id', $invoice->id)
                        ->first();

                    if ($existing) {
                        // Update existing
                        $existing->update([
                            'client_id' => $invoice->client_id,
                            'client_code' => $invoice->client->code ?? 'N/A',
                            'client_name' => $invoice->client->name,
                            'netto' => $invoice->dpp,
                            'ppn' => $invoice->ppn,
                            'payment' => $totalPaid,
                            'ending_balance' => $outstandingBalance,
                            'status' => $outstandingBalance > 0 ? 'outstanding' : 'paid'
                        ]);
                        $updated++;
                    } else {
                        // Create new
                        Receivable::create([
                            'period' => $period,
                            'invoice_id' => $invoice->id,
                            'client_id' => $invoice->client_id,
                            'client_code' => $invoice->client->code ?? 'N/A',
                            'client_name' => $invoice->client->name,
                            'netto' => $invoice->dpp,
                            'ppn' => $invoice->ppn,
                            'payment' => $totalPaid,
                            'ending_balance' => $outstandingBalance,
                            'status' => 'outstanding'
                        ]);
                        $created++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Receivables processed for period {$period}",
            'period' => $period,
            'summary' => [
                'total_invoices' => $invoices->count(),
                'receivables_created' => $created,
                'receivables_updated' => $updated,
                'errors_count' => count($errors)
            ],
            'errors' => $errors
        ]);
    }
}
