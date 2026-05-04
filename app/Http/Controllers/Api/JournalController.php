<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $q = Journal::query();

        if ($request->filled('from')) {
            $q->whereDate('date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('date', '<=', $request->input('to'));
        }
        if ($request->filled('acc_code')) {
            $q->where('acc_code', $request->input('acc_code'));
        }
        if ($request->filled('document_number')) {
            $q->where('document_number', $request->input('document_number'));
        }

        $data = $q->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}