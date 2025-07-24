<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class MlMarketing extends Model
{
    use HasApiTokens, HasFactory;

    protected $table = 'ml_marketings';

    protected $guarded = ['id'];
    protected $casts = [
        'bank_account' => 'array',
    ];

    public function account()
    {
        return $this->hasMany(MlAccount::class, 'referal_source', 'referal_source');
    }

    public function absensis()
    {
        return $this->hasMany(MlAbsensi::class, 'marketing_id', 'id');
    }

    public function gaji()
    {
        return $this->hasMany(MlGaji::class, 'marketing_id', 'id');
    }
}
