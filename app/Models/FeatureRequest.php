<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureRequest extends Model
{
    use HasFactory;
    protected $table = 'feature_request';

    protected $fillable = ['title', 'detail', 'category_id', 'user_id'];

    public function category()
    {
        return $this->belongsTo(FeatureRequestCategories::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(FeatureRequestImages::class, 'request_id');
    }
}
