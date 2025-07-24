<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branches';
    protected $fillable = ['id', 'name', 'address', 'phone', 'district_id', 'account_id'];

    public function user()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
