<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        Unit::create([
            'unit_name' => 'Unit (Satuan)'
        ]);

        Unit::create([
            'unit_name' => 'Kilogram (Kg)'
        ]);

        Unit::create([
            'unit_name' => 'Gram (g)' 
        ]);

        Unit::create([
            'unit_name' => 'Liter (L)'
        ]);

        Unit::create([
            'unit_name' => 'Mililiter (ml)'
        ]);

        Unit::create([
            'unit_name' => 'Meter (m)'
        ]);

        Unit::create([
            'unit_name' => 'Centimeter (cm)'
        ]);

        Unit::create([
            'unit_name' => 'Pieces (pcs)'
        ]);

        Unit::create([
            'unit_name' => 'Pack (Paket)'
        ]);

        Unit::create([
            'unit_name' => 'Box (Kotak)'
        ]);

        Unit::create([
            'unit_name' => 'Dozen (Lusin)'
        ]);

        Unit::create([
            'unit_name' => 'Pair (Pasang)'
        ]);

        Unit::create([
            'unit_name' => 'Roll (Gulung)'
        ]);

        Unit::create([
            'unit_name' => 'Set'
        ]);

        Unit::create([
            'unit_name' => 'Bag (Kantong)'
        ]);

        Unit::create([
            'unit_name' => 'Bottle (Botol)'
        ]);

        Unit::create([
            'unit_name' => 'Jar (Toples)'
        ]);

        Unit::create([
            'unit_name' => 'Carton (Karton)'
        ]);

        Unit::create([
            'unit_name' => 'Bundle (Ikat)'
        ]);

        Unit::create([
            'unit_name' => 'Tray (Nampan)'
        ]);
    }
}
