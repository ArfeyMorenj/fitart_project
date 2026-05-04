<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostingPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number', 'date', 'bank_id', 'acc_code', 'cdf_code', 'client_id',
        'description', 'total_invoice', 'debet', 'kredit', 'total_paid',
        'print_receipt', 'auto_journal', 'without_stamp', 'is_posted'
    ];

    protected $casts = [
        'date' => 'date',
        'total_invoice' => 'decimal:2',
        'debet' => 'decimal:2',
        'kredit' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'print_receipt' => 'boolean',
        'auto_journal' => 'boolean',
        'without_stamp' => 'boolean',
        'is_posted' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(PostingPaymentInvoice::class);
    }

    public function costs()
    {
        return $this->hasMany(PostingPaymentCost::class);
    }
}
