<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtectionTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type', 'document_number', 'document_date', 'is_protected',
        'protected_until', 'unlocked_by', 'unlocked_at'
    ];

    protected $casts = [
        'document_date' => 'date',
        'is_protected' => 'boolean',
        'protected_until' => 'date',
        'unlocked_at' => 'datetime',
    ];

    public function unlockedBy()
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }
}
