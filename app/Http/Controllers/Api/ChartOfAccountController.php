<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = ChartOfAccount::query()->orderBy('code');

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        return response()->json([
            'success' => true,
            'data' => $query->limit(200)->get(),
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rows = ChartOfAccount::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('code', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->orderBy('code')
            ->limit(100)
            ->get(['id', 'code', 'name', 'type', 'category', 'is_active']);

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function show(ChartOfAccount $chartOfAccount)
    {
        return response()->json([
            'success' => true,
            'data' => $chartOfAccount,
        ]);
    }
}
