<?php

namespace Database\Seeders;

use App\Models\MaterialCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterilCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ["Kayu", "Besi", "Karet", "Plastik", "Kaca", "Keramik", "Batu", "Pasir", "Tanah liat", "Logam", "Serat alam", "Serat sintetis", "Bahan kimia", "Minyak bumi", "Gas alam", "Bahan baku nabati", "Bahan baku hewani", "Mineral", "Air", "Udara", "Bahan organik", "Bahan anorganik", "Kain", "Kapas", "Kulit", "Lateks", "Lemak", "Protein", "Karbohidrat", "Vitamin", "Mineral", "Zat warna", "Pigmen", "Lain-lain"];

        foreach($data as $d) {
            MaterialCategory::create([
                'category_name' => $d
            ]);
        }

    }
}
