<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterItemProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'unit', 'price', 'acc_omzet', 'acc_omzet_np', 'acc_piutang',
        'cdf_omzet', 'cdf_piutang', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
