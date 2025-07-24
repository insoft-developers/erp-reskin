<?php

namespace App\Imports;

use App\Models\MaterialPurchaseItem;
use App\Models\MlCurrentAsset;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Models\Supplier;
use App\Traits\CommonTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MaterialPurchaseImport implements ToModel, WithHeadingRow
{
    use CommonTrait;

    private $rowCount = 0;
    private $purchaseId = 0;
    private $totalPurchase = 0;

    public function model(array $row)
    {
        if ($row['jumlah_beli'] == null) {
            return null;
        }


        $tanggal_transaksi = $row['tanggal_transaksi'] == null ? date('Y-m-d') : Date::excelToDateTimeObject($row['tanggal_transaksi']);
            $formattedDate = Carbon::parse($tanggal_transaksi)->format('Y-m-d');
        if ($this->rowCount == 0) {
            
           

            $gudang = 'Gudang Persediaan Bahan Baku';
            $cek_supplier = DB::table('md_suppliers')
                ->where('userid', $this->user_id_manage(session('id')))
                ->whereRaw('UPPER(name) like?', [strtoupper($gudang)]);
            if ($cek_supplier->count() > 0) {
                $supplier = $cek_supplier->first()->id;
            } else {
                $data_supplier = [
                    'userid' => $this->user_id_manage(session('id')),
                    'name' => ucwords(strtolower($gudang)),
                ];

                $supplier = DB::table('md_suppliers')->insertGetId($data_supplier);
            }
            
            $kas = DB::table('ml_current_assets')
                ->where('userid', $this->user_id_manage(session('id')))
                ->where('code', 'kas')
                ->first();

            $akun_kas = $kas->id . '_1';

            $data_transaksi = [
                'transaction_date' => $tanggal_transaksi,
                'userid' => $this->user_id_manage(session('id')),
                'account_id' => $akun_kas,
                'product_count' => 0,
                'tax' => 0,
                'discount' => 0,
                'other_expense' => 0,
                'total_purchase' => 0,
                'payment_type' => 0,
                'sync_status' => 0,
                'supplier_id' => $supplier,
                'reference' => 'INV-' . random_int(100000, 999999),
                'image' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $purchase_id = DB::table('ml_material_purchases')->insertGetId($data_transaksi);
            $this->purchaseId = $purchase_id;
        }

        $this->totalPurchase = $this->totalPurchase + $row['buying_price'] * $row['jumlah_beli'];

        $product = DB::table('md_materials')
            ->where('id', $row['material_id'])
            ->first();
        $stok_awal = $product->stock;

        $new_hpp = ($row['hpp'] * $row['stock'] + $row['buying_price'] * $row['jumlah_beli']) / ($row['stock'] + $row['jumlah_beli']);
        $hpp_masuk = round($new_hpp);

        DB::table('md_materials')
            ->where('id', $row['material_id'])
            ->update([
                'stock' => $stok_awal + $row['jumlah_beli'],
                'cost' => $hpp_masuk,
            ]);

        DB::table('log_stocks')->insert([
            'user_id' => $this->user_id_manage(session('id')),
            'relation_id' => $row['material_id'],
            'table_relation' => 'md_material',
            'stock_in' => $row['jumlah_beli'],
            'stock_out' => 0,
            'created_at' => $formattedDate.' '.date('H:i:s'),
            'updated_at' =>  $formattedDate.' '.date('H:i:s'),
        ]);

        return new MaterialPurchaseItem([
            'userid' => $this->user_id_manage(session('id')),
            'purchase_id' => $this->purchaseId,
            'product_id' => $row['material_id'],
            'purchase_amount' => $row['jumlah_beli'] * $row['buying_price'],
            'quantity' => $row['jumlah_beli'],
            'unit_price' => $row['buying_price'],
            'cost' => $row['buying_price'],
            'created_at' => $formattedDate.' '.date('H:i:s'),
            'updated_at' =>  $formattedDate.' '.date('H:i:s'),
            'indeks' => $this->rowCount++,
        ]);
    }

    public function get_purchase_id()
    {
        return $this->purchaseId;
    }

    public function get_total_purchase()
    {
        return $this->totalPurchase;
    }

    public function get_product_count()
    {
        return $this->rowCount;
    }
}
