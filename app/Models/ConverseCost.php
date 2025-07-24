<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConverseCost extends Model
{
    use HasFactory;

    protected $table = 'ml_converse_costs';

    protected $guarded = ['id'];
}
