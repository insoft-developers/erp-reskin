<?php

namespace App\Console\Commands;

use App\Models\WalletLogs;
use App\Models\MlAccount;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

class CheckPendingWalletLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:check-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pending wallet logs and update status if payment_at is more than 2 days';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $config = DB::table('ml_site_config')->first();

            // Mengecek yang ga jadi payment lebih dari waktu akan di canceled
            WalletLogs::where('status', 0)
                ->where('updated_at', '<', Carbon::now()->subDays($config->auto_release_in_days))->update([
                    'status' => 4
                ]);

            $walletLogs = WalletLogs::where('status', 2)
                // ->where('type', '+')
                ->where('updated_at', '<', Carbon::now()->subDays($config->auto_release_in_days))->get();
            // $walletLogs = WalletLogs::where('status', '1')->where('payment_at', '<', $currentTime->subMinute(3))->get();
            if ($walletLogs->count() > 0) {
                Log::debug('auto release in days berjalan');
                foreach ($walletLogs as $walletLog) {
                    $walletLog->status = '3';
                    $walletLog->save();
                    $currentBalanceRecord = 0;

                    $mlAccount = MlAccount::find($walletLog->user_id);
                    if ($mlAccount) {
                        if ($walletLog->type === '+') {
                            $mlAccount->balance = $mlAccount->balance += $walletLog->amount;
                            $mlAccount->save();
                        } else {
                            $mlAccount->balance = $mlAccount->balance -= $walletLog->amount;
                            $mlAccount->save();
                        }
                    } else {
                        $this->info("No ml_account found for user ID {$walletLog->user_id}");
                    }
                    $currentBalanceRecord = $mlAccount->balance;
                    Log::debug("User ID {$walletLog->user_id} with email {$walletLog->mlAccount->email} balance updated to {$currentBalanceRecord}");
                }

                DB::commit();
                $this->info('Successfully updated pending wallet logs');
                return 0;
            }

            DB::commit();
            $this->info('No pending wallet logs found');
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in CheckPendingWalletLogs: ' . $e->getMessage());
            $this->error('An error occurred while processing wallet logs: ' . $e->getMessage());
            return 1;
        }
    }
}
