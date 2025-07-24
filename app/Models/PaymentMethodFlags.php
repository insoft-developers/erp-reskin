<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodFlags extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'payment_method',
        'flag',
        'user_id'
    ];
}
