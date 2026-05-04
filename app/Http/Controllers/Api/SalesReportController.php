<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function summary(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $invoices = Invoice::with(['items'])
            ->whereDate('date', '>=', $validated['from'])
            ->whereDate('date', '<=', $validated['to'])
            ->orderBy('date')
            ->get();

        $rows = [];
        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $key = $item->item_code . '|' . $item->item_name;
                if (!isset($rows[$key])) {
                    $rows[$key] = [
                        'kode' => $item->item_code,
                        'nama' => $item->item_name,
                        'bruto' => 0.0,
                        'discount' => 0.0,
                        'dpp' => 0.0,
                        'ppn' => 0.0,
                        'total' => 0.0,
                    ];
                }

                $rows[$key]['bruto'] += (float) $item->bruto;
                $rows[$key]['dpp'] += (float) $item->bruto;
                $rows[$key]['total'] += (float) $item->bruto;
            }
        }

        foreach ($rows as $key => $row) {
            $ppn = round($row['dpp'] * 0.11, 2);
            $rows[$key]['ppn'] = $ppn;
            $rows[$key]['total'] = $row['dpp'] + $ppn;
        }

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'LAPORAN REKAP PENJUALAN PER ITEM PRODUK',
                'period' => [
                    'from' => $validated['from'],
                    'to' => $validated['to'],
                ],
            ],
            'totals' => [
                'bruto' => (float) collect($rows)->sum('bruto'),
                'discount' => (float) collect($rows)->sum('discount'),
                'dpp' => (float) collect($rows)->sum('dpp'),
                'ppn' => (float) collect($rows)->sum('ppn'),
                'total' => (float) collect($rows)->sum('total'),
            ],
            'data' => array_values($rows),
        ]);
    }

    public function perClient(Request $request)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $invoices = Invoice::with(['client'])
            ->when(!empty($validated['from']), fn ($q) => $q->whereDate('date', '>=', $validated['from']))
            ->when(!empty($validated['to']), fn ($q) => $q->whereDate('date', '<=', $validated['to']))
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'LAPORAN PENJUALAN PER CLIENT',
                'period' => [
                    'from' => $validated['from'] ?? null,
                    'to' => $validated['to'] ?? null,
                ],
            ],
            'data' => $invoices->groupBy('client_id')->map(function ($group) {
                return [
                    'client_code' => $group->first()->client->code ?? null,
                    'client_name' => $group->first()->client->name ?? null,
                    'bruto' => (float) $group->sum('bruto'),
                    'discount' => (float) $group->sum('discount'),
                    'dpp' => (float) $group->sum('dpp'),
                    'ppn' => (float) $group->sum('ppn'),
                    'total' => (float) $group->sum('total'),
                ];
            })->values(),
        ]);
    }
}
