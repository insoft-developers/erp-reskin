<?php

namespace Database\Seeders;

use App\Models\InterCategory;
use App\Models\InterProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [ "Papan kayu", "Balok kayu", "Triplek", "Besi beton", "Plat besi", "Pipa PVC", "Karet vulkanisir", "Granul plastik", "Botol kaca", "Keramik lantai", "Keramik dinding", "Bata merah", "Batu bata ringan", "Genteng", "Beton pracetak", "Serat karbon", "Serat kaca", "Tekstil", "Benang", "Kulit sintetis", "Baja tahan karat", "Aluminium foil", "Kabel", "Papan partikel", "Plywood", "MDF (Medium Density Fiberboard)", "Kulit tanned", "Lem", "Pewarna tekstil", "Komponen elektronik", "Polimer sintetis", "Kertas", "Karton", "Komposit", "Semen", "Aspal", "Kawat", "Mesh logam", "Ferroalloy", "Alloy non-ferrous", "Baja lembaran", "Profil baja", "Lain-Lain"];

        foreach($data as $d) {
            InterCategory::create([
                "inter_category" => $d
            ]);
        }
    }
}
