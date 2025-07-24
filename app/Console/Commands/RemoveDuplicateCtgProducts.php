<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateCtgProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-duplicate-ctg-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua data dengan duplikat berdasarkan kolom 'code' dan 'user_id'
        $duplicates = DB::table('md_product_category')
            ->select('code', 'user_id', 'name', DB::raw('MIN(id) as keep_id'))
            ->groupBy('code', 'user_id', 'name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $deletedCount = 0;

        // Looping data duplikat dan hapus baris kecuali satu baris pertama
        foreach ($duplicates as $duplicate) {
            $deleted = DB::table('md_product_category')
                ->where('code', $duplicate->code)
                ->where('user_id', $duplicate->user_id)
                ->where('name', $duplicate->name)
                ->where('id', '!=', $duplicate->keep_id) // Hanya hapus yang bukan id yang disimpan
                ->update(['is_deleted' => 1]);

            $deletedCount += $deleted;
        }

        $this->info("Total duplikat yang dihapus: $deletedCount");

        return Command::SUCCESS;
    }
}
