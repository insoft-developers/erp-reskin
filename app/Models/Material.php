<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;
    protected $table = "md_materials";

    protected $guarded = ['id'];


    public function material_category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id', 'id');
    }

    public function supplier(): BelongsTo 
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
