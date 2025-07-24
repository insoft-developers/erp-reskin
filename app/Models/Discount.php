<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = "discounts";
    protected $guarded = ['id'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function logDiscountUse()
    {
        return $this->hasMany(LogDiscountUse::class, 'discount_id', 'id');
    }
}
