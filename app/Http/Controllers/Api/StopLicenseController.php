<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\WorkOrder;
use App\Models\StopLicense;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StopLicenseController extends Controller
{
    public function index()
    {
        $stopLicenses = StopLicense::with(['workOrder', 'client', 'product', 'clientSpv'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $stopLicenses
        ]);
    }

    public function search(Request $request)
    {
        $query = StopLicense::with(['workOrder', 'client', 'product', 'clientSpv']);

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->number . '%');
        }

        if ($request->filled('client_name')) {
            $clientName = $request->client_name;
            $query->whereHas('client', function ($clientQuery) use ($clientName) {
                $clientQuery->where('name', 'like', '%' . $clientName . '%');
            });
        }

        if ($request->filled('work_order_number')) {
            $workOrderNumber = $request->work_order_number;
            $query->whereHas('workOrder', function ($workOrderQuery) use ($workOrderNumber) {
                $workOrderQuery->where('number', 'like', '%' . $workOrderNumber . '%');
            });
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('number', 'like', '%' . $q . '%')
                    ->orWhereHas('client', function ($clientQuery) use ($q) {
                        $clientQuery->where('name', 'like', '%' . $q . '%')
                            ->orWhere('code', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('workOrder', function ($workOrderQuery) use ($q) {
                        $workOrderQuery->where('number', 'like', '%' . $q . '%');
                    });
            });
        }

        $stopLicenses = $query->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $stopLicenses,
        ]);
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'number' => 'required|unique:stop_licenses,number',
        'date' => 'required|date',
        'stop_date' => 'required|date',
        'work_order_id' => 'required|exists:work_orders,id',
        'client_id' => 'nullable|exists:clients,id',
        'product_id' => 'nullable|exists:products,id',
        'client_spv_id' => 'nullable|exists:team_members,id',
        'client_spv_code' => 'nullable|string',
        'notes' => 'nullable|string',
        'is_stopped' => 'boolean',
    ]);

    $validated = $this->resolveStopLicenseLookup($validated);
    $stopLicense = StopLicense::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Stop License created successfully',
        'data' => $stopLicense->load(['workOrder', 'client', 'product', 'clientSpv'])
    ], 201);
}



    public function show(StopLicense $stopLicense)
    {
        
        return response()->json([
            'success' => true,
            'data' => $stopLicense
        ]);
    }

   public function update(Request $request, StopLicense $stopLicense)
{
    $validated = $request->validate([
        'number' => 'required|string|max:50|unique:stop_licenses,number,' . $stopLicense->id,
        'date' => 'required|date',
        'stop_date' => 'required|date',
        'work_order_id' => 'required|exists:work_orders,id',
        'client_id' => 'nullable|exists:clients,id',
        'product_id' => 'nullable|exists:products,id',
        'client_spv_id' => 'nullable|exists:team_members,id',
        'client_spv_code' => 'nullable|string',
        'notes' => 'nullable|string',
        'is_stopped' => 'nullable|boolean',
    ]);

    $validated = $this->resolveStopLicenseLookup($validated);
    $stopLicense->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Stop License updated successfully',
        'data' => $stopLicense->fresh()->load(['workOrder', 'client', 'product', 'clientSpv'])
    ], 200);
}


    public function destroy(Request $request, StopLicense $stopLicense)
    {
        $stopLicense->forcedelete();

        return response()->json([
            'success' => true,
            'message' => 'Stop License deleted successfully',
            'deleted' => true
        ]);
    }

    private function resolveStopLicenseLookup(array $validated): array
    {
        $workOrder = WorkOrder::find($validated['work_order_id']);
        if ($workOrder) {
            $validated['client_id'] = $validated['client_id'] ?? $workOrder->client_id;
            $validated['product_id'] = $validated['product_id'] ?? $workOrder->product_id;
        }

        if (!empty($validated['client_spv_code'])) {
            $member = TeamMember::where('code', $validated['client_spv_code'])->first();
            if (!$member) {
                throw ValidationException::withMessages([
                    'client_spv_code' => ['Client supervisor code not found in team_members.'],
                ]);
            }
            $validated['client_spv_id'] = $member->id;
            $validated['client_spv'] = $member->name;
        } elseif (!empty($validated['client_spv_id'])) {
            $member = TeamMember::find($validated['client_spv_id']);
            if ($member) {
                $validated['client_spv'] = $member->name;
            }
        }

        unset($validated['client_spv_code']);
        return $validated;
    }
}
