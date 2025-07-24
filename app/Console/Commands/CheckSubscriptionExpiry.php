<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Traits\WhatsappTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiry extends Command
{
    use WhatsappTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check accounts with subscription expiring in 1 day and send notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for subscriptions expiring in 1 day...');

        // Get tomorrow's date in Y-m-d format
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // Find accounts with upgrade_expiry date matching tomorrow
        $expiringAccounts = DB::table('ml_accounts')
            ->whereDate('upgrade_expiry', $tomorrow)
            ->whereNotNull('phone')
            ->where('role_code', 'general_member')
            ->get();

        $count = $expiringAccounts->count();
        $this->info("Found {$count} account(s) with subscription expiring tomorrow ({$tomorrow}).");
        Log::info("Found {$count} account(s) with subscription expiring tomorrow ({$tomorrow}).");

        if ($count > 0) {
            Log::info('Sending expiry notifications...');
            $this->withProgressBar($expiringAccounts, function ($account) {
                $this->sendExpiryNotification($account);
            });
            $this->newLine();
            Log::info('All notifications sent successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Send expiry notification to a specific account
     * 
     * @param object $account
     * @return void
     */
    private function sendExpiryNotification($account)
    {
        Log::info("Sending notification to account ID: {$account->id}, Phone: {$account->phone}, Name: {$account->fullname}");
        $name = $account->fullname ?? 'Customer';
        $message = "Halo kak {$name}, Istabel dari Randu lagi nih.
        Mau kasih info cepat aja—akun Randu Premium kakak akan berakhir besok.

        Kalau kakak ingin tetap menikmati semua fitur Premium, kakak bisa langsung upgrade lewat menu berikut ya:
        🔗 https://app.randu.co.id/premium

        Atau kalau lebih nyaman dibantu oleh tim kami, kakak bisa hubungi Customer Service di sini:
        💬 https://randu.co.id/chat/upgrade-premium/

        Kami siap bantu kapan pun kakak butuh.
        Salam hangat,
        Istabel dari Randu";

        if (!empty($account->phone)) {
            $this->sendWhatsappMessage($account->phone, $message);
        }
    }
}
