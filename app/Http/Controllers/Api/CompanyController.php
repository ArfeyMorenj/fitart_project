<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        
        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $companies = Company::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->orderBy('code')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:companies',
            'name' => 'required|string',
            'address' => 'nullable|string',
            'address_invoice' => 'nullable|string',
            'city' => 'nullable|string',
            'city_invoice' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|string',
            'npwp' => 'nullable|string',
            'npkp' => 'nullable|string',
            'tax_name' => 'nullable|string',
            'tax_position' => 'nullable|string',
            'invoice_name' => 'nullable|string',
            'invoice_position' => 'nullable|string',
            'invoice_name_2' => 'nullable|string',
            'invoice_position_2' => 'nullable|string',
            'invoice_tolerance_days' => 'nullable|string',
            'upgrade_days' => 'nullable|string',
            'letterhead_top' => 'nullable|string',
            'letterhead_bottom' => 'nullable|string',
        ]);

        $company = Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }

    public function show(Company $company)
    {
        return response()->json([
            'success' => true,
            'data' => $company
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'code' => 'required|unique:companies,code,' . $company->id,
            'name' => 'required|string',
            'address' => 'nullable|string',
            'address_invoice' => 'nullable|string',
            'city' => 'nullable|string',
            'city_invoice' => 'nullable|string',
            'phone' => 'nullable|string',
            'fax' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|string',
            'npwp' => 'nullable|string',
            'npkp' => 'nullable|string',
            'tax_name' => 'nullable|string',
            'tax_position' => 'nullable|string',
            'invoice_name' => 'nullable|string',
            'invoice_position' => 'nullable|string',
            'invoice_name_2' => 'nullable|string',
            'invoice_position_2' => 'nullable|string',
            'invoice_tolerance_days' => 'nullable|string',
            'upgrade_days' => 'nullable|string',
            'letterhead_top' => 'nullable|string',
            'letterhead_bottom' => 'nullable|string',
        ]);

        $company->update($validated);
        $company->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully',
            'data' => $company,
            'was_changed' => $company->wasChanged(),
            'changes' => $company->getChanges()
        ]);
    }

   public function destroy($id)
{
    // Cari data (termasuk yang sudah soft deleted)
    $company = Company::withTrashed()
        ->where('id', $id)
        ->orWhere('code', $id)
        ->first();

    if (! $company) {
        return response()->json([
            'success' => false,
            'message' => 'Company not found',
            'query' => $id
        ], 404);
    }

    try {
        // Hapus permanen dari database
        $company->forceDelete();
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to permanently delete company',
            'error' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Company permanently deleted from database'
    ], 200);
}

}
