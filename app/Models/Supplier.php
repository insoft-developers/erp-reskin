<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = 'md_suppliers';

    protected $fillable = ['userid', 'name', 'contact_name', 'phone', 'email', 'fax', 'website', 'jalan1', 'jalan2', 'postal_code', 'province', 'country','can_be_deleted'];
}
