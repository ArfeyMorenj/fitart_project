<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    private function blockIfInvoicePosted(Request $request, $invoiceId): ?\Illuminate\Http\JsonResponse
{
    $invoice = \App\Models\Invoice::find($invoiceId);
    if (!$invoice) return null;

    $user = $request->user();
    if ((bool)$invoice->is_posted === true && $user?->role !== 'super_admin') {
        return response()->json([
            'success' => false,
            'message' => 'Invoice already posted. Invoice items cannot be modified.'
        ], 409);
    }

    return null;
}
    public function index()
    {
        $invoiceItems = InvoiceItem::with(['invoice', 'masterItemProduct'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $invoiceItems
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'invoice_id' => 'required|exists:invoices,id',
        'master_item_product_id' => 'nullable|exists:master_item_products,id',
        'item_code' => 'nullable|string',
        'item_name' => 'required|string',
        'description' => 'nullable|string',
        'qty' => 'nullable|integer|min:1',
        'unit' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'bruto' => 'nullable|numeric|min:0',
        'months' => 'nullable|integer|min:1',
    ]);

    if ($resp = $this->blockIfInvoicePosted($request, $validated['invoice_id'])) {
        return $resp;
    }

    // auto bruto kalau tidak dikirim
    if (!isset($validated['bruto'])) {
        $qty = (int)($validated['qty'] ?? 1);
        $price = (float)($validated['price'] ?? 0);
        $validated['bruto'] = $qty * $price;
    }

    $invoiceItem = InvoiceItem::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Invoice Item created successfully',
        'data' => $invoiceItem->load(['invoice', 'masterItemProduct'])
    ], 201);
}

    public function show(InvoiceItem $invoiceItem)
    {
        $invoiceItem->load(['invoice', 'masterItemProduct']);
        
        return response()->json([
            'success' => true,
            'data' => $invoiceItem
        ]);
    }

    public function update(Request $request, InvoiceItem $invoiceItem)
{
    $validated = $request->validate([
        'invoice_id' => 'required|exists:invoices,id',
        'master_item_product_id' => 'nullable|exists:master_item_products,id',
        'item_code' => 'nullable|string',
        'item_name' => 'required|string',
        'description' => 'nullable|string',
        'qty' => 'nullable|integer|min:1',
        'unit' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
        'bruto' => 'nullable|numeric|min:0',
        'months' => 'nullable|integer|min:1',
    ]);

    if ($resp = $this->blockIfInvoicePosted($request, $invoiceItem->invoice_id)) {
        return $resp;
    }

    if (!isset($validated['bruto'])) {
        $qty = (int)($validated['qty'] ?? $invoiceItem->qty ?? 1);
        $price = (float)($validated['price'] ?? $invoiceItem->price ?? 0);
        $validated['bruto'] = $qty * $price;
    }

    $invoiceItem->update($validated);
    $invoiceItem->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Invoice Item updated successfully',
        'data' => $invoiceItem->load(['invoice', 'masterItemProduct']),
        'was_changed' => $invoiceItem->wasChanged(),
        'changes' => $invoiceItem->getChanges()
    ]);
}

    public function destroy(Request $request, InvoiceItem $invoiceItem)
    {
        $invoiceItem->delete();
        return response()->json([
            'success' => true,
            'message' => 'Invoice Item deleted successfully',
            'deleted' => true
        ]);
    }
}