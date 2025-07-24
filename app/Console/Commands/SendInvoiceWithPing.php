<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Branch;
use App\Models\WhatsappCrmProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInvoiceWithPing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-invoice-with-ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function get_owner_id(int $user_id)
    {
        $user = Account::where('id', $user_id)->first();
        if ($user->role_code === 'general_member') {
            return $user->id;
        } else if ($user->branch_id !== null) {
            $branch = Branch::where('id', $user->branch_id)->first();
            return $branch->account_id;
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // mengirim invoice yang belum bayar
        $penjualan = \App\Models\Penjualan::where('is_sended', 0)
            ->where('payment_status', 0)
            ->where(function ($query) {
                $query->where('cust_name', '!=', 'Walk In Customer')
                    ->orWhereNotNull('customer_id');
            })
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();

        $this->info('Sending invoice messages to ' . $penjualan->count() . ' customers pending orders');
        // Log::info('Sending invoice messages to ' . $penjualan->count() . ' customers pending orders');
        foreach ($penjualan as $item) {
            // get template message
            $template = \App\Models\WhatsappCrmTemplate::whereIn('owner_id', [0, $this->get_owner_id($item->user_id)])->orderBy('id', 'desc')->first();

            $reference = $item->reference;
            $link = env('APP_URL') . "/quick-invoice?ref=" . $reference;
            $message = str_replace(['{customer_name}', '{invoice_number}', '{link_struk}'], [$item->customer->name, $reference, $link], $template->template_data['invoice_pending']);

            $phone = $item->cust_phone ?? $item->customer->phone;

            $configs = WhatsappCrmProvider::where('owner_id', $this->get_owner_id($item->user_id))
                ->where('is_active', 1)
                ->inRandomOrder()
                ->first();

            if ($configs) {
                $randomDelaySeconds = rand(1, 5);
                \App\Jobs\AutoSendInvoice::dispatch($message, $phone, $configs->credentials['api_key'], $configs->credentials['device_id'], $item->id, 1)
                    ->onQueue('whatsapp-auto-invoice')
                    ->delay(now()->addSeconds($randomDelaySeconds));
            }
        }

        // mengirim invoice yang sudah bayar
        $penjualan = \App\Models\Penjualan::where('is_sended', '<', 2)
            ->where('payment_status', 1)
            ->where(function ($query) {
                $query->where('cust_name', '!=', 'Walk In Customer')
                    ->orWhereNotNull('customer_id');
            })
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $this->info('Sending invoice messages to ' . $penjualan->count() . ' customers complete payments');
        // Log::info('Sending invoice messages to ' . $penjualan->count() . ' customers complete payments');
        foreach ($penjualan as $item) {
            // get template message
            $template = \App\Models\WhatsappCrmTemplate::whereIn('owner_id', [0, $this->get_owner_id($item->user_id)])->orderBy('id', 'desc')->first();

            $reference = $item->reference;
            $link = env('APP_URL') . "/quick-invoice?ref=" . $reference;
            $message = str_replace(['{customer_name}', '{invoice_number}', '{link_struk}'], [$item->customer->name, $reference, $link], $template->template_data['invoice_payment_complete']);

            $phone = $item->cust_phone ?? $item->customer->phone;

            $configs = WhatsappCrmProvider::where('owner_id', $this->get_owner_id($item->user_id))
                ->where('is_active', 1)
                ->inRandomOrder()
                ->first();

            if ($configs) {
                $randomDelaySeconds = rand(1, 5);
                \App\Jobs\AutoSendInvoice::dispatch($message, $phone, $configs->credentials['api_key'], $configs->credentials['device_id'], $item->id, 2)
                    ->onQueue('whatsapp-auto-invoice')
                    ->delay(now()->addSeconds($randomDelaySeconds));
            }
        }

        $this->info('Invoice messages sent successfully.');
        // Log::info('Invoice messages sent successfully.');
    }
}
