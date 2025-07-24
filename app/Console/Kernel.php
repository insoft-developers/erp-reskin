<?php

namespace App\Console;

use App\Jobs\AdminCustomerService;
use App\Models\MdCustomerServiceMessageTemplateAdmin;
use App\Models\MessageHistory;
use App\Models\MlAccount;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $template = MdCustomerServiceMessageTemplateAdmin::first();
        $liveIsReady = false;

        if ($liveIsReady) {
            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(3))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '3_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername       = $user->fullname;
                                $customerphone      = $user->phone;
                                $customerservice    = $user->cs->name;
                                $appkey             = $user->cs->appkey;
                                $message            = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_1
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id' => $user->id,
                                    'interval' => '3_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('09:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(10))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '10_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername       = $user->fullname;
                                $customerphone      = $user->phone;
                                $customerservice    = $user->cs->name;
                                $appkey             = $user->cs->appkey;
                                $message            = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_2
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id' => $user->id,
                                    'interval' => '10_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('10:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(20))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '20_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_2
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '20_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('11:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(30))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '30_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_3
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '30_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('12:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(40))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '40_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_4
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '40_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('13:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(50))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '50_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_5
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '50_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('14:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(60))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '60_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_6
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '60_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('15:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(70))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '70_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_7
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '70_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('16:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(80))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '80_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_8
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '80_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('17:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(90))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '90_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_9
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '90_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('18:30');

            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereDate('created_at', '<=', now()->subDays(100))
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '100_days')
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_10
                                );
                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan
                                MessageHistory::create([
                                    'user_id'  => $user->id,
                                    'interval' => '100_days',
                                ]);
                            }
                        }
                    });
            })->dailyAt('19:30');
        } else {
            $schedule->call(function () use ($template) {
                MlAccount::whereNotNull('created_at')
                    ->whereNotNull('cs_id')
                    ->whereIs_active(1)
                    ->chunk(100, function ($users) use ($template) {
                        foreach ($users as $user) {
                            // Periksa apakah pesan sudah dikirim pada interval tertentu, gunakan '0_days' sebagai default
                            $historyExists = MessageHistory::where('user_id', $user->id)
                                ->where('interval', '0_days') // Gunakan '0_days' untuk pesan tanpa interval khusus
                                ->exists();

                            if (!$historyExists) {
                                $customername    = $user->fullname;
                                $customerphone   = $user->phone;
                                $customerservice = $user->cs->name;
                                $appkey          = $user->cs->appkey;
                                $message         = str_replace(
                                    ['{customer_name}', '{customerservice}'],
                                    [$customername, $customerservice],
                                    $template->template_1 // Bisa sesuaikan template
                                );

                                dispatch(new AdminCustomerService($message, $customerphone, $appkey));

                                // Simpan riwayat pengiriman pesan dengan '0_days'
                                MessageHistory::create([
                                    'user_id' => $user->id,
                                    'interval' => '0_days', // Default tanpa interval
                                ]);
                            }
                        }
                    });
            })->hourly();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
