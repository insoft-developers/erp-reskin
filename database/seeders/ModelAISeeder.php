<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelAISeeder extends Seeder
{
    public function run()
    {
        DB::table('md_model_ais')->insert([
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant.',
                'created_at' => now(),
            ],
            [
                'role' => 'system',
                'content' => 'Tolong balas semua pertanyaan dengan bahasa Indonesia.',
                'created_at' => now(),
            ],
            [
                'role' => 'system',
                'content' => 'Tolong balas semua pertanyaan dengan penjelasan yang singkat tapi mewakili semua pertanyaan user.',
                'created_at' => now(),
            ]
        ]);
    }
}