<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'date',
        'date_install',
        'start_license',
        'client_id',
        'product_id',
        'version',
        'item_id',
        'status',
        'amount',
        'description',
        'item_count',
        'per_unit',
        'notes',
    ];

    protected $casts = [
        'date'          => 'date',
        'date_install'  => 'date',
        'start_license' => 'date',
        'amount'        => 'decimal:2',
        'item_count'    => 'integer',
        'is_active'     => 'boolean',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(WorkOrderTeam::class);
    }

    public function stopLicense(): BelongsTo
    {
        return $this->belongsTo(StopLicense::class);
    }

    // ─── Helper: ambil satu team member by role ───────────────────────────────

    public function getTeamByRole(string $role): ?WorkOrderTeam
    {
        return $this->teams->firstWhere('role', $role);
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'AKTIF';
    }

    public function isStopped(): bool
    {
        return $this->status === 'STOP';
    }
}
