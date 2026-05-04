<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice', 'client'])->get();

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->role, ['finance_admin', 'ar_collector', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only finance_admin, ar_collector, or super_admin can create payment.'
            ], 403);
        }

        $validated = $request->validate([
            'number' => 'required|unique:payments,number',
            'date' => 'required|date',
            'invoice_id' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        // Enforce invoice-client consistency
        $invoice = Invoice::find($validated['invoice_id']);
        if ($invoice && (int)$invoice->client_id !== (int)$validated['client_id']) {
            return response()->json([
                'success' => false,
                'message' => 'client_id must match invoice client_id',
                'invoice_client_id' => $invoice->client_id,
                'payload_client_id' => $validated['client_id'],
            ], 422);
        }

        // Always start unposted (posting lewat endpoint posting)
        $validated['is_posted'] = false;

        $payment = Payment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully',
            'data' => $payment->load(['invoice', 'client'])
        ], 201);
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice', 'client']);

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $user = $request->user();

        if ((bool)$payment->is_posted === true && $user->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Payment already posted. Update is not allowed.'
            ], 409);
        }

        $validated = $request->validate([
            'number' => 'required|unique:payments,number,' . $payment->id,
            'date' => 'required|date',
            'invoice_id' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        // Enforce invoice-client consistency
        $invoice = Invoice::find($validated['invoice_id']);
        if ($invoice && (int)$invoice->client_id !== (int)$validated['client_id']) {
            return response()->json([
                'success' => false,
                'message' => 'client_id must match invoice client_id',
                'invoice_client_id' => $invoice->client_id,
                'payload_client_id' => $validated['client_id'],
            ], 422);
        }

        $payment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'data' => $payment->load(['invoice', 'client'])
        ]);
    }

    public function destroy(Request $request, Payment $payment)
    {
        $user = $request->user();

        if ((bool)$payment->is_posted === true && $user->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Payment already posted. Delete is not allowed.'
            ], 409);
        }

        $force = $request->query('force');
        if (($force === 'true' || $force === true) && $user->role === 'super_admin') {
            $payment->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Payment force-deleted successfully'
            ]);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }
}