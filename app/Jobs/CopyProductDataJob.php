<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyProductDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to_branch_user_id;
    protected $from_branch_user_id;

    /**
     * Create a new job instance.
     *
     * @param $res
     * @param $duplicate_product_from_user_id
     * @return void
     */
    public function __construct($to_branch_user_id, $from_branch_user_id)
    {
        $this->to_branch_user_id = $to_branch_user_id;
        $this->from_branch_user_id = $from_branch_user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::debug('Need copy product');

        // Dapatkan cabang pusat berdasarkan user_id yang diberikan
        $cabang_pusat = DB::table('owner_detail_users')
            ->where('user_id', $this->from_branch_user_id)
            ->orderBy('id', 'asc')
            ->first();

        if ($cabang_pusat) {
            // Ambil semua produk milik cabang pusat
            $m_products = DB::table('md_products')
                ->where('user_id', $cabang_pusat->user_id)
                ->get();

            foreach ($m_products as $index => $product) {
                $productArray = json_decode(json_encode($product), true);
                unset($productArray['id']);
                $productArray['cost'] = 0;
                $productArray['quantity'] = 0;
                $productArray['user_id'] = $this->to_branch_user_id;

                // Copy data kategori produk
                $category = DB::table('md_product_category')
                    ->where('user_id', $product->user_id)
                    ->where('id', $product->category_id)
                    ->where('is_deleted', 0)
                    ->first();

                if ($category) {
                    $categoryArray = json_decode(json_encode($category), true);
                    unset($categoryArray['id']);
                    $categoryArray['user_id'] = $this->to_branch_user_id;

                    // Cek apakah kategori dengan kombinasi code dan name sudah ada untuk user baru
                    $existingCategory = DB::table('md_product_category')
                        ->where('user_id', $this->to_branch_user_id)
                        ->where('code', $categoryArray['code'])
                        ->where('name', $categoryArray['name'])
                        ->where('is_deleted', 0)
                        ->first();

                    if ($existingCategory) {
                        $new_category_id = $existingCategory->id;
                    } else {
                        $new_category_id = DB::table('md_product_category')->insertGetId($categoryArray);
                    }

                    $productArray['category_id'] = $new_category_id;
                }

                // Insert produk baru
                $new_product_id = DB::table('md_products')->insertGetId($productArray);

                // Copy data gambar produk
                $m_images = DB::table('md_product_images')
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($m_images as $image) {
                    $imageArray = json_decode(json_encode($image), true);
                    unset($imageArray['id']);
                    $imageArray['product_id'] = $new_product_id;
                    DB::table('md_product_images')->insert($imageArray);
                }

                // Copy data varian produk
                $m_variants = DB::table('md_product_varians')
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($m_variants as $variant) {
                    $variantArray = json_decode(json_encode($variant), true);
                    unset($variantArray['id']);
                    $variantArray['product_id'] = $new_product_id;
                    DB::table('md_product_varians')->insert($variantArray);
                }

                $prev_materials = [];

                // Copy data materials
                $materials = DB::table('md_materials')
                    ->where('userid', $product->user_id)
                    ->where('is_deleted', 0)
                    ->get();

                foreach ($materials as $material) {
                    $materialArray = json_decode(json_encode($material), true);
                    $prevId = $materialArray['id'];
                    unset($materialArray['id']);
                    $materialArray['userid'] = $this->to_branch_user_id;

                    $existing = DB::table('md_materials')
                        ->where('userid', $this->to_branch_user_id)
                        ->where('material_name', $materialArray['material_name'])
                        ->where('sku', $materialArray['sku'])
                        ->where('category_id', $materialArray['category_id'])
                        ->where('supplier_id', $materialArray['supplier_id'])
                        ->where('is_deleted', 0)
                        ->first();

                    if ($existing) {
                        $newId = $existing->id;
                    } else {
                        $newId = DB::table('md_materials')->insertGetId($materialArray);
                    }

                    $prev_materials[$prevId] = $newId;
                }

                // Copy data komposisi produk
                $m_compositions = DB::table('md_product_compositions')
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($m_compositions as $composition) {
                    $compositionArray = json_decode(json_encode($composition), true);
                    unset($compositionArray['id']);
                    $compositionArray['product_id'] = $new_product_id;
                    $compositionArray['material_id'] = $prev_materials[$composition->material_id] ?? $composition->material_id;

                    DB::table('md_product_compositions')->insert($compositionArray);
                }

                // Copy inter products
                $inter_products = DB::table('md_inter_products')
                    ->where('userid', $product->user_id)
                    ->get();

                foreach ($inter_products as $inter_product) {
                    $interProductArray = json_decode(json_encode($inter_product), true);
                    $prevId = $interProductArray['id'];
                    unset($interProductArray['id']);
                    $interProductArray['userid'] = $this->to_branch_user_id;

                    $newInterProductId = DB::table('md_inter_products')->insertGetId($interProductArray);

                    // Copy inter compose products
                    $m_inter_compose_products = DB::table('md_inter_compose_products')
                        ->where('inter_product_id', $prevId)
                        ->get();

                    foreach ($m_inter_compose_products as $inter_compose_product) {
                        $interComposeProductArray = json_decode(json_encode($inter_compose_product), true);
                        unset($interComposeProductArray['id']);
                        $interComposeProductArray['inter_product_id'] = $newInterProductId;
                        $interComposeProductArray['material_id'] = $prev_materials[$inter_compose_product->material_id] ?? $inter_compose_product->material_id;

                        DB::table('md_inter_compose_products')->insert($interComposeProductArray);
                    }
                }
            }
        }
    }
}
