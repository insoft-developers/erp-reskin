<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlAccumulatedDepreciation;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCurrentAsset;
use App\Models\MlFixedAsset;
use App\Models\MlLongtermDebt;
use App\Models\MlShorttermDebt;
use App\Models\Shrinkage;
use App\Models\ShrinkageSimulate;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\JournalTrait;
use App\Traits\LogUserTrait;

class PenyusutanController extends Controller
{
    use CommonTrait;
    use JournalTrait;
    use LogUserTrait;
    public function list(Request $request)
    {
        $columns = ['id', 'ml_fixed_asset_id', 'ml_accumulated_depreciation_id', 'ml_admin_general_fee_id', 'name', 'initial_value', 'useful_life', 'residual_value', 'note', 'user_id', 'sync_status', 'created_at', 'is_lost', 'quantity', 'buying_price'];

        $keyword = $request->keyword;
        $ml_fixed_asset = $request->ml_fixed_asset;
        $ml_accumulated_depreciation = $request->ml_accumulated_depreciation;
        $ml_admin_general_fee = $request->ml_admin_general_fee;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = Shrinkage::orderBy('id', 'desc')
            ->where('user_id', $this->user_id_staff($request->userid))
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->whereHas('ml_fixed_asset', function ($q) use ($ml_fixed_asset) {
                if ($ml_fixed_asset != '') {
                    $q->where('id', $ml_fixed_asset);
                }
            })
            ->whereHas('ml_accumulated_depreciation', function ($q) use ($ml_accumulated_depreciation) {
                if ($ml_accumulated_depreciation != '') {
                    $q->where('id', $ml_accumulated_depreciation);
                }
            })
            ->whereHas('ml_admin_general_fee', function ($q) use ($ml_admin_general_fee) {
                if ($ml_admin_general_fee != '') {
                    $q->where('id', $ml_admin_general_fee);
                }
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('created_at', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            })
            ->get();

        $rows = [];
        foreach ($data as $key) {
            $row['id'] = $key->id;
            $row['ml_fixed_asset_id'] = $key->ml_fixed_asset_id;
            $row['ml_accumulated_depreciation_id'] = $key->ml_accumulated_depreciation_id;
            $row['ml_admin_general_fee_id'] = $key->ml_admin_general_fee_id;
            $row['name'] = $key->name;
            $row['initial_value'] = $key->initial_value;
            $row['useful_life'] = $key->useful_life;
            $row['residual_value'] = $key->residual_value;
            $row['note'] = $key->note;
            $row['user_id'] = $key->user_id;
            $row['sync_status'] = $key->sync_status;
            $row['created_at'] = $key->created_at;
            $row['kategori_penyusutan'] = $key->ml_fixed_asset->name ?? null;
            $row['beban_penyusutan'] = $key->ml_admin_general_fee->name ?? null;
            $row['akumulasi_penyusutan'] = $key->ml_accumulated_depreciation->name ?? null;
            $row['is_lost'] = $key->is_lost;
            $row['quantity'] = $key->quantity;
            $row['buying_price'] = $key->buying_price;

            array_push($rows, $row);
        }

        $this->insert_user_log($request->userid, 'penyusutan list');

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function akunBiayaPenyusutan(Request $request)
    {
        $data = MlAdminGeneralFee::where('userid', $this->user_id_staff($request->userid))->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function akuns(Request $request)
    {
        $rows = [];
        $lancar = MlCurrentAsset::where('userid', $this->user_id_staff($request->userid))->get();
        foreach ($lancar as $l) {
            $row['id'] = $l->id . '_' . $l->account_code_id . '_1';
            $row['name'] = $l->name . ' - (Beli Cash)';
            array_push($rows, $row);
        }
        $pendek = MlShorttermDebt::where('userid', $this->user_id_staff($request->userid))->get();
        foreach ($pendek as $p) {
            $row['id'] = $p->id . '_' . $p->account_code_id . '_2';
            $row['name'] = $p->name . ' - (Beli Utang)';
            array_push($rows, $row);
        }
        $panjang = MlLongtermDebt::where('userid', $this->user_id_staff($request->userid))->get();
        foreach ($panjang as $j) {
            $row['id'] = $j->id . '_' . $j->account_code_id . '_2';
            $row['name'] = $j->name . ' - (Beli Utang)';
            array_push($rows, $row);
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function kategoriPenyusutan(Request $request)
    {
        $data = MlFixedAsset::where('userid', $this->user_id_staff($request->userid))->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function akunAkumulasiPenyusutan(Request $request)
    {
        $columns = ['id', 'name', 'userid'];
        $keyword = $request->keyword;

        $data = MlAccumulatedDepreciation::orderBy('id', 'asc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $rules = [
            'ml_fixed_asset_id' => 'required',
            'ml_accumulated_depreciation_id' => 'required',
            'ml_admin_general_fee_id' => 'required',
            'name' => 'required',
            'initial_value' => 'required',
            'useful_life' => 'required',
            'residual_value' => 'required',
            'quantity' => 'required',
            'buying_with_account' => 'required',
            'date' => 'required'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
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
        try {
            $input['sync_status'] = 0;
            $input['date'] = date("Y-m-d", strtotime($input['date']));
            $input['buying_price'] = $input['quantity'] == null ? $input['initial_value'] : $input['initial_value'] / $input['quantity'];
            $create = Shrinkage::create($input);

            $arr = explode('_', $input['buying_with_account']);

            if ($arr[2] == 2) {
                $debt = new Debt();
                $debt->debt_from = $arr[0];
                $debt->save_to = $input['ml_fixed_asset_id'];
                $debt->name = 'Pembelian Aset ' . $input['name'];
                $debt->type = $arr[1] == '4' ? 'Utang Jangka Pendek' : 'Utang Jangka Panjang';
                $debt->sub_type = $arr[1] == '4' ? 'Utang Usaha (Accounts Payable)' : 'Utang Bank Jangka Panjang (Long-term Bank Loans)';
                $debt->amount = $input['initial_value'];
                $debt->note = 'Pembelian Aset ' . $input['name'];
                $debt->user_id = $this->user_id_staff($input['user_id']);
                $debt->sync_status = 0;
                $debt->relasi_trx = 'penyusutan_' . $create->id;
                $debt->created_at = date('Y-m-d H:i:s');
                $debt->updated_at = date('Y-m-d H:i:s');
                $debt->date =  date("Y-m-d", strtotime($input['date']));
                $debt->save();

                $_controller = new HutangController();
                $_controller->single_sync($debt->id, $this->user_id_staff($input['user_id']));
            }

            $initial_book_value = $input['initial_value'];
            for ($value = 0; $value < $input['useful_life']; $value++) {
                $month = now()
                    ->addMonths($value + 1)
                    ->format('F Y');
                $date = now()
                    ->addMonths($value + 1)
                    ->format('Y-m');
                $shrinkage = ($input['initial_value'] - $input['residual_value']) / $input['useful_life'];
                $final_book_value = $initial_book_value - $shrinkage;

                $dataSimulate = [
                    'shrinkage_id' => $create->id,
                    'month' => "1 $month",
                    'initial_book_value' => $initial_book_value,
                    'shrinkage' => $shrinkage,
                    'final_book_value' => $final_book_value,
                    'user_id' => $this->user_id_staff($input['user_id']),
                    'date' => $date . '-01',
                ];

                ShrinkageSimulate::create($dataSimulate);

                $initial_book_value = $final_book_value;
            }

            $this->sync($create->id, $this->user_id_staff($input['user_id']));

            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        try {
            return $this->atomic(function () use ($id) {
                $dt = Shrinkage::findorFail($id);
                if ($dt->sync_status == 1) {
                    $journals = Journal::where('relasi_trx', 'penyusutan_' . $id)->get();
                    foreach ($journals as $journal) {
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                }

                if ($dt->is_lost == 1) {
                    $s_id = $dt->relasi_trx;
                    $new = Shrinkage::findorFail($s_id);
                    $stok_awal = $dt->quantity;
                    $stok_akhir = $stok_awal + $new->quantity;
                    $new->quantity = $stok_akhir;
                    $new->save();
                } else {
                    $lost_list = Shrinkage::where('relasi_trx', $id);
                    if ($lost_list->count() > 0) {
                        foreach ($lost_list->get() as $ll) {
                            if ($ll->sync_status == 1) {
                                $junal = Journal::where('relasi_trx', 'penyusutan_' . $ll->id)->get();
                                foreach ($junal as $lst) {
                                    JournalList::where('journal_id', $lst->id)->delete();
                                    Journal::findorFail($lst->id)->delete();
                                }
                            }
                        }
                    }

                    $lost_list->delete();
                }

                $delete = Shrinkage::find($id)->delete();


               
                if ($dt->is_lost == 1) {
                } else {
                    $deleteSimulate = ShrinkageSimulate::where('shrinkage_id', $id)->delete();

                    $utang = Debt::where('relasi_trx', 'penyusutan_' . $id);
                    if ($utang->count() > 0) {
                        $hutang = $utang->first();
                        if ($hutang->sync_status == 1) {
                            $journals = Journal::where('relasi_trx', 'utang_' . $hutang->id)->get();
                            foreach ($journals as $journal) {
                                JournalList::where('journal_id', $journal->id)->delete();
                                Journal::findorFail($journal->id)->delete();
                            }
                        }

                        $delete = Debt::find($hutang->id)->delete();

                        $dph = DebtPaymentHistory::where('debt_id', $hutang->id)->get();
                        foreach ($dph as $dh) {
                            if ($dh->sync_status == 1) {
                                $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                                foreach ($journals as $journal) {
                                    JournalList::where('journal_id', $journal->id)->delete();
                                    Journal::findorFail($journal->id)->delete();
                                }
                            }
                        }

                        $history = DebtPaymentHistory::where('debt_id', $hutang->id)->delete();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success' => true,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }

    public function simulate(Request $request)
    {
        $data = Shrinkage::find($request->id);
        $rows = [];

        foreach ($data->shrinkageSimulate as $item) {
            $row['month'] = $item->month;
            $row['initial_book_value'] = $item->initial_book_value;
            $row['shrinkage'] = $item->shrinkage;
            $row['final_book_value'] = $item->final_book_value;
            array_push($rows, $row);
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function syncProcess(Request $request)
    {
        $this->sync($request->id, $this->user_id_staff($request->userid));
        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function sync($transaction_id, $userid)
    {
        $dt = Shrinkage::where('id', $transaction_id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $dt->ml_admin_general_fee_id;
            $accode_code_id = 10;
            $keterangan = $dt->note;
            $rf = $dt->ml_accumulated_depreciation_id . '_' . 3;
            $st = $untuk . '_' . $accode_code_id;

            if ($dt->is_lost == 1) {
                $waktu = strtotime($dt->date);
                $transaction_name = $this->get_nama_transaksi($untuk, $accode_code_id, $keterangan) . '( ' . $dt->name . ' )';
                $this->sync_journal($transaction_name, $rf, $st, $dt->initial_value, $transaction_id, $waktu, $userid, 0);
            } else {
                $sims = ShrinkageSimulate::where('shrinkage_id', $transaction_id)->get();
                if ($sims->count() > 0) {
                    foreach ($sims as $ndex => $sim) {
                        $nominal = $sim->shrinkage;
                        $waktu = strtotime($sim->date);
                        $transaction_name = $this->get_nama_transaksi($untuk, $accode_code_id, $keterangan);
                        $this->sync_journal($transaction_name, $rf, $st, $nominal, $transaction_id, $waktu, $userid, $ndex);
                    }
                }
            }
        }
    }


    protected function get_nama_transaksi($untuk, $accode_code_id, $keterangan)
    {
        if ($accode_code_id == 10) {
            $data = MlAdminGeneralFee::findorFail($untuk);
            $transaction_name = $data->name;
            return $transaction_name;
        } elseif ($accode_code_id == 3) {
            $data = MlAccumulatedDepreciation::findorFail($untuk);
            $transaction_name = $data->name;
            return $transaction_name;
        }
    }


    protected function make_asset_journal($id, $userid)
    {
        $master = Shrinkage::findorFail($id);
        $arr = explode('_', $master->buying_with_account);
        $waktu = strtotime($master->date);

        $nama_asset = MlFixedAsset::findorFail($master->ml_fixed_asset_id);
        if ($arr[1] == 1) {
            $query = MlCurrentAsset::findorFail($arr[0]);
            $nama_akun = $query->name;
        } elseif ($arr[1] == 4) {
            $query = MlShorttermDebt::findorFail($arr[0]);
            $nama_akun = $query->name;
        } elseif ($arr[1] == 5) {
            $query = MlLongtermDebt::findorFail($arr[0]);
            $nama_akun = $query->name;
        }

        $data_journal = [
            'userid' => $this->user_id_staff($userid),
            'journal_id' => 0,
            'transaction_id' => 9,
            'transaction_name' => 'Pembelian Aset - ' . $master->name,
            'rf_accode_id' => $arr[0] . '_' . $arr[1],
            'st_accode_id' => $master->ml_fixed_asset_id . '_2',
            'nominal' => $master->initial_value,
            'total_balance' => $master->initial_value,
            'color_date' => $this->set_color(9),
            'created' => $waktu,
            'relasi_trx' => 'penyusutan_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        $data_list_insert = [
            'journal_id' => $journal_id,
            'rf_accode_id' => '',
            'st_accode_id' => $master->ml_fixed_asset_id . '_2',
            'account_code_id' => 2,
            'asset_data_id' => $master->ml_fixed_asset_id,
            'asset_data_name' => $nama_asset->name,
            'credit' => 0,
            'debet' => $master->initial_value,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JOurnalList::insert($data_list_insert);

        $data_list_insert2 = [
            'journal_id' => $journal_id,
            'rf_accode_id' => $arr[0] . '_' . $arr[1],
            'st_accode_id' => '',
            'account_code_id' => $arr[1],
            'asset_data_id' => $arr[0],
            'asset_data_name' => $nama_akun,
            'credit' => $master->initial_value,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert2);
    }


    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $userid, $index)
    {
        
        $master = Shrinkage::findorFail($id);
        $arr = explode('_', $master->buying_with_account);
        if ($master->is_lost !== 1 && $index == 0 && $arr[2] == 1) {
            $this->make_asset_journal($id, $userid);
        }
        
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $userid,
            'journal_id' => 0,
            'transaction_id' => 10,
            'transaction_name' => $master->is_lost == 1 ? $transaction_name : $transaction_name . ' - ' . $master->name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal,
            'total_balance' => $nominal,
            'color_date' => $this->set_color(10),
            'created' => $waktu,
            'relasi_trx' => 'penyusutan_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        $data_list_insert = [
            'journal_id' => $journal_id,
            'rf_accode_id' => '',
            'st_accode_id' => $st,
            'account_code_id' => $ex_st[1],
            'asset_data_id' => $ex_st[0],
            'asset_data_name' => $this->get_nama_transaksi($ex_st[0], $ex_st[1], ''),
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
            'asset_data_name' => $this->get_nama_transaksi($ex_rf[0], $ex_rf[1], ''),
            'credit' => $nominal,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert2);

        $me = Shrinkage::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function lostStore(Request $request)
    {
        $input = $request->all();

        $rules = [
            'transaction_id' => 'required',
            'lost_quantity' => 'required',
            'lost_value' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
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

        $p = Shrinkage::findorFail($input['transaction_id']);

        $lost_quantity = (int) $input['lost_quantity'];
        $stock = (int) $p->quantity;

        if ($lost_quantity > $stock) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah aset hilang tidak boleh lebih besar dari jumlah aset yang ada',
            ]);
        }

        try {
            $data_insert = [
                'ml_fixed_asset_id' => $p->ml_fixed_asset_id,
                'ml_accumulated_depreciation_id' => $p->ml_accumulated_depreciation_id,
                'ml_admin_general_fee_id' => $p->ml_admin_general_fee_id,
                'name' => 'Pengurangan Asset ' . $input['asset_name'],
                'initial_value' => $input['lost_value'],
                'quantity' => $input['lost_quantity'],
                'buying_price' => $p->buying_price,
                'useful_life' => 1,
                'residual_value' => 0,
                'note' => $input['asset_note'],
                'user_id' => $this->user_id_staff($input['userid']),
                'sync_status' => 0,
                'is_lost' => 1,
                'relasi_trx' => $input['transaction_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'date' => date('Y-m-d'),
            ];

            $sr = Shrinkage::create($data_insert);
            $this->sync($sr->id, $this->user_id_staff($input['userid']));

            $stok_awal = $p->quantity;
            $stok_akhir = $stok_awal - $input['lost_quantity'];
            $p->quantity = $stok_akhir;
            $p->save();

            return response()->json([
                'success' => true,
                'message' => 'Sukses Simpan Data',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
