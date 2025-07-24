<?php

namespace App\Exports;

use App\Models\Journal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GeneralLedgerExport implements FromView
{
    
    public $estimations;
    public $awal;
    public $akhir;

    public function __construct($estimations, $awal, $akhir) {
        $this->estimations = $estimations;
        $this->awal = $awal;
        $this->akhir = $akhir;
        
    }
    
    
    public function view(): View
    {
        return view('export.general_ledger', [
            'estimasi' => $this->estimations,
            'awal' => $this->awal,
            'akhir' => $this->akhir
            
        ]);
    }
}
