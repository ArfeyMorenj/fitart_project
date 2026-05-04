<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProtectionPeriod;
use Illuminate\Http\Request;

class ProtectionController extends Controller
{
    public function index()
    {
        $protections = ProtectionPeriod::all();
        
        return response()->json([
            'success' => true,
            'data' => $protections
        ]);
    }

    public function store(Request $request)
    {
        // Validasi role: hanya super_admin yang boleh create proteksi periode
        $user = $request->user();
        if ($user->role !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only super_admin can create protection period.'
            ], 403);
        }
        $validated = $request->validate([
            'period' => 'required|unique:protection_periods,period',
            'is_protected' => 'nullable|boolean',
            'protected_at' => 'nullable|date',
            'protected_by' => 'nullable|exists:users,id',
        ]);

        $protection = ProtectionPeriod::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Protection created successfully',
            'data' => $protection
        ], 201);
    }

    public function show(ProtectionPeriod $protection)
    {
        return response()->json([
            'success' => true,
            'data' => $protection
        ]);
    }

    public function update(Request $request, ProtectionPeriod $protection)
    {
        $validated = $request->validate([
            'period' => 'required|unique:protection_periods,period,' . $protection->id,
            'is_protected' => 'nullable|boolean',
            'protected_at' => 'nullable|date',
            'protected_by' => 'nullable|exists:users,id',
        ]);

        $protection->update($validated);
        $protection->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Protection updated successfully',
            'data' => $protection,
            'was_changed' => $protection->wasChanged(),
            'changes' => $protection->getChanges()
        ]);
    }

    public function destroy(Request $request, ProtectionPeriod $protection)
    {
        $protection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Protection deleted successfully',
            'deleted' => true
        ]);
    }
}