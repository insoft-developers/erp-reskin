<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlIncome extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'ml_income';
    public $timestamps = false;
}
