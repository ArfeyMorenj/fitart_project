<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\WorkOrderTeam;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    // ─── Role yang valid untuk team ──────────────────────────────────────────
    const TEAM_ROLES = [
        'implementor_1',
        'implementor_2',
        'programmer',
        'system_analyst',
        'supervisor',
        'client_spv',
    ];

    // ─── Label tampilan untuk setiap role ────────────────────────────────────
    const ROLE_LABELS = [
        'implementor_1'  => 'Implementator 1',
        'implementor_2'  => 'Implementator 2',
        'programmer'     => 'Programmer',
        'system_analyst' => 'System Analist',
        'supervisor'     => 'Supervisor',
        'client_spv'     => 'Klien Spv',
    ];

    // ─── Helper: format data work order beserta tim ───────────────────────────
    private function formatWorkOrder(WorkOrder $workOrder): array
    {
        $workOrder->load(['client', 'product', 'item', 'teams.teamMember']);

        $data = $workOrder->toArray();

        // Buat struktur team yang rapi per-role (mudah dibaca frontend)
        $teamByRole = [];
        foreach (self::TEAM_ROLES as $role) {
            $teamByRole[$role] = [
                'role'           => $role,
                'label'          => self::ROLE_LABELS[$role],
                'team_member_id' => null,
                'name'           => null,
                'code'           => null,
            ];
        }

        foreach ($workOrder->teams as $team) {
            if (isset($teamByRole[$team->role])) {
                $teamByRole[$team->role] = [
                    'role'           => $team->role,
                    'label'          => self::ROLE_LABELS[$team->role] ?? $team->role,
                    'team_member_id' => $team->team_member_id,
                    'name'           => $team->teamMember?->name,
                    'code'           => $team->teamMember?->code,
                ];
            }
        }

        $data['team'] = array_values($teamByRole);
        unset($data['teams']); // hapus array mentah, pakai yang sudah diformat

        return $data;
    }

    // ─── INDEX ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = WorkOrder::with(['client', 'product', 'item', 'teams.teamMember']);

        // Filter opsional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $workOrders = $query->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $workOrders->map(fn($wo) => $this->formatWorkOrder($wo)),
        ]);
    }

    // Search endpoint explicit: /api/work-orders/search?q=...
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $clientId = $request->query('client_id');

        $query = WorkOrder::with(['client', 'product', 'item', 'teams.teamMember']);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($clientId)) {
            $query->where('client_id', $clientId);
        }

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('number', 'like', "%{$q}%")
                    ->orWhereHas('client', function ($clientQuery) use ($q) {
                        $clientQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('code', 'like', "%{$q}%");
                    });
            });
        }

        $workOrders = $query->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $workOrders->map(fn($wo) => $this->formatWorkOrder($wo)),
        ]);
    }

    // ─── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Validasi role user
        $user = $request->user();
        if (!in_array($user->role, ['sales_operator', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only sales_operator or super_admin can create work order.',
            ], 403);
        }

        $validated = $request->validate([
            'number'        => 'nullable|string|unique:work_orders,number',
            'date'          => 'required|date',
            'date_install'  => 'required|date',
            'start_license' => 'nullable|date',
            'client_id'     => 'required|exists:clients,id',
            'product_id'    => 'nullable|exists:products,id',
            'version'       => 'nullable|string|max:50',
            'item_id'       => 'nullable|exists:items,id',
            'status'        => 'nullable|string',
            'amount'        => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'item_count'    => 'nullable|integer|min:0',
            'per_unit'      => 'nullable|string',
            'notes'         => 'nullable|string',

            // Team — opsional, bisa diisi sekaligus saat create
            'team'                           => 'nullable|array',
            'team.*.role'                    => 'required_with:team|in:' . implode(',', self::TEAM_ROLES),
            'team.*.team_member_id'          => 'required_with:team|exists:team_members,id',
        ]);

        // Auto-generate nomor WO jika tidak diisi
        if (empty($validated['number'])) {
            $validated['number'] = $this->generateNumber();
        }

        $teamData = $validated['team'] ?? [];
        unset($validated['team']);

        $workOrder = WorkOrder::create($validated);

        // Simpan team jika ada
        if (!empty($teamData)) {
            $this->syncTeam($workOrder, $teamData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Work Order created successfully',
            'data'    => $this->formatWorkOrder($workOrder),
        ], 201);
    }

    // ─── SHOW ─────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $workOrder = WorkOrder::with(['client', 'product', 'item', 'teams.teamMember'])
            ->find($id);

        if (!$workOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Work Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatWorkOrder($workOrder),
        ]);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────
    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'number'        => 'required|string|unique:work_orders,number,' . $workOrder->id,
            'date'          => 'required|date',
            'date_install'  => 'required|date',
            'start_license' => 'nullable|date',
            'client_id'     => 'required|exists:clients,id',
            'product_id'    => 'nullable|exists:products,id',
            'version'       => 'nullable|string|max:50',
            'item_id'       => 'nullable|exists:items,id',
            'status'        => 'nullable|string',
            'amount'        => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'item_count'    => 'nullable|integer|min:0',
            'per_unit'      => 'nullable|string',
            'notes'         => 'nullable|string',

            // Team bisa diupdate sekaligus
            'team'                           => 'nullable|array',
            'team.*.role'                    => 'required_with:team|in:' . implode(',', self::TEAM_ROLES),
            'team.*.team_member_id'          => 'required_with:team|nullable|exists:team_members,id',
        ]);

        $teamData = $validated['team'] ?? null;
        unset($validated['team']);

        $workOrder->update($validated);

        // Update team jika dikirim
        if ($teamData !== null) {
            $this->syncTeam($workOrder, $teamData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Work Order updated successfully',
            'data'    => $this->formatWorkOrder($workOrder->fresh()),
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $workOrder = WorkOrder::withTrashed()
            ->where('id', $id)
            ->orWhere('number', $id)
            ->first();

        if (!$workOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Work Order not found',
            ], 404);
        }

        if ($workOrder->trashed()) {
            // Sudah di-soft delete sebelumnya → restore
            $workOrder->restore();
            return response()->json([
                'success' => true,
                'message' => 'Work Order restored successfully',
                'data'    => $this->formatWorkOrder($workOrder->fresh()),
            ]);
        }

        $workOrder->delete(); // soft delete

        return response()->json([
            'success' => true,
            'message' => 'Work Order deleted successfully',
        ]);
    }

    // ─── ASSIGN TEAM (endpoint terpisah) ──────────────────────────────────────
    /**
     * POST /work-orders/{work_order}/assign-team
     *
     * Contoh body:
     * {
     *   "team": [
     *     { "role": "implementor_1",  "team_member_id": 1 },
     *     { "role": "programmer",     "team_member_id": 2 },
     *     { "role": "supervisor",     "team_member_id": 3 },
     *     { "role": "client_spv",     "team_member_id": null }  ← kosongkan role ini
     *   ]
     * }
     */
    public function assignTeam(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'team'                  => 'required|array|min:1',
            'team.*.role'           => 'required|in:' . implode(',', self::TEAM_ROLES),
            'team.*.team_member_id' => 'nullable|exists:team_members,id',
        ]);

        $this->syncTeam($workOrder, $request->team);

        return response()->json([
            'success' => true,
            'message' => 'Team assigned successfully',
            'data'    => $this->formatWorkOrder($workOrder->fresh()),
        ]);
    }

    // ─── PRIVATE HELPERS ──────────────────────────────────────────────────────

    /**
     * Sinkronisasi team — hapus role lama, isi role baru.
     * Role yang team_member_id-nya null akan dihapus.
     */
    private function syncTeam(WorkOrder $workOrder, array $teamData): void
    {
        foreach ($teamData as $item) {
            $role           = $item['role'];
            $teamMemberId   = $item['team_member_id'] ?? null;

            // Hapus entry lama untuk role ini
            $workOrder->teams()->where('role', $role)->delete();

            // Isi hanya kalau team_member_id tidak null
            if ($teamMemberId) {
                $workOrder->teams()->create([
                    'team_member_id' => $teamMemberId,
                    'role'           => $role,
                ]);
            }
        }
    }

    /**
     * Generate nomor WO otomatis dengan format: WO-{bulan}{tahun}-{urutan}
     * Contoh: WO-032026-0001
     */
    private function generateNumber(): string
    {
        $prefix  = 'WO-' . now()->format('mY') . '-';
        $last    = WorkOrder::where('number', 'like', $prefix . '%')
                        ->orderBy('number', 'desc')
                        ->value('number');

        $lastNum = $last ? (int) substr($last, strlen($prefix)) : 0;

        return $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    }
}
