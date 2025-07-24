<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAccountInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-account-info';

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
        $cek = DB::table('ml_accounts')->get();

        $data = [];
        foreach($cek as $c) {
            $data[] = [
                'id' => $c->id,
                'user_id' => $c->id,
                'created_at' => now()
            ];
        }

        DB::table('ml_account_info')->insert($data);
        $this->info('done');
    }
}
