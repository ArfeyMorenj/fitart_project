<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductGroupController extends Controller
{
    public function index()
    {
        $groups = ProductGroup::query()
            ->orderBy('code')
            ->get()
            ->map(fn (ProductGroup $group) => $this->enrichCoaNames($group->toArray()));

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $groups = ProductGroup::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->orderBy('code')
            ->limit(50)
            ->get()
            ->map(fn (ProductGroup $group) => $this->enrichCoaNames($group->toArray()));

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->role, ['super_admin', 'sales_operator'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:product_groups,code',
            'name' => 'required|string|max:255',
            'acc_omzet' => 'nullable|string|max:20',
            'cdf_piutang' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);
        $this->validateCoaCodes($validated, ['acc_omzet', 'cdf_piutang']);

        $group = ProductGroup::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'acc_omzet' => $validated['acc_omzet'] ?? null,
            'cdf_piutang' => $validated['cdf_piutang'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product Group created successfully',
            'data' => $this->enrichCoaNames($group->toArray())
        ], 201);
    }

    public function show(ProductGroup $productGroup)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrichCoaNames($productGroup->toArray())
        ]);
    }

    public function update(Request $request, ProductGroup $productGroup)
    {
        $user = $request->user();
        if (!in_array($user->role, ['super_admin', 'sales_operator'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:product_groups,code,' . $productGroup->id,
            'name' => 'required|string|max:255',
            'acc_omzet' => 'nullable|string|max:20',
            'cdf_piutang' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);
        $this->validateCoaCodes($validated, ['acc_omzet', 'cdf_piutang']);

        $productGroup->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'acc_omzet' => $validated['acc_omzet'] ?? null,
            'cdf_piutang' => $validated['cdf_piutang'] ?? null,
            'is_active' => $validated['is_active'] ?? $productGroup->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product Group updated successfully',
            'data' => $this->enrichCoaNames($productGroup->fresh()->toArray()),
        ]);
    }

    public function destroy(Request $request, ProductGroup $productGroup)
    {
        $user = $request->user();
        if ($user->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        // Soft delete
        $productGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product Group deleted successfully',
            'deleted' => true
        ]);
    }

    private function validateCoaCodes(array $payload, array $fields): void
    {
        $codes = [];
        foreach ($fields as $field) {
            $value = $payload[$field] ?? null;
            if (!empty($value)) {
                $codes[$field] = $value;
            }
        }

        if ($codes === []) {
            return;
        }

        $existing = ChartOfAccount::whereIn('code', array_values($codes))
            ->pluck('name', 'code');

        $errors = [];
        foreach ($codes as $field => $code) {
            if (!$existing->has($code)) {
                $errors[$field] = ["Account code [$code] not found in chart_of_accounts."];
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function enrichCoaNames(array $row): array
    {
        $codes = array_filter([
            $row['acc_omzet'] ?? null,
            $row['cdf_piutang'] ?? null,
        ]);

        if ($codes === []) {
            $row['acc_omzet_name'] = null;
            $row['cdf_piutang_name'] = null;
            return $row;
        }

        $coaNames = ChartOfAccount::whereIn('code', $codes)->pluck('name', 'code');
        $row['acc_omzet_name'] = $coaNames[$row['acc_omzet'] ?? ''] ?? null;
        $row['cdf_piutang_name'] = $coaNames[$row['cdf_piutang'] ?? ''] ?? null;

        return $row;
    }
}
