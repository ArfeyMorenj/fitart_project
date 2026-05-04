<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'logo',
        'company_name',
        'address',
        'city',
        'phone',
        'npwp',
        'period_start',
        'acc_ppn_kes',
        'acc_ppn_mas',
        'acc_discount',
        'bank1',
        'bank1_sn',
        'bank1_ac',
        'bank2',
        'bank2_sn',
        'bank2_ac',
    ];
}
