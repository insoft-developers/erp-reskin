<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('md_message_templates')->insert([
            [
                'key' => 'order_in',
                'title' => 'Orderan Masuk',
                'template' => 'Halo Kak {name}! 🌟 Ada info nih, saya {customerservice} belum lihat transfer untuk pesanan {productname}. Ada yang mau kakak tanyakan soal produknya? 🤔 {customerservice} siap bantu. Berikut link invoice bisa anda lihat di {link_struk}',
                'info' => 'Silahkan ketik parameter berikut untuk menambahkan data user {customer_name}, {customer_phone}, {productname}, {customerservice}, {link_struk}',
                'created_at' => now()
            ],
            [
                'key' => 'struk_out',
                'title' => 'Kirim Struk',
                'template' => 'Halo {name} berikut adalah struk dari pesanan kaka',
                'info' => 'Silahkan ketik parameter berikut untuk menambahkan data user {customer_name}, {customer_phone}, {productname}, {customerservice}',
                'created_at' => now()
            ]
        ]);
    }
}
