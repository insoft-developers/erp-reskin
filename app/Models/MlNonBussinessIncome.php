<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlNonBussinessIncome extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'ml_non_business_income';
    public $timestamps = false;
}
