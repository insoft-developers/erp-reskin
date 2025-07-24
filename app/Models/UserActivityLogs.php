<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLogs extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'page', 'is_mobile'];
}
