<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Toko Cabang Manyar 1','address' => 'Jl Raya Manyar','phone' => '(031) 4676342','district_id' => 3578100,'account_id' => 13370],
            ['name' => 'Toko Cabang Gubeng','address' => 'Jl Raya Gubeng','phone' => '(031) 4676342','district_id' => 3578100,'account_id' => 13370],
            ['name' => 'Toko Cabang Benowo','address' => 'Jl Raya Benowo','phone' => '(031) 4676342','district_id' => 3578280,'account_id' => 13370]
        ];
        DB::table('branches')->insert($data);
    }
}
