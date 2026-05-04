<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceType;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RecurringInvoiceController extends Controller
{
    public function generate(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->role, ['finance_admin', 'super_admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'period' => 'required|string|size:7', // YYYY-MM
            'invoice_type_id' => 'nullable|exists:invoice_types,id',
            'bank_id' => 'nullable|exists:banks,id',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $period = $validated['period'];
        $periodStart = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $periodEnd   = Carbon::createFromFormat('Y-m', $period)->endOfMonth();

        $invoiceType = null;
        if (!empty($validated['invoice_type_id'])) {
            $invoiceType = InvoiceType::find($validated['invoice_type_id']);
        } else {
            $invoiceType = InvoiceType::where('is_license', true)->where('is_active', true)->first();
        }
        if (!$invoiceType) {
            return response()->json([
                'success' => false,
                'message' => 'InvoiceType license tidak ditemukan. Set minimal 1 invoice_type is_license=1'
            ], 422);
        }

        // Ambil WO aktif yang start_license <= akhir periode, dan tidak di-stop sebelum periode berjalan
        $workOrders = WorkOrder::with(['client', 'item', 'stopLicense'])
            ->where('status', 'AKTIF')
            ->whereNotNull('start_license')
            ->whereDate('start_license', '<=', $periodEnd)
            ->get()
            ->filter(function ($wo) use ($periodStart) {
                if (!$wo->stopLicense) return true;
                // kalau stop_date < period start => jangan generate
                return Carbon::parse($wo->stopLicense->stop_date)->gte($periodStart) === true && $wo->stopLicense->is_stopped == false;
            })
            ->values();

        $created = [];
        $skipped = [];

        DB::transaction(function () use ($workOrders, $invoiceType, $validated, $period, $periodEnd, &$created, &$skipped) {

            foreach ($workOrders as $wo) {
                // skip kalau sudah ada invoice recurring WO + period
                $exists = Invoice::where('work_order_id', $wo->id)
                    ->where('period', $period)
                    ->exists();

                if ($exists) {
                    $skipped[] = ['work_order_id' => $wo->id, 'reason' => 'already generated'];
                    continue;
                }

                $client = $wo->client;

                $qty = max(1, (int)($wo->item_count ?? 1));
                $price = (float)($wo->amount ?? 0);
                $dpp = $qty * $price;

                $ppnPct = isset($validated['ppn_percentage'])
                    ? (float)$validated['ppn_percentage']
                    : 11;

                $ppn = round($dpp * ($ppnPct / 100), 2);
                $total = $dpp + $ppn;

                // simple numbering fallback (biar gak ngeblok existing flow)
                $number = 'RC-' . str_replace('-', '', $period) . '-' . str_pad((Invoice::withTrashed()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

                $invoice = Invoice::create([
                    'number' => $number,
                    'invoice_type_id' => $invoiceType->id,
                    'client_id' => $client->id,
                    'bank_id' => $validated['bank_id'] ?? null,
                    'work_order_id' => $wo->id,
                    'period' => $period,

                    'date' => $periodEnd->toDateString(),
                    'due_date' => $client->credit_term_days ? $periodEnd->copy()->addDays((int)$client->credit_term_days)->toDateString() : null,

                    'client_address' => $client->address,
                    'description' => "Recurring {$period} / WO {$wo->number}",
                    'bruto' => $dpp,
                    'discount' => 0,
                    'dpp' => $dpp,
                    'ppn' => $ppn,
                    'ppn_percentage' => $ppnPct,
                    'dp' => 0,
                    'other' => 0,
                    'total' => $total,
                    'include_ppn' => true,
                    'is_paid' => false,
                    'is_posted' => false,
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'master_item_product_id' => null,
                    'item_code' => $wo->item?->code,
                    'item_name' => $wo->item?->name ?? 'Recurring Item',
                    'description' => "WO {$wo->number}",
                    'qty' => $qty,
                    'unit' => 'UNIT',
                    'price' => $price,
                    'months' => 1,
                ]);

                $created[] = $invoice->load(['client', 'invoiceType', 'items', 'workOrder']);
            }
        });

        return response()->json([
            'success' => true,
            'period' => $period,
            'created_count' => count($created),
            'skipped_count' => count($skipped),
            'created' => $created,
            'skipped' => $skipped,
        ]);
    }
}