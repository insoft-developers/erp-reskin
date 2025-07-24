<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlAccount extends Model
{
    use HasFactory;

    protected $table = 'ml_accounts';
    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'balance',
        'status_cashier',
        'popup_show',
    ];

    public function information()
    {
        return $this->hasMany(MlUserInformation::class, 'user_id', 'id');
    }
    public function categories()
    {
        return $this->hasMany(ProductCategory::class, 'user_id', 'id');
    }

    public function businessGroup()
    {
        return $this->hasOne(BusinessGroup::class, 'user_id', 'id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'account_id', 'id');
    }

    public function mlSettingUser()
    {
        return $this->hasOne(MlSettingUser::class, 'user_id', 'id');
    }

    public function mlAccountInfo()
    {
        return $this->hasOne(MlAccountInfo::class, 'user_id', 'id');
    }

    public function cs()
    {
        return $this->belongsTo(MdCustomerService::class, 'cs_id', 'id');
    }
}
