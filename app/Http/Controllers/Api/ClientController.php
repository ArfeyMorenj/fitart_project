<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::all();
        
        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:clients',
            'status' => 'nullable|string',
            'name' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'npwp' => 'nullable|string',
            'npkp' => 'nullable|string',
            'tax_name' => 'nullable|string',
            'tax_address' => 'nullable|string',
            'credit_term_days' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Use DB defaults when fields are not provided
        $data = array_merge([
            'status' => $request->input('status', 'NON GROUP'),
            'credit_term_days' => $request->input('credit_term_days', 0),
            'is_active' => $request->input('is_active', true),
        ], $validated);

        $client = Client::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully',
            'data' => $client
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        // Load relasi (include contacts per related table)
        $client->load(['workOrders', 'invoices', 'contacts', 'licenses']);
        
        return response()->json([
            'success' => true,
            'data' => $client
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'code' => 'required|unique:clients,code,' . $client->id,
            'status' => 'nullable|string',
            'name' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'npwp' => 'nullable|string',
            'npkp' => 'nullable|string',
            'tax_name' => 'nullable|string',
            'tax_address' => 'nullable|string',
            'credit_term_days' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $client->update($validated);

        // reload from database to ensure returned data reflects db state
        $client->refresh();
        $client->load(['workOrders', 'invoices', 'contacts', 'licenses']);

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'data' => $client,
            'was_changed' => $client->wasChanged(),
            'changes' => $client->getChanges()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    // Cari data, termasuk yang sudah soft delete
    $client = Client::withTrashed()
        ->where('id', $id)
        ->orWhere('code', $id)
        ->first();

    if (! $client) {
        return response()->json([
            'success' => false,
            'message' => 'Client not found',
            'query' => $id
        ], 404);
    }

    try {
        // Hapus permanen dari database
        $client->forceDelete();
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to permanently delete client',
            'error' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Client permanently deleted from database'
    ], 200);
}

    public function search(Request $request)
{
    $query = $request->input('q');

    $clients = Client::where('name', 'like', '%' . $query . '%')
        ->orWhere('code', 'like', '%' . $query . '%')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $clients
    ]);
}

}