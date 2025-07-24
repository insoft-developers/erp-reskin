<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ResetAccountTrait
{
    use CommonTrait;

    public function resetUserData(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            DB::table('ml_accounts')->where('id', $userId)->update([
                'status_cashier' => 0
            ]);

            // Update queries
            DB::table('md_products')->where('user_id', $userId)->update([
                'cost' => 0,
                'default_cost' => 0,
                'quantity' => 0,
                'stock_alert' => 0,
            ]);

            DB::table('md_materials')->where('userid', $userId)->update([
                'stock' => 0,
                'cost' => 0,
            ]);

            DB::table('md_inter_products')->where('userid', $userId)->update([
                'cost' => 0,
                'stock' => 0,
            ]); 

            DB::table('ml_converse_costs')->where('userid', $this->user_id_manage($userId))->delete();
            DB::table('ml_converse_items')->where('userid', $this->user_id_manage($userId))->delete();
            DB::table('ml_converses')->where('userid', $this->user_id_manage($userId))->delete();

            // Delete queries
            $deleteTables = [
                ['table' => 'mt_rekapitulasi_harians', 'column' => 'user_id'],
                ['table' => 'mt_kas_kecils', 'column' => 'user_id'],
                ['table' => 'mt_pengeluaran_outlets', 'column' => 'user_id'],
                ['table' => 'log_stocks', 'column' => 'user_id'],
                ['table' => 'ml_journal', 'column' => 'userid'],
                ['table' => 'penjualan', 'column' => 'user_id'],
                ['table' => 'ml_product_purchases', 'column' => 'userid'],
                ['table' => 'ml_inter_purchases', 'column' => 'userid'],
                ['table' => 'ml_product_purchase_items', 'column' => 'userid'],
                ['table' => 'ml_material_purchases', 'column' => 'userid'],
                ['table' => 'ml_material_purchase_items', 'column' => 'userid'],
                ['table' => 'ml_product_manufactures', 'column' => 'userid'],
                ['table' => 'transfer_stock_products', 'column' => 'user_id'],
                ['table' => 'transfer_stock_materials', 'column' => 'user_id'],
                ['table' => 'debts', 'column' => 'user_id'],
                ['table' => 'debt_payment_histories', 'column' => 'user_id'],
                ['table' => 'invoices', 'column' => 'user_id'],
                ['table' => 'md_adjustment_inter_products', 'column' => 'user_id'],
                ['table' => 'md_adjustment_materials', 'column' => 'user_id'],
                ['table' => 'md_adjustment_products', 'column' => 'user_id'],
                ['table' => 'md_adjustments', 'column' => 'user_id'],
                ['table' => 'md_expense', 'column' => 'user_id'],
                ['table' => 'receivables', 'column' => 'user_id'],
                ['table' => 'receivable_payment_histories', 'column' => 'user_id'],
                ['table' => 'shrinkages', 'column' => 'user_id'],
                ['table' => 'shrinkage_simulates', 'column' => 'user_id'],
            ];

            foreach ($deleteTables as $table) {
                DB::table($table['table'])->where($table['column'], $userId)->delete();
            }

            // Complex delete queries
            DB::table('ml_journal_list')
                ->leftJoin('ml_journal', 'ml_journal_list.journal_id', '=', 'ml_journal.id')
                ->whereNull('ml_journal.id')
                ->delete();

            DB::table('penjualan_products')
                ->leftJoin('penjualan', 'penjualan_products.penjualan_id', '=', 'penjualan.id')
                ->whereNull('penjualan.id')
                ->delete();

            DB::table('penjualan_product_varians')
                ->leftJoin('penjualan_products', 'penjualan_product_varians.penjualan_product_id', '=', 'penjualan_products.id')
                ->whereNull('penjualan_products.id')
                ->delete();

            DB::table('invoice_details')
                ->leftJoin('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
                ->whereNull('invoices.id')
                ->delete();

            DB::table('invoice_termins')
                ->leftJoin('invoices', 'invoice_termins.invoice_id', '=', 'invoices.id')
                ->whereNull('invoices.id')
                ->delete();

            DB::table('landing_page_detail_bump_products')
                ->leftJoin('landing_pages', 'landing_page_detail_bump_products.landing_page_id', '=', 'landing_pages.id')
                ->whereNull('landing_pages.id')
                ->delete();

            DB::table('db_appranducoid.landing_pages')->where('user_id', $userId)->delete();
        });
    }
}
