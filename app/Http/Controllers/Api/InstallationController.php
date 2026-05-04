<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use Illuminate\Http\Request;

class InstallationController extends Controller

{
    public function index()
    {
        $installations = Installation::with(['workOrder', 'client'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $installations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'client_id' => 'required|exists:clients,id',
            'install_date' => 'required|date',
            'implementor_1' => 'nullable',
            'implementor_2' => 'nullable',
            'implementor_3' => 'nullable',
            'notes' => 'nullable',
        ]);

        $installation = Installation::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Installation created successfully',
            'data' => $installation->load(['workOrder', 'client'])
        ], 201);
    }

    public function show(Installation $installation)
    {
        $installation->load(['workOrder', 'client']);
        
        return response()->json([
            'success' => true,
            'data' => $installation
        ]);
    }

    public function update(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'client_id' => 'required|exists:clients,id',
            'install_date' => 'required|date',
            'implementor_1' => 'nullable',
            'implementor_2' => 'nullable',
            'implementor_3' => 'nullable',
            'notes' => 'nullable',
        ]);

        $installation->update($validated);
        $installation->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Installation updated successfully',
            'data' => $installation->load(['workOrder', 'client']),
            'was_changed' => $installation->wasChanged(),
            'changes' => $installation->getChanges()
        ]);
    }

    public function destroy(Request $request, Installation $installation)
    {
        $installation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Installation deleted successfully',
            'deleted' => true
        ]);
    }
}
