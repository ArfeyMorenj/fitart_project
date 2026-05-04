<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostingOmzet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number', 'date', 'acc_code', 'sequence', 'description',
        'debit', 'credit', 'is_posted'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'is_posted' => 'boolean',
    ];
}
