<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InstallUserKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install-user-key';

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
        $con = new Controller();
        $users = DB::table('ml_accounts')->whereNull('user_key')->get();

        foreach ($users as $user) {
            DB::table('ml_accounts')->whereId($user->id)->update([
                'user_key' => $con->generateRandomString(8, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')
            ]);
        }

        $this->info('Well Done !');
    }
}
