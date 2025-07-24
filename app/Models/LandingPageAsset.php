<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageAsset extends Model
{
    use HasFactory;
    protected $table = 'landing_page_assets';

    protected $fillable = [
        'user_id',
        'path',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(MlAccount::class, 'user_id');
    }
}
