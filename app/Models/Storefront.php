<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storefront extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'store_address',
        'payment_method',
        'shipping',
        'banner',
        'template',
        'banner_image1',
        'banner_link1',
        'banner_image2',
        'banner_link2',
        'banner_image3',
        'banner_link3',
        'delivery',
    ];
}
