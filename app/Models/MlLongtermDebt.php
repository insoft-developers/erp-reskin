<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlLongtermDebt extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'ml_longterm_debt';
    public $timestamps = false;
}
