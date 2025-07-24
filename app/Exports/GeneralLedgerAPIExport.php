<?php

namespace App\Exports;

use App\Models\Journal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GeneralLedgerAPIExport implements FromView
{
    
    public $estimations;
    public $awal;
    public $akhir;
    public $userid;

    public function __construct($estimations, $awal, $akhir, $userid) {
        $this->estimations = $estimations;
        $this->awal = $awal;
        $this->akhir = $akhir;
        $this->userid = $userid;
        
    }
    
    
    public function view(): View
    {
        return view('export.general_ledger2', [
            'estimasi' => $this->estimations,
            'awal' => $this->awal,
            'akhir' => $this->akhir,
            'userid' => $this->userid
            
        ]);
    }
}
