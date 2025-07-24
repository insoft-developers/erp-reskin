<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterCategory extends Model
{
    use HasFactory;
    protected $table = 'md_inter_categories';
    protected $guarded = ['id'];
}
