<?php

namespace Database\Seeders;

use App\Models\MlBank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MlBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'bank_name' => 'Bank BCA',
                'flip_code' => 'bca',
                'bank_code' => '014',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'Bank BRI',
                'flip_code' => 'bri',
                'bank_code' => '002',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'Bank BNI',
                'flip_code' => 'bni',
                'bank_code' => '009',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'flip_code' => 'mandiri',
                'bank_code' => '008',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'Bank BSI',
                'flip_code' => 'bsm',
                'bank_code' => '451',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'CIMB Niaga',
                'flip_code' => 'cimb',
                'bank_code' => '022',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'DBS Indonesia',
                'flip_code' => 'dbs',
                'bank_code' => '046',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'Dana',
                'flip_code' => 'dana',
                'bank_code' => '1012',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'Gopay',
                'flip_code' => 'gopay',
                'bank_code' => '1011',
                'vendor' => 'duitku',
            ],
            [
                'bank_name' => 'OVO',
                'flip_code' => 'ovo',
                'bank_code' => '1010',
                'vendor' => 'duitku',
            ],
        ];

        $bank = MlBank::insert($data);
    }
}
