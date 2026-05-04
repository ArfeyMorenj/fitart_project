<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostingPaymentInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'posting_payment_id', 'invoice_id', 'client_id', 'amount', 'ppn'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'ppn' => 'decimal:2',
    ];

    public function postingPayment()
    {
        return $this->belongsTo(PostingPayment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
