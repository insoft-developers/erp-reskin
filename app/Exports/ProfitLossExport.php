<?php

namespace App\Exports;

use App\Models\Journal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProfitLossExport implements FromView
{
    
    public $details;
    public $time_array;
    public $periode;
    public $start;
    public function __construct($details, $time_array, $periode, $start) {
        $this->details = $details;
        $this->time_array = $time_array;
        $this->periode = $periode;
        $this->start = $start;
    }
    
    
    public function view(): View
    {
        return view('export.profit_loss', [
            'data' => $this->details,
            'time_array' => $this->time_array,
            'periode' => $this->periode,
            'start' => $this->start
        ]);
    }
}
