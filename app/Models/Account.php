<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Account extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'ml_accounts';
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $fillable = [
        "uuid",
        "cs_id",
        "email",
        "username",
        "fullname",
        "password",
        "roles",
        "role_code",
        "status",
        "is_upgraded",
        "upgrade_expiry",
        "is_soft_delete",
        "recovery_code",
        "recovery_code_duration",
        "token",
        "created",
        "referal_source",
        "referal_code",
        "is_active",
        "start_date",
        "pin",
        "branch_id",
        "position_id",
        "phone",
        "user_key",
        "clock_in",
        "clock_out",
        "holiday",
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function business_group()
    {
        return $this->hasOne(BusinessGroup::class, 'user_id', 'id');
    }

    public function mlAccountInfo()
    {
        return $this->hasOne(MlAccountInfo::class, 'user_id', 'id');
    }
}
