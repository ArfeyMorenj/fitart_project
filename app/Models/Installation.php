<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Installation extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'client_id',
        'install_date',
        'implementor_1',
        'implementor_2',
        'implementor_3',
        'notes',
    ];

    public function workOrder() {
        return $this->belongsTo(WorkOrder::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
