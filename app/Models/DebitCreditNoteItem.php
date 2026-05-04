<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitCreditNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit_credit_note_id', 'item_code', 'item_name', 'description',
        'dpp_amount', 'ppn_amount'
    ];

    protected $casts = [
        'dpp_amount' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
    ];

    public function debitCreditNote()
    {
        return $this->belongsTo(DebitCreditNote::class);
    }
}
