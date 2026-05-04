<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BankController extends Controller
{
    public function index()
    {
        $Bank = Bank::query()->orderBy('code')->get()->map(function (Bank $bank) {
            return $this->enrichCoaNames($bank->toArray());
        });
        
        return response()->json([
            'success' => true,
            'data' => $Bank
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $banks = Bank::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%')
                    ->orWhere('account_number', 'like', '%' . $q . '%');
            })
            ->orderBy('code')
            ->limit(50)
            ->get()
            ->map(fn (Bank $bank) => $this->enrichCoaNames($bank->toArray()));

        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:banks',
            'name' => 'required|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'type' => 'nullable|in:M,H',
            'acc_code' => 'nullable|string',
            'cdf_code' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $this->validateCoaCodes($validated, ['acc_code', 'cdf_code']);

        $bank = Bank::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Bank created successfully',
        'data' => $this->enrichCoaNames($bank->toArray())
    ], 201);
}

    public function show(Bank $Bank)
    {
        return response()->json([
            'success' => true,
            'data' => $this->enrichCoaNames($Bank->toArray())
        ]);
    }

    public function update(Request $request, Bank $Bank)
    {
        $validated = $request->validate([
            'code' => 'required|unique:banks,code,' . $Bank->id,
            'name' => 'required|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'type' => 'nullable|in:M,H',
            'acc_code' => 'nullable|string',
            'cdf_code' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $this->validateCoaCodes($validated, ['acc_code', 'cdf_code']);

        $Bank->update($validated);
        $Bank->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Bank updated successfully',
            'data' => $this->enrichCoaNames($Bank->toArray()),
            'was_changed' => $Bank->wasChanged(),
            'changes' => $Bank->getChanges()
        ]);
    }

    public function destroy($id)
{
    // Find bank even if already soft deleted
    $bank = Bank::withTrashed()
        ->where('id', $id)
        ->orWhere('code', $id)
        ->first();

    if (! $bank) {
        return response()->json([
            'success' => false,
            'message' => 'Bank not found',
            'query' => $id
        ], 404);
    }

    try {
        // Force delete permanently from database
        $bank->forceDelete();
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to permanently delete bank',
            'error' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Bank permanently deleted from database'
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
            $row['acc_code'] ?? null,
            $row['cdf_code'] ?? null,
        ]);

        if ($codes === []) {
            $row['acc_code_name'] = null;
            $row['cdf_code_name'] = null;
            return $row;
        }

        $coaNames = ChartOfAccount::whereIn('code', $codes)->pluck('name', 'code');
        $row['acc_code_name'] = $coaNames[$row['acc_code'] ?? ''] ?? null;
        $row['cdf_code_name'] = $coaNames[$row['cdf_code'] ?? ''] ?? null;

        return $row;
    }
}
