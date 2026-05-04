<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'acc_omzet', 'acc_piutang',
        'cdf_omzet', 'cdf_piutang', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
    public function invoiceItems()
{
    return $this->hasMany(InvoiceItem::class, 'master_item_product_id');
}

    
}
