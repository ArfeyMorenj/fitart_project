<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'type', 'category', 'level',
        'parent_code', 'is_header', 'is_active'
    ];

    protected $casts = [
        'is_header' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_code', 'code');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_code', 'code');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'acc_code', 'code');
    }
}
