<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterItemProduct;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MasterItemProductController extends Controller
{
    /**
     * Display a listing of master item products
     */
    public function index()
    {
        $items = MasterItemProduct::where('is_active', true)
            ->select([
                'id', 'code', 'name', 'unit', 'price',
                'acc_omzet', 'acc_omzet_np', 'acc_piutang', 'cdf_piutang',
                'is_active'
            ])
            ->orderBy('code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Store a newly created master item product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:master_item_products,code|max:50',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:20',
            'price' => 'nullable|numeric|min:0',
            'acc_omzet' => 'required|string|max:20',
            'acc_omzet_np' => 'nullable|string|max:20',
            'acc_piutang' => 'required|string|max:20',
            'cdf_piutang' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        // Validasi bahwa COA (Chart of Account) ada
        $this->validateChartOfAccounts([
            $validated['acc_omzet'],
            $validated['acc_omzet_np'] ?? null,
            $validated['acc_piutang'],
            $validated['cdf_piutang'] ?? null
        ]);

        $item = MasterItemProduct::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Master item product created successfully',
            'data' => $this->formatItem($item)
        ], 201);
    }

    /**
     * Display the specified master item product
     */
    public function show(MasterItemProduct $masterItemProduct)
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatItem($masterItemProduct)
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $items = MasterItemProduct::query()
            ->select([
                'id', 'code', 'name', 'unit', 'price',
                'acc_omzet', 'acc_omzet_np', 'acc_piutang', 'cdf_piutang',
                'is_active'
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->orderBy('code')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Update the specified master item product
     */
    public function update(Request $request, MasterItemProduct $masterItemProduct)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:master_item_products,code,' . $masterItemProduct->id . '|max:50',
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:20',
            'price' => 'nullable|numeric|min:0',
            'acc_omzet' => 'required|string|max:20',
            'acc_omzet_np' => 'nullable|string|max:20',
            'acc_piutang' => 'required|string|max:20',
            'cdf_piutang' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean'
        ]);

        // Validasi COA
        $this->validateChartOfAccounts([
            $validated['acc_omzet'],
            $validated['acc_omzet_np'] ?? null,
            $validated['acc_piutang'],
            $validated['cdf_piutang'] ?? null
        ]);

        $masterItemProduct->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Master item product updated successfully',
            'data' => $this->formatItem($masterItemProduct->fresh())
        ]);
    }

    /**
     * Remove the specified master item product
     */
    public function destroy(MasterItemProduct $masterItemProduct)
    {
        // Check jika ada invoice items yang pakai ini
        if ($masterItemProduct->invoiceItems()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete master item product that has invoice items'
            ], 422);
        }

        $masterItemProduct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Master item product deleted successfully'
        ]);
    }

    /**
     * Toggle status master item product
     */
    public function toggleStatus(MasterItemProduct $masterItemProduct)
    {
        $newStatus = !$masterItemProduct->is_active;
        $masterItemProduct->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Master item product status updated',
            'data' => $this->formatItem($masterItemProduct->fresh())
        ]);
    }

    /**
     * Validate Chart of Accounts exist
     */
    private function validateChartOfAccounts(array $codes)
    {
        $codes = array_filter($codes); // Remove null values

        if (empty($codes)) {
            return;
        }

        $existing = ChartOfAccount::whereIn('code', $codes)->pluck('code')->toArray();
        $missing = array_diff($codes, $existing);

        if (!empty($missing)) {
            $validator = validator([], []);
            $validator->errors()->add('coa', 'Chart of Account codes not found: ' . implode(', ', $missing));
            throw new ValidationException($validator);
        }
    }

    private function formatItem(MasterItemProduct $item): array
    {
        $coaNames = ChartOfAccount::whereIn('code', array_filter([
            $item->acc_omzet,
            $item->acc_omzet_np,
            $item->acc_piutang,
            $item->cdf_piutang,
        ]))->pluck('name', 'code');

        return [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'unit' => $item->unit,
            'price' => $item->price,
            'acc_omzet' => $item->acc_omzet,
            'acc_omzet_name' => $coaNames[$item->acc_omzet] ?? null,
            'acc_omzet_np' => $item->acc_omzet_np,
            'acc_omzet_np_name' => $coaNames[$item->acc_omzet_np] ?? null,
            'acc_piutang' => $item->acc_piutang,
            'acc_piutang_name' => $coaNames[$item->acc_piutang] ?? null,
            'cdf_piutang' => $item->cdf_piutang,
            'cdf_piutang_name' => $coaNames[$item->cdf_piutang] ?? null,
            'is_active' => (bool) $item->is_active,
        ];
    }
}
