<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;

class LaporanAbsensiExportByDate implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    protected $daysInMonth;

    public function __construct($data, $daysInMonth)
    {
        $this->data = $data;
        $this->daysInMonth = $daysInMonth;
    }

    public function view(): View
    {
        return view('main.report.attendance.exportByDate', ['data' => $this->data, 'daysInMonth' => $this->daysInMonth]);
    }
}
