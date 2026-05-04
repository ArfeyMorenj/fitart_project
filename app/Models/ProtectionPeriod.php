<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtectionPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'period', 'is_protected', 'protected_at', 'protected_by'
    ];

    protected $casts = [
        'is_protected' => 'boolean',
        'protected_at' => 'date',
    ];

    public function protectedBy()
    {
        return $this->belongsTo(User::class, 'protected_by');
    }
}
