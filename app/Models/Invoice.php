<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number', 'invoice_bm_km', 'invoice_bm_km_date', 'date', 'due_date', 'tax_date', 'tax_number',
        'tax_note', 'invoice_note',
        'invoice_type_id', 'client_id', 'bank_id', 'client_address', 'description',
        'invoice_category', 'invoice_mode', 'tax_type', 'instance', 'bruto', 'discount', 'dpp',
        'ppn', 'ppn_percentage', 'dp', 'other', 'total', 'include_ppn',
        'use_old_letterhead', 'without_payment_posting', 'stamp_and_signature',
        'auto_journal', 'pass_protelasi', 'is_paid',
        'is_posted', 'posted_date','work_order_id',
  'period'
    ];

    protected $casts = [
        'date' => 'date',
        'invoice_bm_km_date' => 'date',
        'due_date' => 'date',
        'tax_date' => 'date',
        'posted_date' => 'date',
        'bruto' => 'decimal:2',
        'discount' => 'decimal:2',
        'dpp' => 'decimal:2',
        'ppn' => 'decimal:2',
        'ppn_percentage' => 'decimal:2',
        'dp' => 'decimal:2',
        'other' => 'decimal:2',
        'total' => 'decimal:2',
        'include_ppn' => 'boolean',
        'use_old_letterhead' => 'boolean',
        'without_payment_posting' => 'boolean',
        'stamp_and_signature' => 'boolean',
        'auto_journal' => 'boolean',
        'pass_protelasi' => 'boolean',
        'is_paid' => 'boolean',
        'is_posted' => 'boolean',
    ];

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function postingPaymentInvoices()
    {
        return $this->hasMany(PostingPaymentInvoice::class);
    }

    public function receivable()
    {
        return $this->hasOne(Receivable::class);
    }

    public function debitCreditNotes()
    {
        return $this->hasMany(DebitCreditNote::class);
    }
    public function workOrder()
    {
    return $this->belongsTo(WorkOrder::class);
    }

}
