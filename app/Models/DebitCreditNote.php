<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebitCreditNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'number', 'date', 'invoice_id', 'client_id', 'description',
        'dpp_amount', 'ppn_amount', 'total_amount', 'auto_journal', 'is_posted'
    ];

    protected $casts = [
        'date' => 'date',
        'dpp_amount' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'auto_journal' => 'boolean',
        'is_posted' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(DebitCreditNoteItem::class);
    }
}
