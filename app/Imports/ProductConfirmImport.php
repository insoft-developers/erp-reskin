<?php

namespace App\Imports;

use App\Models\MaterialCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Traits\CommonTrait;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductConfirmImport implements ToModel, WithHeadingRow
{
    use CommonTrait;
    public function model(array $row)
    {
        $cek_category = ProductCategory::where('user_id', $this->user_id_manage(session('id')))->whereRaw('UPPER(name) like?', [strtoupper($row['category'])]);
        if ($cek_category->count() > 0) {
            $cat = $cek_category->first()->id;
        } else {
            $sup = new ProductCategory();
            $sup->code = ucwords(strtolower($row['category']));
            $sup->name = ucwords(strtolower($row['category']));
            $sup->user_id = $this->user_id_manage(session('id'));
            $sup->is_deleted = 0;
            $sup->save();
            $cat = $sup->id;
        }

        return new Product([
            'category_id' => $cat,
            'code' => '',
            'sku' => $this->generate_product_sku($row['product_name']),
            'barcode' => $row['barcode'],
            'name' => $row['product_name'],
            'price' => $row['default_price'],
            'cost' => $row['cogs_hpp'] == null ? 0 : $row['cogs_hpp'],
            'default_cost' => 0,
            'unit' => $row['satuan'] == null ? 'Unit (Satuan)' : $row['satuan'],
            'quantity' => 0,
            'stock_alert' => 0,
            'sell' => 0,
            'created' => date('Y-m-d H:i:s'),
            'user_id' => $this->user_id_manage(session('id')),
            'is_variant' => 1,
            'is_manufactured' => 1,
            'buffered_stock' => $row['buffered_stock'] == null ? 0 : $row['buffered_stock'],
            'weight' => $row['product_weight'] == null ? 0 : $row['product_weight'],
            'description' => $row['description'] == null ? 'no-description' : $row['description'],
            'created_by' => 0,
            'price_ta' => $row['delivery_price'] == null ? $row['default_price'] : $row['delivery_price'],
            'price_mp' => $row['marketplace_price'] == null ? $row['default_price'] : $row['marketplace_price'],
            'price_cus' => $row['custom_price'] == null ? $row['default_price'] : $row['custom_price'],
            'store_displayed' => 1,
        ]);
    }
}
