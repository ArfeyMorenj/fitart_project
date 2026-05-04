<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'specification', 'description', 'author_code',
        'author_name', 'compiler', 'year', 'product_group_id', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function productGroup()
{
    return $this->belongsTo(ProductGroup::class, 'product_group_id');
}

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function stopLicenses()
    {
        return $this->hasMany(StopLicense::class);
    }
    
}
