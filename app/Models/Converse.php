<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Converse extends Model
{
    use HasFactory;


    protected $table = 'ml_converses';
    protected $guarded = ['id'];

    public function material() : BelongsTo {
        return $this->belongsTo(Material::class, 'product_id', 'id');      
    }


    public function inter() : BelongsTo {
        return $this->belongsTo(InterProduct::class, 'product_id', 'id');      
    }


    public function converse_item(): HasMany{
        return $this->hasMany(ConverseItem::class, 'converse_id', 'id');
    }

    public function cost(): HasMany{
        return $this->hasMany(ConverseCost::class, 'converse_id','id');
    }

   
}
