<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BackupOmzet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-omzet';

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
        $periods = [
            // ['month' => '01', 'year' => '2020'],
            // ['month' => '02', 'year' => '2020'],
            // ['month' => '03', 'year' => '2020'],
            // ['month' => '04', 'year' => '2020'],
            // ['month' => '05', 'year' => '2020'],
            // ['month' => '06', 'year' => '2020'],
            // ['month' => '07', 'year' => '2020'],
            // ['month' => '08', 'year' => '2020'],
            // ['month' => '09', 'year' => '2020'],
            // ['month' => '10', 'year' => '2020'],
            // ['month' => '11', 'year' => '2020'],
            // ['month' => '12', 'year' => '2020'],

            // ['month' => '01', 'year' => '2021'],
            // ['month' => '02', 'year' => '2021'],
            // ['month' => '03', 'year' => '2021'],
            // ['month' => '04', 'year' => '2021'],
            // ['month' => '05', 'year' => '2021'],
            // ['month' => '06', 'year' => '2021'],
            // ['month' => '07', 'year' => '2021'],
            // ['month' => '08', 'year' => '2021'],
            // ['month' => '09', 'year' => '2021'],
            // ['month' => '10', 'year' => '2021'],
            // ['month' => '11', 'year' => '2021'],
            // ['month' => '12', 'year' => '2021'],

            // ['month' => '01', 'year' => '2022'],
            // ['month' => '02', 'year' => '2022'],
            // ['month' => '03', 'year' => '2022'],
            // ['month' => '04', 'year' => '2022'],
            // ['month' => '05', 'year' => '2022'],
            // ['month' => '06', 'year' => '2022'],
            // ['month' => '07', 'year' => '2022'],
            // ['month' => '08', 'year' => '2022'],
            // ['month' => '09', 'year' => '2022'],
            // ['month' => '10', 'year' => '2022'],
            // ['month' => '11', 'year' => '2022'],
            // ['month' => '12', 'year' => '2022'],

            // ['month' => '01', 'year' => '2023'],
            // ['month' => '02', 'year' => '2023'],
            // ['month' => '03', 'year' => '2023'],
            // ['month' => '04', 'year' => '2023'],
            // ['month' => '05', 'year' => '2023'],
            // ['month' => '06', 'year' => '2023'],
            // ['month' => '07', 'year' => '2023'],
            // ['month' => '08', 'year' => '2023'],
            // ['month' => '09', 'year' => '2023'],
            // ['month' => '10', 'year' => '2023'],
            // ['month' => '11', 'year' => '2023'],
            // ['month' => '12', 'year' => '2023'],

            // ['month' => '01', 'year' => '2024'],
            // ['month' => '02', 'year' => '2024'],
            // ['month' => '03', 'year' => '2024'],
            // ['month' => '04', 'year' => '2024'],
            // ['month' => '05', 'year' => '2024'],
            // ['month' => '06', 'year' => '2024'],
            ['month' => '08', 'year' => '2024'],
        ];

        foreach ($periods as $period) {
            $this->info($period['month'] . '-' . $period['year']);
            Artisan::call('app:profit-record', ['--from-month' => $period['month'], '--from-year' => $period['year']]);
        }

        $this->info('done');
    }
}
