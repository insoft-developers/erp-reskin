<?php

namespace App\Imports;

use App\Models\OpnameItem;
use App\Models\OpnameTrash;
use App\Traits\CommonTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OpnameImport implements ToModel, WithHeadingRow
{
    use CommonTrait;
    private $total_fisik = 0;
    private $total_nilai_fisik = 0;
    private $total_selisih = 0;
    private $total_nilai_selisih = 0;
    
    public function model(array $row)
    {
        if ($row['stok_fisik'] == null || $row['opname_id'] == null || $row['id'] == null) {
            return null;
        }

        $nilai_fisik = $row['stok_fisik'] == null || $row['hpp'] == null ? 0 : (int) $row['stok_fisik'] * (int) $row['hpp'];
        $selisih = ( $row['stok_fisik'] == null ? (int) $row['stok'] : (int) $row['stok_fisik'] - (int) $row['stok']);


        $this->total_fisik = $this->total_fisik + $row['stok_fisik'];
        $this->total_nilai_fisik = $this->total_nilai_fisik + $nilai_fisik;
        $this->total_selisih = $this->total_selisih + $selisih;
        $this->total_nilai_selisih = $this->total_nilai_selisih + ($selisih * $row['hpp']); 

        $data = [
            'physical_quantity' => $row['stok_fisik'],
            'physical_total_value' => $nilai_fisik,
            'selisih' => $selisih
        ];

        DB::table('opname_items')->where('id', $row['id'])->update($data);

        return new OpnameTrash([
            'opname_id' => 1,
            'status' => 1,
        ]);
    }

    public function get_total() {
        $data['total_fisik'] = $this->total_fisik;
        $data['total_nilai_fisik'] = $this->total_nilai_fisik;
        $data['total_selisih'] = $this->total_selisih;
        $data['total_nilai_selisih'] = $this->total_nilai_selisih;

        return $data;
    }
}
