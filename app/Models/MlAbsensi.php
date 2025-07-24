<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlAbsensi extends Model
{
    use HasFactory;

    protected $table = 'ml_absensis';

    protected $guarded = ['id'];
    protected $casts = [
        'contact' => 'array',
        'resistance' => 'array',
    ];

    public function marketing()
    {
        return $this->belongsTo(MlMarketing::class, 'marketing_id', 'id');
    }
}
