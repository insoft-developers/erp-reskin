<?php

namespace App\Imports;

use App\Models\MaterialCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Traits\CommonTrait;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    private $total = 0;
    public function model(array $row)
    {
        $this->total = $this->total + 1;
        
    }

    public function get_total()
    {
        return $this->total;
    }
}
