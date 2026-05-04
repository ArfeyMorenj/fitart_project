<?php

namespace App\Http\Middleware;

use App\Models\ProtectionPeriod;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EnsurePeriodNotProtected
{
    public function handle(Request $request, Closure $next)
    {
        // hanya block untuk request mutasi data
        if ($request->isMethodSafe()) {
            return $next($request);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $date = $this->resolveDocumentDate($request);
        if (!$date) {
            return $next($request);
        }

        $period = $date->format('Y-m');

        $isLocked = ProtectionPeriod::where('period', $period)
            ->where('is_protected', true)
            ->exists();

        if (!$isLocked) {
            return $next($request);
        }

        // super_admin always bypass
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // bypass invoice-level flag (hanya kalau invoice memang sudah di-flag pass_protelasi)
        $invoice = $request->route('invoice');
        if ($invoice && (bool)($invoice->pass_protelasi ?? false) === true) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Period is locked. Transaction not allowed.',
            'period' => $period
        ], 423);
    }

   private function resolveDocumentDate(Request $request): ?Carbon
{
    // Untuk PUT/PATCH: cek NEW date dari request body DULU
    if (in_array($request->method(), ['PUT', 'PATCH']) && $request->filled('date')) {
        return Carbon::parse($request->input('date'));
    }

    // Untuk POST/DELETE: cek dari route binding (existing record)
    foreach (['invoice', 'payment', 'receivable', 'posting_payment'] as $key) {
        $model = $request->route($key);
        if ($model && isset($model->date)) {
            return Carbon::parse($model->date);
        }
    }

    if ($request->filled('date')) {
        return Carbon::parse($request->input('date'));
    }

    return null;
}
}