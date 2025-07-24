<?php

namespace App\Console\Commands;

use App\Traits\OmzetTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfitRecord extends Command
{
    use OmzetTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:profit-record {--from-month=} {--from-year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $controller;
    protected $businessTable = 'business_groups';
    /**
     * Execute the console command.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function get_profit(string $startOfMonthsAgo, string $endOfMonthsAgo, int $userId)
    {
        $profit = $this->count_net_profit($startOfMonthsAgo, $endOfMonthsAgo, $userId);
        return $profit;
    }

    public function handle()
    {
        $fromMonth = $this->option('from-month');
        $fromYear = $this->option('from-year');

        DB::beginTransaction();
        try {
            // $previousMonths = [20,19,18,17,16,15,14,13,12,11,10,9,8,7,6,5,4,3,2,1];
            $previousMonths = [1];

            foreach ($previousMonths as $prev) {
                if ($fromMonth && $fromYear) {
                    $startOfMonthsAgo = Carbon::createFromDate($fromYear, $fromMonth, 1)->startOfMonth()->format('Y-m-d H:i:s');
                    $endOfMonthsAgo = Carbon::createFromDate($fromYear, $fromMonth, 1)->endOfMonth()->format('Y-m-d H:i:s');
                } else {
                    $startOfMonthsAgo = Carbon::now()->subMonths($prev)->startOfMonth()->format('Y-m-d H:i:s');
                    $endOfMonthsAgo = Carbon::now()->subMonths($prev)->endOfMonth()->format('Y-m-d H:i:s');
                }

                $this->info($startOfMonthsAgo . ' - ' . $endOfMonthsAgo);
                // get list user dan bukan staff
                $users = DB::table('ml_accounts as ma')
                    ->selectRaw('ma.*, bg.id as company_id, bg.branch_name')
                    ->join('business_groups as bg', 'bg.user_id', '=', 'ma.id')
                    ->whereRole_code('general_member')
                    ->where('is_active', 1)
                    // ->where('ma.id', 14339)
                    ->get();

                foreach ($users as $user) {
                    // mengembalikan data per user
                    $getpro = $this->get_profit($startOfMonthsAgo, $endOfMonthsAgo, $user->id);
                    $profit = $getpro['bersih'];
                    $gross = $getpro['kotor'];
                    $summary = $this->get_summary($startOfMonthsAgo, $endOfMonthsAgo, $user->id);

                    DB::table('md_profit_records')->insert([
                        'userid' => $user->id,
                        'company_id' => $user->company_id,
                        'period' => $fromMonth . $fromYear,
                        'net_profit' => $profit,
                        'gross_profit' => $gross,
                        'summary_order_data' => $summary,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            $this->info('done!');
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->info('fail! ' . $th->getMessage());
        }
    }
}
