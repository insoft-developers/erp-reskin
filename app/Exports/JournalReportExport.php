<?php

namespace App\Exports;

use App\Models\Journal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JournalReportExport implements FromView
{
    
    public $data;
    public $awal;
    public $akhir;

    public function __construct($data, $awal, $akhir) {
        $this->data = $data;
        $this->awal = $awal;
        $this->akhir = $akhir;
        
    }
    
    
    public function view(): View
    {
        return view('export.journal_report', [
            'data' => $this->data,
            'awal' => $this->awal,
            'akhir' => $this->akhir
            
        ]);
    }
}
