<?php

namespace App\Exports;

use App\Models\Journal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BalanceExport implements FromView
{
    
    public $details;
    public $start;
    public $time_array;
    public $periode;
    public $laba;
    public function __construct($details, $start, $time_array, $periode, $laba) {
        $this->details = $details;
        $this->start = $start;
        $this->time_array = $time_array;
        $this->periode = $periode;
        $this->laba = $laba;
    }
    
    
    public function view(): View
    {
        return view('export.balance_sheet', [
            'dt' => $this->details,
            'start' => $this->start,
            'time_array' => $this->time_array,
            'periode' => $this->periode,
            'laba_bersih' => $this->laba
        ]);
    }
}
