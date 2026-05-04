<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'status', 'name', 'address', 'city', 'phone', 'fax',
        'npwp', 'npkp', 'tax_name', 'tax_address', 'credit_term_days', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_term_days' => 'integer',
    ];

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function stopLicenses()
    {
        return $this->hasMany(StopLicense::class);
    }

    public function debitCreditNotes()
    {
        return $this->hasMany(DebitCreditNote::class);
    }
  public function licenses()
{
    return $this->hasMany(\App\Models\License::class);
}

}
