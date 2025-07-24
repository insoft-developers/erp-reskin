<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Traits\CommonTrait;
use App\Traits\JournalTrait;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialImport implements ToModel, WithHeadingRow
{
    private $total = 0;
    public function model(array $row)
    {
        $this->total = $this->total + 1;
       
    }

    public function get_total() {
        return $this->total;
    }
}
