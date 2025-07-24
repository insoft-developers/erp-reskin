<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateExpiredSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:expired-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update any expried subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentTime = date("Y-m-d H:i:s");
        $queryAccounts = DB::table('ml_accounts')
            ->where('is_upgraded', 1)
            ->where('upgrade_expiry', '<', $currentTime);

        $accounts = $queryAccounts->get();
        if ($accounts->count() > 0) {
            DB::beginTransaction();
            try {
                $accoundIds = $accounts->pluck('id')->toArray();
                DB::table('subscription_logs')
                    ->whereIn('account_id', $accoundIds)
                    ->where('is_active', 1)
                    ->where('status', 1)
                    ->update([
                        'is_active' => 0
                    ]);

                foreach ($accounts as $ac) {
                    $owner = DB::table('owner_detail_users')->whereOwner_id($ac->id);
                    if ($owner->count()) {
                        $owner->update(['is_active' => 0]);
                    }
                }

                $queryAccounts->update([
                    'is_upgraded'       => 0,
                    'upgrade_expiry'    => null,
                ]);

                DB::commit();
                $this->info('Successfully updated expired subscriptions');
                return 0;
            } catch (Exception $e) {
                DB::rollBack();
                $this->error($e);
                return 1;
            }
        }
    }
}
