<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::query()->orderBy('code')->get()->map(function (Item $item) {
            return $this->enrichCoaNames($item->toArray());
        });
        
        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $items = Item::query()
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

    public function store(Request $request)
    {
        // Validasi role: hanya super_admin dan sales_operator yang boleh menambah item
        $user = $request->user();
        if (!in_array($user->role, ['super_admin', 'sales_operator'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only super_admin or sales_operator can create item.'
            ], 403);
        }

        $validated = $request->validate([
            'code' => 'required|unique:items',
            'name' => 'required|string',
            'acc_omzet' => 'nullable|string',
            'acc_piutang' => 'nullable|string',
            'cdf_omzet' => 'nullable|string',
            'cdf_piutang' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $this->validateCoaCodes($validated, ['acc_omzet', 'acc_piutang', 'cdf_omzet', 'cdf_piutang']);

        $item = Item::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'data' => $this->enrichCoaNames($item->toArray())
        ], 201);
    }

    public function show($id)
    {
        $item = Item::with('invoiceItems')->find($id);

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->enrichCoaNames($item->toArray())
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        $validated = $request->validate([
            'code' => 'required|unique:items,code,' . $item->id,
            'name' => 'required|string',
            'acc_omzet' => 'nullable|string',
            'acc_piutang' => 'nullable|string',
            'cdf_omzet' => 'nullable|string',
            'cdf_piutang' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $this->validateCoaCodes($validated, ['acc_omzet', 'acc_piutang', 'cdf_omzet', 'cdf_piutang']);

        $item->update($validated);
        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data' => $this->enrichCoaNames($item->toArray()),
            'was_changed' => $item->wasChanged(),
            'changes' => $item->getChanges()
        ]);
    }

    public function destroy($id)
    {
        // ambil item, termasuk yang sudah soft delete
        $item = Item::withTrashed()
            ->where('id', $id)
            ->orWhere('code', $id)
            ->first();

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
                'query' => $id
            ], 404);
        }

        try {
            // hapus permanent dari database
            $item->forceDelete();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete item',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item permanently deleted from database'
        ], 200);
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
            $row['acc_piutang'] ?? null,
            $row['cdf_omzet'] ?? null,
            $row['cdf_piutang'] ?? null,
        ]);

        if ($codes === []) {
            $row['acc_omzet_name'] = null;
            $row['acc_piutang_name'] = null;
            $row['cdf_omzet_name'] = null;
            $row['cdf_piutang_name'] = null;
            return $row;
        }

        $coaNames = ChartOfAccount::whereIn('code', $codes)->pluck('name', 'code');

        $row['acc_omzet_name'] = $coaNames[$row['acc_omzet'] ?? ''] ?? null;
        $row['acc_piutang_name'] = $coaNames[$row['acc_piutang'] ?? ''] ?? null;
        $row['cdf_omzet_name'] = $coaNames[$row['cdf_omzet'] ?? ''] ?? null;
        $row['cdf_piutang_name'] = $coaNames[$row['cdf_piutang'] ?? ''] ?? null;

        return $row;
    }
}
