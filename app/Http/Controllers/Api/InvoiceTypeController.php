<?php

namespace App\Http\Controllers\Api;

use App\Models\InvoiceType;
use Illuminate\Http\Request;

class InvoiceTypeController
{
    /**
     * Display a listing of invoice types
     */
    public function index()
    {
        $types = InvoiceType::where('is_active', true)
            ->select(['id', 'code', 'name', 'is_license', 'auto_create_number', 'is_active', 'created_at'])
            ->orderBy('code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $types = InvoiceType::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->orderBy('code')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Store a newly created invoice type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:invoice_types,code|max:20',
            'name' => 'required|string|max:255',
            'is_license' => 'nullable|boolean',
            'auto_create_number' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_license'] = $validated['is_license'] ?? false;
        $validated['auto_create_number'] = $validated['auto_create_number'] ?? true;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $type = InvoiceType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Invoice type created successfully',
            'data' => $type
        ], 201);
    }

    /**
     * Display the specified invoice type
     */
    public function show(InvoiceType $invoiceType)
    {
        return response()->json([
            'success' => true,
            'data' => $invoiceType
        ]);
    }

    /**
     * Update the specified invoice type
     */
    public function update(Request $request, InvoiceType $invoiceType)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:invoice_types,code,' . $invoiceType->id . '|max:20',
            'name' => 'required|string|max:255',
            'is_license' => 'nullable|boolean',
            'auto_create_number' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);

        $invoiceType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Invoice type updated successfully',
            'data' => $invoiceType
        ]);
    }

    /**
     * Remove the specified invoice type
     */
    public function destroy(InvoiceType $invoiceType)
    {
        // Check if type memiliki invoices
        if ($invoiceType->invoices()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete invoice type that has invoices'
            ], 422);
        }

        $invoiceType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice type deleted successfully'
        ]);
    }

    /**
     * Toggle invoice type status
     */
    public function toggleStatus(InvoiceType $invoiceType)
    {
        $newStatus = !$invoiceType->is_active;
        $invoiceType->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice type status updated',
            'data' => $invoiceType
        ]);
    }
}
