<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Receivable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReceivableReportController extends Controller
{
    public function summary(Request $request)
    {
        $validated = $request->validate([
            'period' => 'nullable|date_format:Y-m',
        ]);

        $period = $validated['period'] ?? now()->format('Y-m');

        $rows = Receivable::query()
            ->where('period', $period)
            ->orderBy('client_code')
            ->get()
            ->groupBy('client_id')
            ->map(function ($group) {
                return [
                    'client_code' => $group->first()->client_code,
                    'client_name' => $group->first()->client_name,
                    'saldo_awal' => (float) $group->sum('beginning_balance'),
                    'netto' => (float) $group->sum('netto'),
                    'ppn' => (float) $group->sum('ppn'),
                    'biaya' => (float) $group->sum('biaya'),
                    'nota_debet' => (float) $group->sum('nota_debet'),
                    'pembayaran' => (float) $group->sum('payment'),
                    'saldo_akhir' => (float) $group->sum('ending_balance'),
                ];
            })->values();

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'RINGKASAN PIUTANG',
                'period' => $period,
            ],
            'summary' => [
                'saldo_akhir' => (float) $rows->sum('saldo_akhir'),
            ],
            'data' => $rows,
        ]);
    }

    public function detail(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $query = Invoice::with(['client', 'payments', 'debitCreditNotes'])
            ->whereDate('date', '>=', $validated['from'])
            ->whereDate('date', '<=', $validated['to']);

        if (!empty($validated['client_id'])) {
            $query->where('client_id', $validated['client_id']);
        }

        $rows = $query->orderBy('date')->get()->map(function (Invoice $invoice) {
            $paid = (float) $invoice->payments->sum('amount');
            $nota = (float) $invoice->debitCreditNotes->sum('total_amount');
            $saldo = (float) $invoice->total + $nota - $paid;

            return [
                'tanggal' => optional($invoice->date)->format('Y-m-d'),
                'nomor' => $invoice->number,
                'keterangan' => $invoice->description,
                'tagihan' => (float) $invoice->total,
                'tgl_bukti' => optional($invoice->payments->first()?->date)->format('Y-m-d'),
                'no_bukti' => $invoice->payments->first()?->number,
                'jumlah' => $paid,
                'nota_dk' => $nota,
                'saldo' => $saldo,
                'client_code' => $invoice->client->code ?? null,
                'client_name' => $invoice->client->name ?? null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'LAPORAN DETAIL PIUTANG KLIEN',
                'period' => [
                    'from' => $validated['from'],
                    'to' => $validated['to'],
                ],
                'client_id' => $validated['client_id'] ?? null,
            ],
            'totals' => [
                'tagihan' => (float) $rows->sum('tagihan'),
                'jumlah' => (float) $rows->sum('jumlah'),
                'nota_dk' => (float) $rows->sum('nota_dk'),
                'saldo' => (float) $rows->sum('saldo'),
            ],
            'data' => $rows,
        ]);
    }

    public function data(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|date_format:Y-m',
        ]);

        $rows = Receivable::with(['client:id,code,name', 'invoice:id,number'])
            ->where('period', $validated['period'])
            ->orderBy('client_code')
            ->orderBy('invoice_id')
            ->get()
            ->map(function (Receivable $row) {
                return [
                    'no_invoice' => $row->invoice->number ?? null,
                    'kd_klien' => $row->client_code,
                    'nama_klien' => $row->client_name,
                    'saldo_awal' => (float) $row->beginning_balance,
                    'netto' => (float) $row->netto,
                    'ppn' => (float) $row->ppn,
                    'biaya' => (float) $row->biaya,
                    'nota_debet' => (float) $row->nota_debet,
                    'pembayaran' => (float) $row->payment,
                    'saldo_akhir' => (float) $row->ending_balance,
                ];
            });

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'DATA PIUTANG',
                'period' => $validated['period'],
            ],
            'summary' => [
                'saldo_awal' => (float) $rows->sum('saldo_awal'),
                'netto' => (float) $rows->sum('netto'),
                'ppn' => (float) $rows->sum('ppn'),
                'biaya' => (float) $rows->sum('biaya'),
                'nota_debet' => (float) $rows->sum('nota_debet'),
                'pembayaran' => (float) $rows->sum('pembayaran'),
                'saldo_akhir' => (float) $rows->sum('saldo_akhir'),
            ],
            'data' => $rows->values(),
        ]);
    }

    public function processDetail(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|date_format:Y-m',
        ]);

        $periodStart = Carbon::createFromFormat('Y-m-d', $validated['period'] . '-01')->startOfMonth();
        $nextPeriod = $periodStart->copy()->addMonth()->format('Y-m');

        $rows = Receivable::where('period', $validated['period'])->get();

        return response()->json([
            'success' => true,
            'report' => [
                'title' => 'PROSES PIUTANG',
                'period' => $validated['period'],
                'next_period' => $nextPeriod,
            ],
            'summary' => [
                'opening_balance' => (float) $rows->sum('beginning_balance'),
                'new_invoices' => (float) $rows->sum('netto') + (float) $rows->sum('ppn') + (float) $rows->sum('biaya'),
                'nota_debet' => (float) $rows->sum('nota_debet'),
                'payments' => (float) $rows->sum('payment'),
                'closing_balance' => (float) $rows->sum('ending_balance'),
            ],
            'rule' => 'saldo_akhir = saldo_awal + netto + ppn + biaya + nota_debet - pembayaran',
        ]);
    }
}
