<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-duplicate-materials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark duplicate entries in md_materials as deleted based on userid, material_name, sku, category_id, and supplier_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua data dengan duplikat berdasarkan kolom yang ditentukan
        $duplicates = DB::table('md_materials')
            ->select('userid', 'material_name', 'sku', 'category_id', 'supplier_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('userid', 'material_name', 'sku', 'category_id', 'supplier_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $deletedCount = 0;

        // Looping data duplikat dan hapus baris kecuali satu baris pertama
        foreach ($duplicates as $duplicate) {
            $deleted = DB::table('md_materials')
                ->where('userid', $duplicate->userid)
                ->where('material_name', $duplicate->material_name)
                ->where('sku', $duplicate->sku)
                ->where('category_id', $duplicate->category_id)
                ->where('supplier_id', $duplicate->supplier_id)
                ->where('id', '!=', $duplicate->keep_id) // Hanya hapus yang bukan id yang disimpan
                ->update(['is_deleted' => 1]);

            $deletedCount += $deleted;
        }

        $this->info("Total duplikat yang dihapus: $deletedCount");

        return Command::SUCCESS;
    }
}
