<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class owner_detail_users extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'user_id'];
}
