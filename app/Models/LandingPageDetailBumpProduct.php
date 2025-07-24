<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageDetailBumpProduct extends Model
{
    use HasFactory;
    protected $table = 'landing_page_detail_bump_products';

    protected $fillable = [
        'landing_page_id',
        'product_id',
        'custom_name',
        'custom_photo',
        'discount',
        'title',
        'description',
    ];

    public function landing_page()
    {
        return $this->belongsTo(LandingPage::class, 'id', 'landing_page_id');
    }

    public function product()
    {
        return $this->belongsTo(MdProduct::class, 'product_id');
    }
}
