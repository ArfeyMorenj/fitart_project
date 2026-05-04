<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'master_item_product_id', 'item_code', 'item_name',
        'description', 'qty', 'unit', 'price', 'bruto', 'months'
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'bruto' => 'decimal:2',
        'months' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function masterItemProduct()
    {
        return $this->belongsTo(MasterItemProduct::class);
    }
    public function item()
{
    // item_code menyimpan kode item string -> items.code
    return $this->belongsTo(\App\Models\Item::class, 'item_code', 'code');
}


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->bruto = $item->qty * $item->price;
        });
    }
}
