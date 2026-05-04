<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'address', 'address_invoice', 'city', 'city_invoice',
        'phone', 'fax', 'email', 'website', 'npwp', 'npkp',
        'tax_name', 'tax_position', 'invoice_name', 'invoice_position',
        'invoice_name_2', 'invoice_position_2', 'invoice_tolerance_days',
        'upgrade_days', 'letterhead_top', 'letterhead_bottom'
    ];
}
