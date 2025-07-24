<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }
    
    public function invoiceDetail()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(MdCurrency::class, 'currency_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(MdClient::class, 'client_id', 'id');
    }

    public function termin()
    {
        return $this->hasMany(InvoiceTermin::class, 'invoice_id', 'id');
    }
}
