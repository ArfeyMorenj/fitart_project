<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostingPaymentCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'posting_payment_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function postingPayment()
    {
        return $this->belongsTo(PostingPayment::class);
    }
}
