<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'filled_date', 'period_start', 'period_end', 'sequence', 'prefix',
        'tax_period', 'tax_year', 'tax_code', 'start_number', 'end_number', 'last_number',
        'ppn_percentage', 'dpp_percentage'
    ];

    protected $casts = [
        'filled_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'sequence' => 'integer',
    ];

    public function getNextNumber()
    {
        $this->increment('sequence');
        $this->last_number = $this->prefix . str_pad($this->sequence, 3, '0', STR_PAD_LEFT);
        $this->save();

        return $this->last_number;
    }
}
