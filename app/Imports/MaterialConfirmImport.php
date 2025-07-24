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

class MaterialConfirmImport implements ToModel, WithHeadingRow
{
    use CommonTrait;
    public function model(array $row)
    {
        
        $cek_supplier = Supplier::where('userid', $this->user_id_manage(session('id')))->whereRaw('UPPER(name) like?', [strtoupper($row['supplier'])]);
        if($cek_supplier->count() > 0) {
            $supplier = $cek_supplier->first()->id;
        } else {
            $sup = new Supplier;
            $sup->userid = $this->user_id_manage(session('id'));
            $sup->name = ucwords(strtolower($row['supplier']));
            $sup->save();
            $supplier = $sup->id;
        }

        $cek_category = MaterialCategory::whereRaw('UPPER(category_name) like?', [strtoupper($row['category'])]);
        if($cek_category->count() > 0) {
            $cat = $cek_category->first()->id;
        } else {
            $sup = new MaterialCategory;
            $sup->category_name = ucwords(strtolower($row['category']));
            $sup->save();
            $cat = $sup->id;
        }

        $cek_unit = Unit::whereRaw('UPPER(unit_name) like?', [strtoupper($row['satuan'])]);
        if($cek_unit->count() > 0) {
            $sat = $cek_unit->first()->unit_name;
        } else {
            $sat = "Unit (Satuan)";
        }
        
        return new Material([
            "userid" => $this->user_id_manage(session('id')),
            "material_name" => $row['material_name'],
            "sku" => $row['sku'],
            "category_id" => $cat,
            "description" => $row['description'],
            "supplier_id" => $supplier,
            "unit" => $sat,
            "stock" => 0,
            "cost" => 0,
            "min_stock" => 0,
            "ideal_stock" => 0,
            "is_deleted" => 0
        ]);
    }
}
