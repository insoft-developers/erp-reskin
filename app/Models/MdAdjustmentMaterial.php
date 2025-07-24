<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdAdjustmentMaterial extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function md_adjustment()
    {
        return $this->belongsTo(MdAdjustment::class, 'adjustment_id', 'id');
    }

    public function md_material()
    {
        return $this->belongsTo(Material::class, 'md_material_id', 'id');
    }
}
