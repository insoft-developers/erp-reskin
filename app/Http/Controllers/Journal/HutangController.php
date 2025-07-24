<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\Material;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Models\MlCurrentAsset;
use App\Models\MlFixedAsset;

use App\Models\MlAdminGeneralFee;
use App\Models\MlLongtermDebt;
use App\Models\MlShorttermDebt;
use App\Models\MlCostGoodSold;
use App\Models\MlNonBussinessIncome;
use App\Models\MlSellingCost;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\JournalTrait;
use App\Traits\LogUserTrait;

class HutangController extends Controller
{
    use CommonTrait;
    use JournalTrait;
    use LogUserTrait;
    public function debtList(Request $request)
    {
        $keyword = $request->keyword;
        $type = $request->type;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $userid = $this->user_id_staff($request->userid);

        $columns = ['id', 'debt_from', 'save_to', 'name', 'type', 'sub_type', 'amount', 'note', 'user_id', 'sync_status', 'created_at', 'date','account_code_id'];

        $data = Debt::orderBy('date', 'desc')
            ->where('user_id', $userid)
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('date', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('date', $tahun);
            })
            ->get();

        $rows = [];
        foreach ($data as $d) {
            if ($d->type == 'Utang Jangka Panjang') {
                $from = $d->ml_longterm_debt->name ?? null;
            } elseif ($d->type == 'Utang Jangka Pendek') {
                $from = $d->ml_shortterm_debt->name ?? null;
            }

            $row['id'] = $d->id;
            $row['debt_from'] = $d->debt_from;
            $row['save_to'] = $d->save_to;
            $row['name'] = $d->name;
            $row['type'] = $d->type;
            $row['sub_type'] = $d->sub_type;
            $row['amount'] = $d->amount;
            $row['note'] = $d->note;
            $row['user_id'] = $d->user_id;
            $row['sync_status'] = $d->sync_status;
            $row['created_at'] = $d->created_at;
            $row['balance'] = $d->balance();
            $row['from'] = $from;
            if($d->account_code_id == 1) {
                $row['save'] = $d->ml_current_asset->name ?? null;    
            }
            else if($d->account_code_id == 2) {
                $row['save'] = $d->ml_fixed_asset->name ?? null;    
            }
            else if($d->account_code_id == 9) {
                $row['save'] = $d->ml_selling_cost->name ?? null;    
            }

            else if($d->account_code_id == 10) {
                $row['save'] = $d->ml_general_fee->name ?? null;    
            }
            
            $row['date'] = $d->date == '0000-00-00' ? '' : date('d-m-Y', strtotime($d->date));
            array_push($rows, $row);
        }
        $this->insert_user_log($request->userid, "debt (Hutang) list");
        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function debtHistory(Request $request)
    {
        $input = $request->all();

        $debt = Debt::findorFail($input['id']);

        $rows = [];
        foreach ($debt->debt_payment_history as $item) {
            $row['id'] = $item->id;
            $row['debt_id'] = $item->debt_id;
            $row['payment_from_id'] = $item->payment_from_id;
            $row['payment_to_id'] = $item->payment_to_id;
            $row['amount'] = $item->amount;
            $row['balance'] = $item->balance;
            $row['note'] = $item->note;
            $row['sync_status'] = $item->sync_status;
            $row['created_at'] = $item->created_at;

            if($item->account_code_id == 1) {
                $row['payment_from'] = $item->payment_from->name ?? null;
            }
            else if($item->account_code_id == 2) {
                $row['payment_from'] = $item->payment_fixed->name ?? null;
            }
            else if($item->account_code_id == 9) {
                $row['payment_from'] = $item->payment_selling->name ?? null;
            }
            else if($item->account_code_id == 8) {
                $row['payment_from'] = $item->payment_cogs->name ?? null;
            } 
            else if($item->account_code_id == 10) {
                $row['payment_from'] = $item->payment_cost->name ?? null;
            }
            else if($item->account_code_id == 11) {
                $row['payment_from'] = $item->payment_income->name ?? null;
            }

            
            $row['payment_to'] = $item->payment_to($debt->type)->name ?? null;

            array_push($rows, $row);
        }
        

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function debtSubType(Request $request)
    {
        $input = $request->all();
        $sort = [['id' => 'Utang Usaha (Accounts Payable)', 'name' => 'Utang Usaha (Accounts Payable)'], ['id' => 'Utang Wesel Jangka Pendek (Short-term Notes Payable)', 'name' => 'Utang Wesel Jangka Pendek (Short-term Notes Payable)'], ['id' => 'Utang Gaji (Salaries Payable)', 'name' => 'Utang Gaji (Salaries Payable)'], ['id' => 'Utang Pajak (Taxes Payable)', 'name' => 'Utang Pajak (Taxes Payable)'], ['id' => 'Utang Bunga (Interest Payable)', 'name' => 'Utang Bunga (Interest Payable)'], ['id' => 'Utang Sewa (Rent Payable)', 'name' => 'Utang Sewa (Rent Payable)'], ['id' => 'Utang Dividen (Dividends Payable)', 'name' => 'Utang Dividen (Dividends Payable)']];

        $long = [['id' => 'Utang Wesel Jangka Panjang (Long-term Notes Payable)', 'name' => 'Utang Wesel Jangka Panjang (Long-term Notes Payable)'], ['id' => 'Utang Obligasi (Bonds Payable)', 'name' => 'Utang Obligasi (Bonds Payable)'], ['id' => 'Utang Bank Jangka Panjang (Long-term Bank Loans)', 'name' => 'Utang Bank Jangka Panjang (Long-term Bank Loans)'], ['id' => 'Dan Hipotek (Mortgage Payable)', 'name' => 'Dan Hipotek (Mortgage Payable)']];

        if ($input['type'] == 'Utang Jangka Pendek') {
            $data = $sort;
        } elseif ($input['type'] == 'Utang Jangka Panjang') {
            $data = $long;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function debtFrom(Request $request)
    {
        $columns = ['id', 'name', 'userid'];
        $type = $request->type;
        $data = [];

        if ($type == 'Utang Jangka Pendek') {
            $data = MlShorttermDebt::orderBy('id', 'asc')
                ->where('userid', $this->user_id_staff($request->userid))
                ->select($columns)
                ->get();
        } elseif ($type == 'Utang Jangka Panjang') {
            $data = MlLongtermDebt::orderBy('id', 'asc')
                ->where('userid', $this->user_id_staff($request->userid))
                ->select($columns)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function debtTo(Request $request)
    {
        $columns = ['id', 'name', 'userid', 'account_code_id'];

        $current_asset = MlCurrentAsset::orderBy('id', 'asc')
            ->where('userid',$this->user_id_staff( $request->userid))
            ->select($columns)
            ->get();

        $fixed_asset = MlFixedAsset::orderBy('id', 'asc')
            ->where('userid',$this->user_id_staff( $request->userid))
            ->select($columns)
            ->get();
        $selling_cost = MlSellingCost::orderBy('id', 'asc')
            ->where('userid',$this->user_id_staff( $request->userid))
            ->select($columns)
            ->get();
        $general_admin = MlAdminGeneralFee::orderBy('id', 'asc')
            ->where('userid',$this->user_id_staff( $request->userid))
            ->select($columns)
            ->get();

        $data = array_merge($current_asset->toArray(), $fixed_asset->toArray(), $selling_cost->toArray(), $general_admin->toArray());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $rules = array(
            "debt_from" => "required",
            "save_to" => "required",
            "name" => "required",
            "type" => "required",
            "sub_type" => "required",
            "amount" => "required",
            "tanggal" => "required"
        );

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}'];
            $html = '';
            foreach ($pesanarr as $p) {
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= str_replace(':', '', $o) ."\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        try {
            $input['date'] = date('Y-m-d', strtotime($input['tanggal']));
            $saveto = explode("_", $input['save_to']);

            $input['save_to'] = $saveto[0];
            $input['account_code_id'] = $saveto[1];

            $id = Debt::create($input)->id;
            $this->single_sync($id, $this->user_id_staff($input['user_id']));
            $this->insert_user_log($request->user_id, "save new debt");
            return response()->json([
                'success' => true,
                'message' => 'Sukses tambah data',
            ]);
        }catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
       
    }

    public function single_sync($trans_id, $userid)
    {
        
        $id = $trans_id;
        $dt = Debt::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
           
           
            $relasi = explode('_', $dt->relasi_trx);

            $untuk = $dt->save_to;
            
            if($relasi[0] == 'konversi') {
                $accode_code_id = 9;
            }
            else if($relasi[0] == 'penyusutan') {
                $accode_code_id = 2;
            } else {
                $accode_code_id = $dt->account_code_id;
            }

            $keterangan = $dt->sub_type;
            $ac_id = $dt->type == 'Utang Jangka Pendek' ? 4 : 5;

            $rf = $dt->debt_from . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->amount;
            $tanggal = date('Y-m-d', strtotime($dt->date));
            $waktu = strtotime($tanggal);
            $transaction_name = $this->get_transaction_name($dt->debt_from, $ac_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 3, 'utang', $userid);
        }
    }

    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';
        if ($accode_code_id == 4) {
            $account = MlShorttermDebt::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 5) {
            $account = MlLongtermDebt::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
            $transaction_name = $account->name;
        }
        elseif ($accode_code_id == 2) {
            $account = MlFixedAsset::findorFail($untuk);
            $transaction_name = $account->name;
        }

        return $transaction_name . ' - ' . $keterangan;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $transaction_id, $relasi, $userid)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $userid,
            'journal_id' => 0,
            'transaction_id' => $transaction_id,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal,
            'total_balance' => $nominal,
            'color_date' => $this->set_color($transaction_id),
            'created' => $waktu,
            'relasi_trx' => $relasi . '_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        $data_list_insert = [
            'journal_id' => $journal_id,
            'rf_accode_id' => '',
            'st_accode_id' => $st,
            'account_code_id' => $ex_st[1],
            'asset_data_id' => $ex_st[0],
            'asset_data_name' => $this->get_transaction_name($ex_st[0], $ex_st[1], ''),
            'credit' => 0,
            'debet' => $nominal,
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
            'asset_data_name' => $this->get_transaction_name($ex_rf[0], $ex_rf[1], ''),
            'credit' => $nominal,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert2);
        if ($relasi == 'utang') {
            $me = Debt::findorFail($id);
        } elseif ($relasi == 'payment') {
            $me = DebtPaymentHistory::findorFail($id);
        }

        $me->sync_status = 1;
        $me->save();
    }

    public function debtPayment(Request $request) {
        $input = $request->all();

        $rules = array(
            "amount" => "required"
        );

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}'];
            $html = '';
            foreach ($pesanarr as $p) {
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        

        if($request->amount > $request->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah yang anda masukkan lebih besar daripada sisa hutang!',
            ]);
        }


        try {
            $input['balance'] = $request->balance - $request->amount;
            $input['created_at'] = date('Y-m-d H:i:s', strtotime($input['tanggal']));
            $input['updated_at'] = date('Y-m-d H:i:s', strtotime($input['tanggal']));

            $payment_from_id = explode("_", $input['payment_from_id']);

            $input['payment_from_id'] = $payment_from_id[0];
            $input['account_code_id'] = $payment_from_id[1];

            $id = DebtPaymentHistory::create($input)->id;
            $this->syncPayment($id, $this->user_id_staff($input['user_id']));
            return response()->json([
                'success' => true,
                'message' => "Pembayaran Berhasil",
            ]);
        }catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function syncPayment($payment_id, $userid)
    {
        
        $payment = DebtPaymentHistory::findorFail($payment_id);
        $utang = Debt::findorFail($payment->debt_id);

        if ($payment->sync_status !== 1) {
            $untuk = $payment->payment_to_id;
            $accode_code_id = $utang->type == 'Utang Jangka Pendek' ? 4 : 5;
            $keterangan = $payment->note;
            $ac_id = $payment->account_code_id;

            $rf = $payment->payment_from_id . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $payment->amount;
            $tanggal = date('Y-m-d', strtotime($payment->created_at));
            $waktu = strtotime($tanggal);
            $transaction_name = 'Pembayaran ' . $utang->type . ' ' . $keterangan;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $payment_id, $waktu, 4, 'payment', $userid);
        }
    }


    public function debtDestroy(Request $request)
    {
        $id = $request->id;
        
        try {
            return $this->atomic(function () use ($id) {
                $dt = Debt::findorFail($id);
                $relasi = $dt->relasi_trx;
                if (!empty($relasi)) {
                    $rel = explode('_', $relasi);
                    if ($rel[0] == 'konversi') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data Gagal Dihapus!, Silahkan Hapus Dari Menu Konversi Stock..!',
                        ]);
                    }
                    if ($rel[0] == 'penyusutan') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data Gagal Dihapus!, Silahkan Hapus Dari Menu Penyusutan..!',
                        ]);
                    }
                }

                if ($dt->sync_status == 1) {
                    $journals = Journal::where('relasi_trx', 'utang_' . $id)->get();
                    foreach ($journals as $journal) {
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                }

                if (!empty($relasi)) {
                    $rel = explode('_', $relasi);
                    if ($rel[0] == 'product-purchase') {
                        $items = ProductPurchaseItem::where('purchase_id', $rel[1])->get();
                        foreach ($items as $item) {
                            $product = Product::findorFail($item->product_id);
                            $current_stock = $product->quantity;
                            $current_cost = $product->cost;

                            Product::where('id', $item->product_id)->update([
                                'quantity' => $current_stock - $item->quantity,
                                'cost' => $current_stock - $item->quantity == 0 ? $product->default_cost : $this->reverse_cogs($current_cost, $current_stock, $item->cost, $item->quantity),
                            ]);
                        }

                        ProductPurchase::destroy($rel[1]);
                        ProductPurchaseItem::where('purchase_id', $rel[1])->delete();
                    } elseif ($rel[0] == 'material-purchase') {
                        $items = MaterialPurchaseItem::where('purchase_id', $rel[1])->get();
                        foreach ($items as $item) {
                            $product = Material::findorFail($item->product_id);
                            $current_stock = $product->stock;
                            $current_cost = $product->cost;

                            Material::where('id', $item->product_id)->update([
                                'stock' => $current_stock - $item->quantity,
                                'cost' => $current_stock - $item->quantity == 0 ? $product->default_cost : $this->reverse_cogs($current_cost, $current_stock, $item->cost, $item->quantity),
                            ]);
                        }

                        MaterialPurchase::destroy($rel[1]);
                        MaterialPurchaseItem::where('purchase_id', $rel[1])->delete();
                    }
                }

                $delete = Debt::find($id)->delete();

                $dph = DebtPaymentHistory::where('debt_id', $id)->get();
                foreach ($dph as $dh) {
                    if ($dh->sync_status == 1) {
                        $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                        foreach ($journals as $journal) {
                            JournalList::where('journal_id', $journal->id)->delete();
                            Journal::findorFail($journal->id)->delete();
                        }
                    }
                }

                $history = DebtPaymentHistory::where('debt_id', $id)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }

    public function debtSync(Request $request) {
        $this->single_sync($request->id, $this->user_id_staff($request->userid));
        return response()->json([
            'success' => true,
            'message' => 'Sync Data berhasil!',
        ]);
    }

    public function paymentSync(Request $request)
    {
       
        $payment = DebtPaymentHistory::findorFail($request->payment_id);
        $utang = Debt::findorFail($payment->debt_id);

        if ($payment->sync_status !== 1) {
            $untuk = $payment->payment_to_id;
            $accode_code_id = $utang->type == 'Utang Jangka Pendek' ? 4 : 5;
            $keterangan = $payment->note;
            $ac_id = $payment->account_code_id;

            $rf = $payment->payment_from_id . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $payment->amount;
            $tanggal = date('Y-m-d', strtotime($payment->created_at));
            $waktu = strtotime($tanggal);
            $transaction_name = 'Pembayaran ' . $utang->type . ' ' . $keterangan;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $request->payment_id, $waktu, 4, 'payment', $this->user_id_staff($request->userid));
        }

        return response()->json([
            'success' => true,
            'message' => "Sync Jurnal Berhasil"
        ]);
    }


    public function hutang_current_asset(Request $request) {
        $current_asset = MlCurrentAsset::where('userid', $this->user_id_staff($request->userid))->get();
        $fixed_asset = MlFixedAsset::where('userid', $this->user_id_staff($request->userid))->get();
        $cost = MlCostGoodSold::where('userid', $this->user_id_staff($request->userid))->get();
        $selling_cost = MLSellingCost::where('userid', $this->user_id_staff($request->userid))->get();
        $general_fee = MlAdminGeneralFee::where('userid', $this->user_id_staff($request->userid))->get();
        $nb_income = MlNonBussinessIncome::where('userid', $this->user_id_staff($request->userid))->get();

        $data = array_merge($current_asset->toArray(), $fixed_asset->toArray(), $cost->toArray(), $selling_cost->toArray(), $general_fee->toArray(), $nb_income->toArray());
        return response()->json([
            "success" =>true,
            "data" => $data
        ]);
    }

    public function paymentDelete(Request $request) {
        $input = $request->all();
        $payment = DebtPaymentHistory::findorFail($input['id']);
        if($payment->sync_status == 1) {
            $jurnal = Journal::where('relasi_trx', 'payment_'. $input['id']);
            if($jurnal->count() > 0) {
                foreach($jurnal->get() as $j) {
                    JournalList::where('journal_id', $j->id)->delete();
                }
                $jurnal->delete();
            }
        }
        
        $payment->delete();
        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);



    }
}
