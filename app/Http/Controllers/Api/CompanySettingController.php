<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompanySettingController extends Controller
{
    public function show()
    {
        $setting = CompanySetting::first();

        return response()->json([
            'success' => true,
            'data' => $setting ? $this->enrichCoaNames($setting->toArray()) : null
        ]);
    }

    public function update(Request $request)
    {
        $setting = CompanySetting::first() ?? new CompanySetting();

        $validated = $request->validate([
            'logo' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'npwp' => 'nullable|string',
            'period_start' => 'nullable|date',

            'acc_ppn_kes' => 'nullable|string',
            'acc_ppn_mas' => 'nullable|string',
            'acc_discount' => 'nullable|string',

            'bank1' => 'nullable|string',
            'bank1_sn' => 'nullable|string',
            'bank1_ac' => 'nullable|string',

            'bank2' => 'nullable|string',
            'bank2_sn' => 'nullable|string',
            'bank2_ac' => 'nullable|string',
        ]);
        $this->validateCoaCodes($validated, ['acc_ppn_kes', 'acc_ppn_mas', 'acc_discount']);

        if ($request->hasFile('logo')) {
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }

            $validated['logo'] = $request->file('logo')->store('company_logo', 'public');
        }

        $setting->fill($validated)->save();

        return response()->json([
            'success' => true,
            'message' => 'Company settings updated successfully',
            'data' => $this->enrichCoaNames($setting->toArray())
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
            $row['acc_ppn_kes'] ?? null,
            $row['acc_ppn_mas'] ?? null,
            $row['acc_discount'] ?? null,
        ]);

        if ($codes === []) {
            $row['acc_ppn_kes_name'] = null;
            $row['acc_ppn_mas_name'] = null;
            $row['acc_discount_name'] = null;
            return $row;
        }

        $coaNames = ChartOfAccount::whereIn('code', $codes)->pluck('name', 'code');
        $row['acc_ppn_kes_name'] = $coaNames[$row['acc_ppn_kes'] ?? ''] ?? null;
        $row['acc_ppn_mas_name'] = $coaNames[$row['acc_ppn_mas'] ?? ''] ?? null;
        $row['acc_discount_name'] = $coaNames[$row['acc_discount'] ?? ''] ?? null;

        return $row;
    }
}
