<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['category_name' => 'Catat Keuangan Pribadi', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Restoran', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Online', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Produsen Fashion & Aksesoris', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Produsen Food & Beverage', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Produsen Produk Rumah Tangga', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Produsen Herbal & Kecantikan', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Minimarket', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Butik', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Warung', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Handphone', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Komputer', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Elektronik', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Vape Store', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Salon / Barber', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Laundry', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Spa / Massage', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Peralatan Bayi & Anak', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Katering', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Cepat Saji', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Gym / Fitness / Pusat Kebugaran', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Kedai Kopi / Cafe', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Petshop', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Material Bangunan', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Roti', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Sembako', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Serba Ada', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Waralaba Makanan', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Waralaba Minuman', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Kosmetik', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Sepatu', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Aksesoris', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Toko Mainan', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Import / Export', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Jasa / Profesional', 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Lain Lain', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('categories')->insert($data);
    }
}
