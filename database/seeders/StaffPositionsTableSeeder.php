<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffPositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertPositionStaff();
    }

    private function insertPositionStaff()
    {
        $position = [
            ['position' => 'Kasir', 'remarks' => 'Kasir'],
            ['position' => 'Administratif', 'remarks' => 'Administratif'],
            ['position' => 'Sales / Marketing', 'remarks' => 'Sales / Marketing'],
            ['position' => 'Dapur', 'remarks' => 'Dapur'],
            ['position' => 'Gudang', 'remarks' => 'Gudang']
        ];

        DB::table('staff_positions')->insert($position);
    }
}
