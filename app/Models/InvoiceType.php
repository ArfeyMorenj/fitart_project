<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'is_license', 'auto_create_number', 'is_active'
    ];

    protected $casts = [
        'is_license' => 'boolean',
        'auto_create_number' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
