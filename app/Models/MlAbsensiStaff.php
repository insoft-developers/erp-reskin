<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlAbsensiStaff extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'contact' => 'array',
        'note' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(MlAccount::class, 'account_id', 'id');
    }
}
