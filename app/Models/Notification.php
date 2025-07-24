<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = "notifications";
    protected $guarded = ['id'];

    public function penulis()
    {
        return $this->belongsTo(MlAccount::class, 'author', 'id');
    }
}
