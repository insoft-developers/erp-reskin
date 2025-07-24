<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferStockMaterial extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function materialFrom()
    {
        return $this->belongsTo(Material::class, 'material_from_id', 'id');
    }

    public function materialTo()
    {
        return $this->belongsTo(Material::class, 'material_to_id', 'id');
    }
}
