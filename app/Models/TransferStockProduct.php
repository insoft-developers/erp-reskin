<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferStockProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function productFrom()
    {
        return $this->belongsTo(MdProduct::class, 'product_from_id', 'id');
    }

    public function productTo()
    {
        return $this->belongsTo(MdProduct::class, 'product_to_id', 'id');
    }
}
