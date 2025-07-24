<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlUserInformation extends Model
{
    use HasFactory;

    protected $table = 'ml_user_information';

    public function user()
    {
        return $this->belongsTo(MlAccount::class, 'user_id');
    }
}
