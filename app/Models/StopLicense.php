<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StopLicense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number', 'date', 'stop_date', 'work_order_id', 'client_id',
        'product_id', 'client_spv', 'client_spv_id', 'notes', 'is_stopped'
    ];

    protected $casts = [
        'date' => 'date',
        'stop_date' => 'date',
        'is_stopped' => 'boolean',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function clientSpv()
    {
        return $this->belongsTo(TeamMember::class, 'client_spv_id');
    }
}
