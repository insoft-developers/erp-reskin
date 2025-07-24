<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessGroup extends Model
{
    use HasFactory;

    protected $table = 'business_groups';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(MlBank::class, 'bank_id', 'id');
    }
    
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
}
