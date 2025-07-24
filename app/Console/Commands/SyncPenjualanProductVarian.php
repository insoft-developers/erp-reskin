<?php

namespace App\Console\Commands;

use App\Models\PenjualanProductVarian;
use Illuminate\Console\Command;

class SyncPenjualanProductVarian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-penjualan-product-varian';

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
        $data = PenjualanProductVarian::whereNull('varian_name')->get();

        foreach ($data as $key => $value) {
            $value->update([
                'varian_name' => $value->variant->varian_name ?? null,
            ]);
        }

        return true;
    }
}
