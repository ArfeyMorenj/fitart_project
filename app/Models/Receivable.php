<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'period', 'invoice_id', 'client_id', 'client_code', 'client_name',
        'beginning_balance', 'netto', 'ppn', 'biaya', 'nota_debet',
        'payment', 'ending_balance', 'status'
    ];

    protected $casts = [
        'beginning_balance' => 'decimal:2',
        'netto' => 'decimal:2',
        'ppn' => 'decimal:2',
        'biaya' => 'decimal:2',
        'nota_debet' => 'decimal:2',
        'payment' => 'decimal:2',
        'ending_balance' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
