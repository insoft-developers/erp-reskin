<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Versi Gratis', 'price' => 0, 'frequency' => 'Monthly',],
            ['name' => 'Premium Bulanan', 'price' => 49000, 'frequency' => 'Monthly',],
            ['name' => 'Premium Tahunan', 'price' => 490000, 'frequency' => 'Yearly',]
        ];
        DB::table('subscription_plans')->insert($data);
    }
}
