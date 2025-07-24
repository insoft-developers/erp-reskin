<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $position = [
            ['position' => 'Keuangan', 'remarks' => 'Keuangan'],
            ['position' => 'Manager', 'remarks' => 'Manager'],
            ['position' => 'Supervisor', 'remarks' => 'Supervisor'],
        ];
        DB::table('staff_positions')->insert($position);
    }
}
