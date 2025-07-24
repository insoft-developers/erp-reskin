<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlGaji extends Model
{
    use HasFactory;

    protected $table = 'ml_gaji';

    protected $guarded = ['id'];
    protected $casts = [
        'penerimaan' => 'array',
        'potongan' => 'array',
    ];

    public function marketing()
    {
        return $this->belongsTo(MlMarketing::class, 'marketing_id', 'id');
    }
}
