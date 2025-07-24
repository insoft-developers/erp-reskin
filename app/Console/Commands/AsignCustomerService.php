<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AsignCustomerService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:asign-customer-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign active customer service to users in ml_accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil data customer service yang is_active = 1
        $customerServices = DB::table('md_customer_services')
            ->where('is_active', 1)
            ->pluck('id')
            ->toArray();

        if (empty($customerServices)) {
            $this->error('Tidak ada customer service yang aktif.');
            return;
        }

        // Hitung jumlah customer service yang aktif
        $csCount = count($customerServices);

        // Ambil semua data user di ml_accounts
        $users = DB::table('ml_accounts')->get();

        // Variabel untuk menghitung urutan customer service
        $index = 0;

        // Update setiap user dengan cs_id dari customer service
        foreach ($users as $user) {
            // Ambil id customer service berdasarkan urutan
            $csId = $customerServices[$index % $csCount];

            // Update cs_id di table ml_accounts
            DB::table('ml_accounts')
                ->where('id', $user->id)
                ->update(['cs_id' => $csId]);

            // Increment index untuk loop customer service
            $index++;
        }

        $this->info('Customer service berhasil di-assign ke semua user.');
    }
}
