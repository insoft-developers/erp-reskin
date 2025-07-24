<?php

namespace App\Console\Commands;

use App\Http\Controllers\Main\AccountController;
use Illuminate\Console\Command;

class darurat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:darurat';

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
        $controller = new AccountController();
        $id = 15536;
        $controller->insert_ml_current_assets($id);
        $controller->insert_ml_fixed_assets($id);
        $controller->insert_ml_accumulated_depreciation($id);
        $controller->insert_ml_shortterm_debt($id);
        $controller->insert_ml_longterm_debt($id);
        $controller->insert_ml_capital($id);
        $controller->insert_ml_income($id);
        $controller->insert_ml_cost_good_sold($id);
        $controller->insert_ml_selling_cost($id);
        $controller->insert_ml_admin_general_fees($id);
        $controller->insert_ml_non_business_income($id);
        $controller->insert_ml_non_business_expenses($id);
        $controller->insert_default_supplier($id);

        $this->info('done');
    }
}
