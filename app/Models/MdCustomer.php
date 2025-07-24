<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdCustomer extends Model
{
    use HasFactory;

    protected $table = "md_customers";
    protected $guarded = ['id'];
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'kecamatan',
        'kelurahan',
        'alamat',
        'created',
        'user_id',
        'province_id',
        'city_id',
        'district_id',
    ];

    public function followup()
    {
        return FollowUp::orderBy('id', 'asc')->where('type', 'followup')->where('account_id', session('id'))->get();
    }

    public function upselling()
    {
        return FollowUp::orderBy('id', 'asc')->where('type', 'upselling')->where('account_id', session('id'))->get();
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
