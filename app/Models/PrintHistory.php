<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'report_type', 'document_number',
        'parameters', 'printed_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'printed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
