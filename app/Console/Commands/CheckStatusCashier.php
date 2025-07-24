<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckStatusCashier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-status-cashier';

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
        // Mendapatkan tanggal hari ini pada jam 23:59:59
        $closeTime = Carbon::now()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        // Log::debug('Sudah dijalankan pada pukul ' . $closeTime);

        // Melakukan update pada tabel mt_kas_kecil
        DB::table('mt_kas_kecils')->whereNull('close_cashier_at')->update(['close_cashier_at' => $closeTime]);
        $this->info('Kolom close_cashier_at berhasil diisi dengan ' . $closeTime);

        DB::table('ml_accounts')->whereNotNull('id')->update(['status_cashier' => 0]);
        $this->info('Kolom status_cashier pada ml_accounts sudah dirubah menjadi 0 untuk semua user');
    }
}
