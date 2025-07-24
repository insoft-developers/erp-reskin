<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasFactory;
    protected $table = 'ml_journal';
    public $timestamps = false;


    public function journal_list():HasMany
    {
        return $this->hasMany(JournalList::class, 'journal_id', 'id');
    }
}
