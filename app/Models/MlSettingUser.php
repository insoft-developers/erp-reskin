<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlSettingUser extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'printer_connection',
        'printer_paper_size',
        'printer_custom_footer',
        'is_rounded',
    ];

    public function user()
    {
        return $this->belongsTo(MlAccount::class, 'user_id', 'id');
    }
}
