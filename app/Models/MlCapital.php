<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlCapital extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'ml_capital';
    public $timestamps = false;
}
