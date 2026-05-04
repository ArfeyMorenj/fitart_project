<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function index()
    {
        $licenses = License::with(['client', 'workOrder'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $licenses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_number' => 'required|unique:licenses,license_number',
            'client_id' => 'required|exists:clients,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'license_date' => 'required|date',
            'due_date' => 'nullable|date',
            'subtotal' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'status' => 'nullable|in:unpaid,partial,paid',
            'notes' => 'nullable|string',
        ]);

        $license = License::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'License created successfully',
            'data' => $license->load(['client','workOrder'])
        ], 201);
    }

    public function show(License $license)
    {
        $license->load(['client','workOrder']);

        return response()->json([
            'success' => true,
            'data' => $license
        ]);
    }

    public function update(Request $request, License $license)
    {
        $validated = $request->validate([
            'license_number' => 'required|unique:licenses,license_number,' . $license->id,
            'client_id' => 'required|exists:clients,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
            'license_date' => 'required|date',
            'due_date' => 'nullable|date',
            'subtotal' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'status' => 'nullable|in:unpaid,partial,paid',
            'notes' => 'nullable|string',
        ]);

        $license->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'License updated successfully',
            'data' => $license->load(['client','workOrder'])
        ]);
    }

    public function destroy(License $license)
    {
        $license->forcedelete();

        return response()->json([
            'success' => true,
            'message' => 'License deleted successfully'
        ]);
    }
}
