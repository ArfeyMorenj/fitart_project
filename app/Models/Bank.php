<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'account_number', 'account_name', 'type',
        'acc_code', 'cdf_code', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function postingPayments()
    {
        return $this->hasMany(PostingPayment::class);
    }
}
