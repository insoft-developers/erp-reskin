<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\InterProduct;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MdAdjustment;
use App\Models\MdAdjustmentInterProduct;
use App\Models\MdAdjustmentMaterial;
use App\Models\MdAdjustmentProduct;
use App\Models\MdProduct;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\Product;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AdjustmentController extends Controller
{
    use JournalTrait;
    use CommonApiTrait;

    public function list(Request $request) {
        $input = $request->all();
        $query = MdAdjustment::where('user_id', $this->user_id_staff($input['userid']))
            ->orderBy('id','desc');

        $query->whereMonth('date', $input['month']);
        $query->whereYear('date', $input['year']);
        if(! empty($input['category']))
        {
            $query->where('category_adjustment_id', $input['category']);
        }


        $data = $query->get();
        
        $rows = [];
        foreach($data as $index => $key) {
            
            $total_quantity = 0;

            $row['id'] = $key->id;
            $row['date'] = $key->date;
            $row['total_quantity'] = $key->total_quantity;
            $row['sync_status'] = $key->sync_status;
            $row['cogs'] = $key->cost_good_sold_id;
            $row['category_adjustment_id'] = $key->category_adjustment_id;
            $row['type'] = $key->type; 
            $row['userid'] = $key->user_id;
            $row['category'] = $key->category == null ? 'not-found' : $key->category->name ;
            if($key->type == 'product') {
                $row['detail'] = $key->md_adjustment_product;
                
                $d = [];
                foreach($row['detail'] as $k) {
                    $produk = MdProduct::where('id', $k->product_id);
                    $total_quantity = $total_quantity + $k->quantity;
                    if($produk->count() > 0) {
                        array_push($d, $produk->first()->name);
                    } else {
                        array_push($d, 'not-found');
                    }
                   
                }
                $row['product_name'] = $d;  

              
            }
            else if($key->type == 'inter_product') {
                $row['detail'] = $key->md_adjustment_inter_product;
                
                $d = [];
                foreach($row['detail'] as $k) {
                    $produk = InterProduct::where('id', $k->md_inter_product_id);
                    $total_quantity = $total_quantity + $k->quantity;
                    if($produk->count() > 0) {
                        array_push($d, $produk->first()->product_name);
                    } else {
                        array_push($d, 'not-found');
                    }
                   
                }
                $row['product_name'] = $d;  
            }
            else if($key->type == 'material') {
                $row['detail'] = $key->md_adjustment_material;
                $d = [];
                foreach($row['detail'] as $k) {
                    $produk = Material::where('id', $k->md_material_id);
                    $total_quantity = $total_quantity + $k->quantity;
                    if($produk->count() > 0) {
                        array_push($d, $produk->first()->material_name);
                    } else {
                        array_push($d, 'not-found');
                    }
                   
                }
                $row['product_name'] = $d;  
            }
            $row['totals'] = $total_quantity;
            array_push($rows, $row);
        }

        return response()->json([
            "success" => true,
            "data" => $rows
        ]);
    }


    public function account(Request $request)
    {
        
        $data = MlCostGoodSold::orderBy('id', 'desc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->get();

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
       
    }


    public function product(Request $request)
    {
        
        $data = MdProduct::orderBy('id', 'desc')
            ->where('user_id', $this->user_id_staff($request->userid))
            ->get();

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
       
    }


    public function inter(Request $request)
    {
        
        $data = InterProduct::orderBy('id', 'desc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->get();

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
       
    }

    public function material(Request $request)
    {
        
        $data = Material::orderBy('id', 'desc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->get();

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
       
    }


    public function product_store(Request $request)
    {
        $data = $request->all();

        $rules = array(
            "userid"=> "required",
            "date"=> "required",
            "category_adjustment_id"=> "required",
            "cost_good_sold_id"=> "required",
            "product_id.*"=> "required",
            "quantity.*"=> "required",
            "type.*"=> "required"
        );

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}', '"'];
            $html = '';
            $nomor = 0;
            foreach ($pesanarr as $p) {
                $nomor++;
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= $nomor . '. ' . str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        try {
            return $this->atomic(function () use ($data) {
                $user_id = $this->user_id_staff($data['userid']);
                $tanggal_transaksi = date('Y-m-d', strtotime($data['date'])).' '.date('H:i:s');
                $mdAdjustment = MdAdjustment::create([
                    'date' => date('Y-m-d', strtotime($data['date'])),
                    'total_quantity' => collect($data['quantity'])->sum(),
                    'created' => now(),
                    'cost_good_sold_id' => $data['cost_good_sold_id'],
                    'user_id' => $user_id,
                    'type' => 'product',
                    'category_adjustment_id' => $data['category_adjustment_id'],
                ]);

                foreach ($data['product_id'] as $key => $value) {
                    $mdAdjustmentProduct = MdAdjustmentProduct::create([
                        'adjustment_id' => $mdAdjustment->id,
                        'category_adjustment_id' => $data['category_adjustment_id'],
                        'product_id' => $value,
                        'quantity' => $data['quantity'][$key],
                        'type' => $data['type'][$key],
                        'created' => $tanggal_transaksi,
                        'user_id' => $user_id,
                    ]);

                    $product = MdProduct::find($value);
                    $product['quantity'] = $data['type'][$key] == 'addition' ? $product['quantity'] + $data['quantity'][$key] : $product['quantity'] - $data['quantity'][$key];
                    $product->save();

                    $stock_in = $data['type'][$key] == 'addition' ? $data['quantity'][$key] : 0;
                    $stock_out = $data['type'][$key] == 'substraction' ? $data['quantity'][$key] : 0;

                    $this->logStock('md_product', $value, $stock_in, $stock_out, $user_id, $tanggal_transaksi);
                }

                $this->single_sync_id($mdAdjustment->id, $user_id);
                return response()->json([
                    "success" => true,
                    "message" => 'Data Berhasil di Tambahkan!'
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage()
            ]);
        }
    }


    public function inter_store(Request $request)
    {
        $data = $request->all();

        $rules = array(
            "userid"=> "required",
            "date"=> "required",
            "category_adjustment_id"=> "required",
            "cost_good_sold_id"=> "required",
            "md_material_id.*"=> "required",
            "quantity.*"=> "required",
            "type.*"=> "required"
        );

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}', '"'];
            $html = '';
            $nomor = 0;
            foreach ($pesanarr as $p) {
                $nomor++;
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= $nomor . '. ' . str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        try {
            return $this->atomic(function () use ($data) {
                $user_id = $this->user_id_staff($data['userid']);
                $tanggal_transaksi = date('Y-m-d', strtotime($data['date'])).' '.date('H:i:s');
                $mdAdjustment = MdAdjustment::create([
                    'date' => date('Y-m-d', strtotime($data['date'])),
                    'total_quantity' => collect($data['quantity'])->sum(),
                    'created' => now(),
                    'cost_good_sold_id' => $data['cost_good_sold_id'],
                    'user_id' => $user_id,
                    'type' => 'inter_product',
                    'category_adjustment_id' => $data['category_adjustment_id'],
                ]);

                foreach ($data['md_inter_product_id'] as $key => $value) {
                    $MdAdjustmentInterProduct = MdAdjustmentInterProduct::create([
                        'adjustment_id' => $mdAdjustment->id,
                        'category_adjustment_id' => $data['category_adjustment_id'],
                        'md_inter_product_id' => $value,
                        'quantity' => $data['quantity'][$key],
                        'type' => $data['type'][$key],
                        'user_id' => $user_id,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi
                    ]);

                    $interProduct = InterProduct::find($value);
                    $interProduct['stock'] = $data['type'][$key] == 'addition' ? $interProduct['stock'] + $data['quantity'][$key] : $interProduct['stock'] - $data['quantity'][$key];
                    $interProduct->save();

                    $stock_in = $data['type'][$key] == 'addition' ? $data['quantity'][$key] : 0;
                    $stock_out = $data['type'][$key] == 'substraction' ? $data['quantity'][$key] : 0;

                    $this->logStock('md_inter_product', $value, $stock_in, $stock_out, $user_id, $tanggal_transaksi);
                }

                $this->single_sync_id($mdAdjustment->id, $user_id);
                return response()->json([
                    "success" => true,
                    "message" => 'Data Berhasil di Tambahkan!'
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage()
            ]);
        }
    }

    public function material_store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $user_id = $this->user_id_staff($data['userid']);
                $tanggal_transaksi = date('Y-m-d', strtotime($data['date'])).' '.date('H:i:s');
                $mdAdjustment = MdAdjustment::create([
                    'date' => date('Y-m-d', strtotime($data['date'])),
                    'total_quantity' => collect($data['quantity'])->sum(),
                    'created' => now(),
                    'cost_good_sold_id' => $data['cost_good_sold_id'],
                    'user_id' => $user_id,
                    'type' => 'material',
                    'category_adjustment_id' => $data['category_adjustment_id'],
                ]);

                foreach ($data['md_material_id'] as $key => $value) {
                    $MdAdjustmentMaterial = MdAdjustmentMaterial::create([
                        'adjustment_id' => $mdAdjustment->id,
                        'category_adjustment_id' => $data['category_adjustment_id'],
                        'md_material_id' => $value,
                        'quantity' => $data['quantity'][$key],
                        'type' => $data['type'][$key],
                        'user_id' => $user_id,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi
                    ]);

                    $material = Material::find($value);
                    $material['stock'] = $data['type'][$key] == 'addition' ? $material['stock'] + $data['quantity'][$key] : $material['stock'] - $data['quantity'][$key];
                    $material->save();

                    $stock_in = $data['type'][$key] == 'addition' ? $data['quantity'][$key] : 0;
                    $stock_out = $data['type'][$key] == 'substraction' ? $data['quantity'][$key] : 0;

                    $this->logStock('md_material', $value, $stock_in, $stock_out, $user_id, $tanggal_transaksi);
                }

                $this->single_sync_id($mdAdjustment->id, $user_id);

                return response()->json([
                    "success" => true,
                    "message" => "Data berhasil ditambahkan!"
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage()
            ]);
        }
    }


    public function logStock($table, $id, $stock_in, $stock_out, $userid, $tanggal)
    {
        try {
            LogStock::create([
                'user_id' => $this->user_id_staff($userid),
                'relation_id' => $id,
                'table_relation' => $table,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
                'created_at' => $tanggal,
                'updated_at' => $tanggal
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    protected function single_sync_id($id, $userid)
    {
        $dt = MdAdjustment::where('id', $id)->first();

        $jenis = $dt->type;
        if ($jenis == 'product') {
            $tipe = 'Penyesuaian Produk';
        } elseif ($jenis == 'inter_product') {
            $tipe = 'Penyesuaian Barang Setengah Jadi';
        } elseif ($jenis == 'material') {
            $tipe = 'Penyesuaian Bahan Baku';
        }
        if ($dt->sync_status !== 1) {
            $untuk = $this->get_code($jenis, $userid);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $dt->cost_good_sold_id . '_' . 8;
            $st = $untuk . '_' . $accode_code_id;

            $tot_nom = 0;

            if ($jenis == 'product') {
                $items = MdAdjustmentProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Product::findorFail($item->product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'material') {
                $items = MdAdjustmentMaterial::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Material::findorFail($item->md_material_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'inter_product') {
                $items = MdAdjustmentInterProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = InterProduct::findorFail($item->md_inter_product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            $nominal = $tot_nom;
            $waktu = strtotime($dt->date);
            $transaction_name = $tipe;
            $this->syncronize_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $dt->type, $userid);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function get_code($type, $userid)
    {
        $query = MlCurrentAsset::where('userid', $this->user_id_staff($userid));
        if ($type == 'product') {
            $query->where('code', 'persediaan-barang-dagang');
        } elseif ($type == 'inter_product') {
            $query->where('code', 'persedian-barang-setengah-jadi');
        } elseif ($type == 'material') {
            $query->where('code', 'persediaan-bahan-baku');
        }

        $data = $query->first();

        return $data->id ?? null;
    }

    protected function get_account_name($type, $userid)
    {
        $query = MlCurrentAsset::where('userid', $this->user_id_staff($userid));
        if ($type == 'product') {
            $query->where('code', 'persediaan-barang-dagang');
        } elseif ($type == 'inter_product') {
            $query->where('code', 'persedian-barang-setengah-jadi');
        } elseif ($type == 'material') {
            $query->where('code', 'persediaan-bahan-baku');
        }

        $data = $query->first();

        return $data->name ?? null;
    }

    protected function get_hpp($id)
    {
        $data = MlCostGoodSold::findorFail($id);
        if (!empty($data)) {
            return $data->name;
        } else {
            return '';
        }
    }


    protected function syncronize_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $tipe, $userid)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $userid,
            'journal_id' => 0,
            'transaction_id' => 10,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => abs($nominal),
            'total_balance' => abs($nominal),
            'color_date' => $this->set_color(10),
            'created' => $waktu,
            'relasi_trx' => 'adjustment_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        if ($nominal > 0) {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $st,
                'account_code_id' => $ex_st[1],
                'asset_data_id' => $ex_st[0],
                'asset_data_name' => $this->get_account_name($tipe, $userid),
                'credit' => 0,
                'debet' => abs($nominal),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $rf,
                'st_accode_id' => '',
                'account_code_id' => $ex_rf[1],
                'asset_data_id' => $ex_rf[0],
                'asset_data_name' => $this->get_hpp($ex_rf[0]),
                'credit' => abs($nominal),
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        } else {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $rf,
                'account_code_id' => $ex_rf[1],
                'asset_data_id' => $ex_rf[0],
                'asset_data_name' => $this->get_hpp($ex_rf[0]),
                'credit' => 0,
                'debet' => abs($nominal),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $st,
                'st_accode_id' => '',
                'account_code_id' => $ex_st[1],
                'asset_data_id' => $ex_st[0],
                'asset_data_name' => $this->get_account_name($tipe, $userid),
                'credit' => abs($nominal),
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        $me = MdAdjustment::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function sync(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $userid = $this->user_id_staff($input['userid']);

        $dt = MdAdjustment::where('id', $id)->first();

        $jenis = $dt->type;
        if ($jenis == 'product') {
            $tipe = 'Penyesuaian Produk';
        } elseif ($jenis == 'inter_product') {
            $tipe = 'Penyesuaian Barang Setengah Jadi';
        } elseif ($jenis == 'material') {
            $tipe = 'Penyesuaian Bahan Baku';
        }
        if ($dt->sync_status !== 1) {
            $untuk = $this->get_code($jenis, $userid);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $dt->cost_good_sold_id . '_' . 8;
            $st = $untuk . '_' . $accode_code_id;

            $tot_nom = 0;

            if ($jenis == 'product') {
                $items = MdAdjustmentProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Product::findorFail($item->product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'material') {
                $items = MdAdjustmentMaterial::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Material::findorFail($item->md_material_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'inter_product') {
                $items = MdAdjustmentInterProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = InterProduct::findorFail($item->md_inter_product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            $nominal = $tot_nom;
            $waktu = strtotime($dt->date);
            $transaction_name = $tipe;
            $this->syncronize_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $dt->type, $userid);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }


    public function destroy(Request $request)
    {
        $id = $request->id;
        $userid = $this->user_id_staff($request->userid);
        try {
            return $this->atomic(function () use ($id, $userid) {
                $dt = MdAdjustment::findorFail($id);
                if ($dt->sync_status == 1) {
                    $journal = Journal::where('relasi_trx', 'adjustment_' . $id)->first();
                    JournalList::where('journal_id', $journal->id)->delete();
                    Journal::findorFail($journal->id)->delete();
                }

                $delete = MdAdjustment::find($id);

                $this->reverseQuantity($delete, $id, $userid);

                $delete->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success' => true,
                'message' => $th->getMessage(),
            ]);
        }
    }

    protected function reverseQuantity($delete, $id, $userid)
    {
        if ($delete->type == 'product') {

            $md_adjustment_product = MdAdjustmentProduct::where('adjustment_id', $id)->where('user_id', $userid)->get();
            foreach ($md_adjustment_product as $key => $value) {
                $product = MdProduct::find($value['product_id']);
                if (isset($product)) {
                    $product['quantity'] = $value['type'] == 'addition' ? $product['quantity'] - $value['quantity'] : $product['quantity'] + $value['quantity'];
                    $product->save();
                }

                $value->delete();
                if ($value->type == 'addition') {
                    $LogStock = LogStock::where('relation_id', $value->product_id)->where('user_id', $this->user_id_staff($userid))->where('table_relation', 'md_product')->where('stock_in', $value->quantity)->whereDate('created_at', Carbon::parse($value->created))->orderBy('id', 'desc')->first()->delete();
                } else if ($value->type == 'substraction') {
                    $LogStock = LogStock::where('relation_id', $value->product_id)->where('user_id', $this->user_id_staff($userid))->where('table_relation', 'md_product')->where('stock_out', $value->quantity)->whereDate('created_at', Carbon::parse($value->created))->orderBy('id', 'desc')->first()->delete();
                }
            }
        } elseif ($delete->type == 'inter_product') {
            $md_inter_product = MdAdjustmentInterProduct::where('adjustment_id', $id)->where('user_id', $userid)->get();
            foreach ($md_inter_product as $key => $value) {
                $product = InterProduct::find($value['md_inter_product_id']);
                if (isset($product)) {
                    $product['stock'] = $value['type'] == 'addition' ? $product['stock'] - $value['quantity'] : $product['stock'] + $value['quantity'];
                    $product->save();
                }

                $value->delete();
                if ($value->type == 'addition') {
                    $LogStock = LogStock::where('relation_id', $value->md_inter_product_id)->where('user_id', $this->user_id_staff($userid))->where('table_relation', 'md_inter_product')->where('stock_in', $value->quantity)->whereDate('created_at', $value->created_at)->orderBy('id', 'desc')->first()->delete();
                } else if ($value->type == 'substraction') {
                    $LogStock = LogStock::where('relation_id', $value->md_inter_product_id)->where('user_id', $this->user_id_staff($userid))->where('table_relation', 'md_inter_product')->where('stock_out', $value->quantity)->whereDate('created_at', $value->created_at)->orderBy('id', 'desc')->first()->delete();
                }
            }
        } elseif ($delete->type == 'material') {
            $md_adjustment_material = MdAdjustmentMaterial::where('adjustment_id', $id)->where('user_id', $userid)->get();
            foreach ($md_adjustment_material as $key => $value) {
                $product = Material::find($value['md_material_id']);
                if (isset($product)) {
                    $product['stock'] = $value['type'] == 'addition' ? $product['stock'] - $value['quantity'] : $product['stock'] + $value['quantity'];
                    $product->save();
                }

                $value->delete();
                $value->delete();
                if ($value->type == 'addition') {
                    $LogStock = LogStock::where('relation_id', $value->md_material_id)->where('user_id', $this->user_id_staff($userid))->where('table_relation', 'md_material')->where('stock_in', $value->quantity)->whereDate('created_at', $value->created_at)->orderBy('id', 'desc')->first()->delete();
                } else if ($value->type == 'substraction') {
                    $LogStock = LogStock::where('relation_id', $value->md_material_id)->where('user_id', $this->user_id_staff($userid))->where('table_relation', 'md_material')->where('stock_out', $value->quantity)->whereDate('created_at', $value->created_at)->orderBy('id', 'desc')->first()->delete();
                }
            }
        }
    }

}
