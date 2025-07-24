<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MtRekapitulasiHarian extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function kasKecil()
    {
        return $this->belongsTo(MtKasKecil::class, 'mt_kas_kecil_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }
}
