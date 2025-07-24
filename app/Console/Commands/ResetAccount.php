<?php

namespace App\Console\Commands;

use App\Traits\ResetAccountTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetAccount extends Command
{
    use ResetAccountTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:account {user_id : ID dari user yang akan direset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset semua data terkait user berdasarkan user_id atau userid di berbagai tabel';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if (!$userId) {
            $this->error('User ID diperlukan untuk menjalankan command ini.');
            return Command::FAILURE;
        }

        $this->resetUserData($userId);

        $this->info("Reset data untuk user dengan ID $userId telah selesai.");

        return Command::SUCCESS;
    }
}
