<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function province()
    {
        return $this->belongsTo(RoProvince::class, 'province_id', 'province_id');
    }

    public function city()
    {
        return $this->belongsTo(RoCity::class, 'city_id', 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(RoDistrict::class, 'district_id', 'subdistrict_id');
    }

    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }

    public function transactionDetail()
    {
        return $this->hasMany(TransactionProductDetail::class, 'transaction_product_id', 'id');
    }
}
