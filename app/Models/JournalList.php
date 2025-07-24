<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalList extends Model
{
    use HasFactory;
    protected $table = "ml_journal_list";
    public $timestamps = false;

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id', 'id');
    }
}
