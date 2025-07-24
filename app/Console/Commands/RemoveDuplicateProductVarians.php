<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateProductVarians extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-duplicate-product-varians';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark duplicate entries in md_product_varians as deleted based on product_id, varian_group, varian_name, sku, and varian_price';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua data dengan duplikat berdasarkan kolom yang ditentukan
        $duplicates = DB::table('md_product_varians')
            ->select('product_id', 'varian_group', 'varian_name', 'sku', 'varian_price', DB::raw('MIN(id) as keep_id'))
            ->groupBy('product_id', 'varian_group', 'varian_name', 'sku', 'varian_price')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $deletedCount = 0;

        // Looping data duplikat dan hapus baris kecuali satu baris pertama
        foreach ($duplicates as $duplicate) {
            $deleted = DB::table('md_product_varians')
                // ->where('product_id', $duplicate->product_id)
                ->where('varian_group', $duplicate->varian_group)
                ->where('varian_name', $duplicate->varian_name)
                ->where('sku', $duplicate->sku)
                ->where('varian_price', $duplicate->varian_price)
                ->where('id', '!=', $duplicate->keep_id) // Hanya hapus yang bukan id yang disimpan
                ->update(['is_deleted' => 1]);

            $deletedCount += $deleted;
        }

        $this->info("Total duplikat yang dihapus: $deletedCount");

        return Command::SUCCESS;
    }
}
