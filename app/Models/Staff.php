<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasFactory,SoftDeletes;

    protected $guard = 'staff';
    protected $fillable = ['branch_id','position_id','name','username','password','phone','start_date','is_active','created_at','updated_at','role_id','role_code','pin'];
    protected $hidden = [
        'password',
    ];
    protected $table = 'staffs';
    public function position() : BelongsTo
    {
        return $this->belongsTo(StaffPosition::class,'position_id','id');
    }

    public function branch() : BelongsTo
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    
}
