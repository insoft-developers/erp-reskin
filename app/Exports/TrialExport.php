<?php

namespace App\Exports;

use App\Models\Journal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TrialExport implements FromView
{
    
    public $details;
    public $awal;
    public $akhir;
    public $data;
    public function __construct($details, $awal, $akhir, $data) {
        $this->details = $details;
        $this->awal = $awal;
        $this->akhir = $akhir;
        $this->data = $data;
    }
    
    
    public function view(): View
    {
        return view('export.trial_balance', [
            'dt' => $this->details,
            'awal' => $this->awal,
            'akhir' => $this->akhir,
            'data' => $this->data
        ]);
    }
}
