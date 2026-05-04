<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TaxReportController extends Controller
{
    public function vat(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
            'type' => 'nullable|in:standard,simplified',
            'excel' => 'nullable|boolean',
        ]);

        $rows = $this->baseQuery($validated)
            ->orderBy('tax_date')
            ->orderBy('number')
            ->get()
            ->values()
            ->map(function (Invoice $invoice, int $index) {
                return [
                    'no' => $index + 1,
                    'no_transaksi' => $invoice->number,
                    'tgl' => optional($invoice->date)->format('Y-m-d'),
                    'nama_wajib_pajak' => $invoice->client->tax_name ?: ($invoice->client->name ?? null),
                    'npwp' => $invoice->client->npwp ?? null,
                    'dpp' => (float) $invoice->dpp,
                    'ppn' => (float) $invoice->ppn,
                    'no_pajak' => $invoice->tax_number,
                    'tgl_pajak' => optional($invoice->tax_date)->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'LAPORAN PAJAK STANDART',
                'period' => [
                    'from' => $validated['from'],
                    'to' => $validated['to'],
                ],
                'type' => $validated['type'] ?? 'standard',
                'excel' => (bool) ($validated['excel'] ?? false),
            ],
            'total_dpp' => (float) $rows->sum('dpp'),
            'total_ppn' => (float) $rows->sum('ppn'),
            'data' => $rows,
        ]);
    }

    public function summary(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $invoices = $this->baseQuery($validated)->get();

        return response()->json([
            'success' => true,
            'period' => [
                'from' => $validated['from'],
                'to' => $validated['to'],
            ],
            'summary' => [
                'total_invoice' => $invoices->count(),
                'total_dpp' => (float) $invoices->sum('dpp'),
                'total_ppn' => (float) $invoices->sum('ppn'),
            ],
            'data' => $invoices->groupBy('client_id')->map(function ($group) {
                return [
                    'client_code' => $group->first()->client->code ?? null,
                    'client_name' => $group->first()->client->tax_name ?: ($group->first()->client->name ?? null),
                    'npwp' => $group->first()->client->npwp ?? null,
                    'dpp' => (float) $group->sum('dpp'),
                    'ppn' => (float) $group->sum('ppn'),
                ];
            })->values(),
        ]);
    }

    private function baseQuery(array $validated): Builder
    {
        return Invoice::query()
            ->with(['client:id,code,name,tax_name,npwp'])
            ->whereDate('tax_date', '>=', $validated['from'])
            ->whereDate('tax_date', '<=', $validated['to'])
            ->whereNotNull('tax_date');
    }
}
