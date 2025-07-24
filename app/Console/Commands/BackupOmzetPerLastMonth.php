<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BackupOmzetPerLastMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-omzet-per-last-month';

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
        $prev_month = date('m', strtotime('-1 month'));
        $current_year = date('Y');
        Artisan::call('app:profit-record', ['--from-month' => $prev_month, '--from-year' => $current_year]);
        $msg = 'Backup data transaksi pada period ' . $prev_month . $current_year . ' Telah berhasil';
        $this->info($msg);
        // Log::debug($msg);
    }
}
