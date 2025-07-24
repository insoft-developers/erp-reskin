<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappCrmTemplate extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_crm_templates';
    protected $fillable = [
        'owner_id',
        'template_data',
    ];
    protected $casts = [
        'template_data' => 'array',
    ];
}
