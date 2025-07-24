<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanProductVarian extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function variant() {
        return $this->belongsTo(MdProductVariant::class, 'varian_id', 'id');
    }
}
