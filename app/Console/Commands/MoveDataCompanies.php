<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveDataCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:move-data-companies';

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
        $olders = DB::table('ml_company')
            ->whereNotIn('userid', function ($query) {
                $query->select('user_id')->from('business_groups');
            })
            ->get();

        foreach ($olders as $older) {
            DB::table('business_groups')->insert([
                'user_id' => $older->userid,
                'branch_name' => $older->company_name,
                'business_address' => $older->address,
                'business_phone' => $older->phone_number,
                'model' => 'main',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->info('done');
    }
}
