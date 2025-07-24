<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_method_flags')->insert([
            [
                'payment_method' => 'bank-bca',
                'flag' => 'EDC'
            ],
            [
                'payment_method' => 'bank-bca',
                'flag' => 'Rekening Pribadi'
            ],
            [
                'payment_method' => 'bank-bca',
                'flag' => 'QRIS'
            ],
            [
                'payment_method' => 'bank-mandiri',
                'flag' => 'EDC'
            ],
            [
                'payment_method' => 'bank-mandiri',
                'flag' => 'Rekening Pribadi'
            ],
            [
                'payment_method' => 'bank-mandiri',
                'flag' => 'QRIS'
            ],
            [
                'payment_method' => 'bank-bni',
                'flag' => 'EDC'
            ],
            [
                'payment_method' => 'bank-bni',
                'flag' => 'Rekening Pribadi'
            ],
            [
                'payment_method' => 'bank-bni',
                'flag' => 'QRIS'
            ],
            [
                'payment_method' => 'bank-bri',
                'flag' => 'EDC'
            ],
            [
                'payment_method' => 'bank-bri',
                'flag' => 'Rekening Pribadi'
            ],
            [
                'payment_method' => 'bank-bri',
                'flag' => 'QRIS'
            ],
            [
                'payment_method' => 'bank-lain',
                'flag' => 'EDC'
            ],
            [
                'payment_method' => 'bank-lain',
                'flag' => 'Rekening Pribadi'
            ],
            [
                'payment_method' => 'bank-lain',
                'flag' => 'QRIS'
            ]
        ]);
    }
}
