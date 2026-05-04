<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSeries;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceSeriesController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'period' => ['nullable', 'regex:/^(0[1-9]|1[0-2])-\d{4}$/'],
        ]);

        $query = InvoiceSeries::query();

        if (!empty($validated['period'])) {
            $query->where('tax_period', $validated['period']);
        }

        $rows = $query
            ->orderByDesc('tax_year')
            ->orderByDesc('tax_period')
            ->orderByDesc('filled_date')
            ->get()
            ->map(function (InvoiceSeries $row) {
                return [
                    'id' => $row->id,
                    'filled_date' => optional($row->filled_date)->format('Y-m-d'),
                    'period' => $row->tax_period,
                    'tax_code' => $row->tax_code,
                    'start_number' => $row->start_number,
                    'end_number' => $row->end_number,
                    'current_number' => $row->last_number,
                    'ppn_percentage' => (float) $row->ppn_percentage,
                    'dpp_percentage' => (float) $row->dpp_percentage,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'filled_date' => 'nullable|date',
            'period' => ['required', 'regex:/^(0[1-9]|1[0-2])-\d{4}$/'],
            'tax_code' => 'required|string|max:10',
            'start_number' => 'required|string|max:50',
            'end_number' => 'required|string|max:50',
            'current_number' => 'nullable|string|max:50',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
            'dpp_percentage' => 'nullable|numeric|min:0',
        ]);

        $periodDate = Carbon::createFromFormat('m-Y', $validated['period']);

        $series = InvoiceSeries::create([
            'filled_date' => $validated['filled_date'] ?? now()->toDateString(),
            'period_start' => $periodDate->copy()->startOfMonth()->toDateString(),
            'period_end' => $periodDate->copy()->endOfMonth()->toDateString(),
            'sequence' => 1,
            'prefix' => $validated['tax_code'],
            'tax_period' => $validated['period'],
            'tax_year' => $periodDate->format('Y'),
            'tax_code' => $validated['tax_code'],
            'start_number' => $validated['start_number'],
            'end_number' => $validated['end_number'],
            'last_number' => $validated['current_number'] ?? $validated['start_number'],
            'ppn_percentage' => $validated['ppn_percentage'] ?? 11,
            'dpp_percentage' => $validated['dpp_percentage'] ?? 1.11,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tax series created successfully',
            'data' => $series,
        ], 201);
    }

    public function destroy(InvoiceSeries $taxSeries)
    {
        $taxSeries->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tax series deleted successfully',
        ]);
    }

    public function nextNumber(InvoiceSeries $taxSeries)
    {
        if (!$taxSeries->start_number || !$taxSeries->end_number) {
            return response()->json([
                'success' => false,
                'message' => 'Start/end number is not configured.',
            ], 422);
        }

        $current = $taxSeries->last_number ?: $taxSeries->start_number;

        if (!ctype_digit((string) $current) || !ctype_digit((string) $taxSeries->end_number)) {
            return response()->json([
                'success' => false,
                'message' => 'Auto increment only supports numeric tax numbers.',
            ], 422);
        }

        $next = (int) $current + 1;
        $end = (int) $taxSeries->end_number;

        if ($next > $end) {
            return response()->json([
                'success' => false,
                'message' => 'Tax series number has reached end of range.',
            ], 422);
        }

        $padded = str_pad((string) $next, strlen((string) $taxSeries->end_number), '0', STR_PAD_LEFT);
        $taxSeries->update(['last_number' => $padded]);

        return response()->json([
            'success' => true,
            'message' => 'Next tax number generated',
            'data' => [
                'id' => $taxSeries->id,
                'period' => $taxSeries->tax_period,
                'tax_code' => $taxSeries->tax_code,
                'current_number' => $taxSeries->last_number,
            ],
        ]);
    }
}
