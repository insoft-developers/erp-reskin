<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckIdleUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-idle-user';

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
        $thresholdDate = now()->subDays(25); // Menghitung tanggal 25 hari yang lalu

        // Melakukan query untuk memperbarui is_active
        DB::table('ml_accounts')
            ->where('idle_user', 0)
            ->where('created_at', '<', $thresholdDate)
            ->update(['is_active' => 0]);

        $this->info('Pembaruan selesai untuk pengguna yang tidak aktif.');
    }
}
