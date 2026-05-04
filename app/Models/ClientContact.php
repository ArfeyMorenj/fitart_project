<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'name', 'phone', 'email', 'position'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
