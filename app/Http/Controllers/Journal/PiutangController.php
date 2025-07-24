<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlCapital;
use App\Models\MlCurrentAsset;
use App\Models\MlIncome;
use App\Models\MlLongtermDebt;
use App\Models\MlNonBussinessIncome;
use App\Models\MlShorttermDebt;
use App\Models\Receivable;
use App\Models\ReceivablePaymentHistory;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\JournalTrait;
use App\Traits\LogUserTrait;

class PiutangController extends Controller
{
    use CommonTrait;
    use JournalTrait;
    use LogUserTrait;
    public function piutangList(Request $request)
    {
        $keyword = $request->keyword;
        $type = $request->type;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $userid = $this->user_id_staff($request->userid);

        $columns = ['id', 'receivable_from', 'save_to', 'name', 'type', 'sub_type', 'amount', 'note', 'user_id', 'sync_status', 'created_at', 'date'];

        $data = Receivable::orderBy('date', 'desc')
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
            $from = $d->receivable_from_m($d->receivable_from, $userid)->name ?? null;

            $row['id'] = $d->id;
            $row['receivable_from'] = $d->receivable_from;
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
            $row['save'] = $d->ml_current_asset->name ?? null;
            $row['date'] = $d->date == '0000-00-00' ? '' : date('d-m-Y', strtotime($d->date));
            array_push($rows, $row);
        }
        $this->insert_user_log($request->userid, 'piutang list');
        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function piutangHistory(Request $request)
    {
        $input = $request->all();

        try {
            $piutang = Receivable::findorFail($input['id']);

            $rows = [];
            foreach ($piutang->receivable_payment_history as $item) {
                $row['id'] = $item->id;
                $row['receivable_id'] = $item->receivable_id;
                $row['payment_from_id'] = $item->payment_from_id;
                $row['payment_to_id'] = $item->payment_to_id;
                $row['amount'] = $item->amount;
                $row['balance'] = $item->balance;
                $row['note'] = $item->note;
                $row['sync_status'] = $item->sync_status;
                $row['created_at'] = $item->created_at;
                $row['payment_from'] = $item->receivable_from_m($item->payment_from_id, $this->user_id_staff($input['userid']))->name ?? null;
                $row['payment_to'] = $item->ml_current_asset3($item->payment_to_id, $item->account_code_id, $this->user_id_staff($input['userid'])) ?? null;

                array_push($rows, $row);
            }

            return response()->json([
                'success' => true,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function piutangSubType(Request $request)
    {
        $input = $request->all();
        $sort = [['id' => 'Piutang Usaha (Accounts Receivable)', 'name' => 'Piutang Usaha (Accounts Receivable)'], ['id' => 'Piutang Non-usaha (Non-trade Receivables)', 'name' => 'Piutang Non-usaha (Non-trade Receivables)'], ['id' => 'Piutang - Karyawan (Employee Receivables)', 'name' => 'Piutang - Karyawan (Employee Receivables)'], ['id' => 'Piutang Bunga (Interest Receivables)', 'name' => 'Piutang Bunga (Interest Receivables)'], ['id' => 'Piutang Pajak (Tax Receivables)', 'name' => 'Piutang Pajak (Tax Receivables)']];

        $long = [['id' => 'Piutang Wesel Jangka Panjang (Long-term Notes Receivable)', 'name' => 'Piutang Wesel Jangka Panjang (Long-term Notes Receivable)'], ['id' => 'Piutang Sewa Jangka Panjang (Long-term Rent Receivables)', 'name' => 'Piutang Sewa Jangka Panjang (Long-term Rent Receivables)'], ['id' => 'Piutang Lain-lain Jangka Panjang (Other Long-term Receivables)', 'name' => 'Piutang Lain-lain Jangka Panjang (Other Long-term Receivables)']];

        if ($input['type'] == 'Piutang Jangka Pendek') {
            $data = $sort;
        } elseif ($input['type'] == 'Piutang Jangka Panjang') {
            $data = $long;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function piutangFrom(Request $request)
    {
        $type = $request->type;
        $data = [];

        $current = MlCurrentAsset::orderBy('id', 'asc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->get();

        foreach ($current as $in) {
            $row['id'] = $in->id;
            $row['name'] = $in->name;
            $row['userid'] = $in->userid;
            $row['account_code_id'] = $in->account_code_id;
            array_push($data, $row);
        }

        $income = MlIncome::orderBy('id', 'asc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->get();

        foreach ($income as $in) {
            $row['id'] = $in->id;
            $row['name'] = $in->name;
            $row['userid'] = $in->userid;
            $row['account_code_id'] = $in->account_code_id;
            array_push($data, $row);
        }

        $nb = MlNonBussinessIncome::orderBy('id', 'asc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->get();

        foreach ($nb as $in) {
            $row['id'] = $in->id;
            $row['name'] = $in->name;
            $row['userid'] = $in->userid;
            $row['account_code_id'] = $in->account_code_id;
            array_push($data, $row);
        }

        $cap = MlCapital::orderBy('id', 'asc')
            ->where('userid', $this->user_id_staff($request->userid))
            ->get();

        foreach ($cap as $in) {
            $row['id'] = $in->id;
            $row['name'] = $in->name;
            $row['userid'] = $in->userid;
            $row['account_code_id'] = $in->account_code_id;
            array_push($data, $row);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function piutangTo(Request $request)
    {
        $columns = ['id', 'name', 'userid'];

        $keyword = 'piutang ' . $request->keyword;

        $data = MlCurrentAsset::orderBy('id', 'asc')
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
            'receivable_from' => 'required',
            'save_to' => 'required',
            'name' => 'required',
            'type' => 'required',
            'sub_type' => 'required',
            'amount' => 'required',
            'tanggal' => 'required',
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
            $input['date'] = date('Y-m-d', strtotime($input['tanggal']));
            // $cek = MlIncome::where('id', $input['receivable_from'])->where('userid', $input['user_id']);
            // if ($cek->count() > 0) {
            //     $account_code_id = 7;
            // } else {
            //     $account_code_id = 1;
            // }

            $receivable_from = explode("_", $input['receivable_from']);

            $input['receivable_from'] = $receivable_from[0];
            $input['account_code_id'] = $receivable_from[1];
            $id = Receivable::create($input)->id;
            $this->single_sync($id, $this->user_id_staff($input['user_id']));
            return response()->json([
                'success' => true,
                'message' => 'Sukses tambah data',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function single_sync($trans_id, $userid)
    {
        $id = $trans_id;
        $dt = Receivable::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $dt->save_to;
            $accode_code_id = 1;
            $keterangan = $dt->sub_type;
            $ac_id = 1;

            $rf = $dt->receivable_from . '_' . $dt->account_code_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->amount;
            $tanggal = date('Y-m-d', strtotime($dt->date));
            $waktu = strtotime($tanggal);
            $transaction_name = $this->get_transaction_name($dt->save_to, $ac_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 5, 'piutang', $userid);
        }
    }

    public function piutangSync(Request $request)
    {
        $this->single_sync($request->id, $this->user_id_staff($request->userid));
        return response()->json([
            'success' => true,
            'message' => 'Sync Data berhasil!',
        ]);
    }

    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';
        if ($accode_code_id == 7) {
            $account = MlIncome::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 11) {
            $account = MlNonBussinessIncome::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 6) {
            $account = MlCapital::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
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
        if ($relasi == 'piutang') {
            $me = Receivable::findorFail($id);
        } elseif ($relasi == 'pembayaran') {
            $me = ReceivablePaymentHistory::findorFail($id);
        }

        $me->sync_status = 1;
        $me->save();
    }

    public function piutangPayment(Request $request)
    {
        $input = $request->all();

        $rules = [
            'amount' => 'required',
            'payment_to_id' => 'required',
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

        if ($request->amount > $request->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah yang anda masukkan lebih besar daripada sisa piutang!',
            ]);
        }

        try {


            $payment_to_id = explode("_", $input['payment_to_id']);


            $input['payment_to_id'] = $payment_to_id[0];
            $input['account_code_id'] = $payment_to_id[1];
            $input['balance'] = $request->balance - $request->amount;
            $input['created_at'] = date('Y-m-d H:i:s', strtotime($input['tanggal']));
            $input['updated_at'] = date('Y-m-d H:i:s', strtotime($input['tanggal']));
            $id = ReceivablePaymentHistory::create($input)->id;
            $this->syncPayment($id, $this->user_id_staff($input['user_id']));
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran Berhasil',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function syncPayment($payment_id, $userid)
    {
        $payment = ReceivablePaymentHistory::findorFail($payment_id);
        $utang = Receivable::findorFail($payment->receivable_id);

        if ($payment->sync_status !== 1) {
            $untuk = $payment->payment_to_id;
            $accode_code_id = $payment->account_code_id;
            $keterangan = $payment->note;
            $ac_id = 1;

            $rf = $payment->payment_from_id . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $payment->amount;
            $tanggal = date('Y-m-d', strtotime($payment->created_at));
            $waktu = strtotime($tanggal);
            $transaction_name = 'Pembayaran ' . $utang->type . ' ' . $keterangan;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $payment_id, $waktu, 6, 'pembayaran', $userid);
        }
    }

    public function piutangDestroy(Request $request)
    {
        $id = $request->id;

        try {
            return $this->atomic(function () use ($id) {
                $dt = Receivable::findorFail($id);
                if ($dt->sync_status == 1) {
                    $journals = Journal::where('relasi_trx', 'piutang_' . $id)->get();
                    foreach ($journals as $journal) {
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                }

                $delete = Receivable::find($id)->delete();

                $dph = ReceivablePaymentHistory::where('receivable_id', $id)->get();
                foreach ($dph as $dh) {
                    if ($dh->sync_status == 1) {
                        $journals = Journal::where('relasi_trx', 'pembayaran_' . $dh->id)->get();
                        foreach ($journals as $journal) {
                            JournalList::where('journal_id', $journal->id)->delete();
                            Journal::findorFail($journal->id)->delete();
                        }
                    }
                }

                $history = ReceivablePaymentHistory::where('receivable_id', $id)->delete();

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

    public function piutangPaymentSync(Request $request)
    {
        $payment = ReceivablePaymentHistory::findorFail($request->payment_id);
        $utang = Receivable::findorFail($payment->receivable_id);

        if ($payment->sync_status !== 1) {
            $untuk = $payment->payment_to_id;
            $accode_code_id = $payment->account_code_id;
            $keterangan = $payment->note;
            $ac_id = 1;

            $rf = $payment->payment_from_id . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $payment->amount;
            $tanggal = date('Y-m-d', strtotime($payment->created_at));
            $waktu = strtotime($tanggal);
            $transaction_name = 'Pembayaran ' . $utang->type . ' ' . $keterangan;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $request->payment_id, $waktu, 6, 'pembayaran', $this->user_id_staff($request->userid));
            return response()->json([
                'success' => true,
                'message' => 'Sync Data berhasil!',
            ]);
        }
    }

    // public function paymentSync(Request $request)
    // {
    //     $payment = ReceivablePaymentHistory::findorFail($request->payment_id);
    //     $utang = Receivable::findorFail($payment->debt_id);

    //     if ($payment->sync_status !== 1) {
    //         $untuk = $payment->payment_to_id;
    //         $accode_code_id = $utang->type == 'Utang Jangka Pendek' ? 4 : 5;
    //         $keterangan = $payment->note;
    //         $ac_id = 1;

    //         $rf = $payment->payment_from_id . '_' . $ac_id;
    //         $st = $untuk . '_' . $accode_code_id;
    //         $nominal = $payment->amount;
    //         $tanggal = date('Y-m-d', strtotime($payment->created_at));
    //         $waktu = strtotime($tanggal);
    //         $transaction_name = 'Pembayaran ' . $utang->type . ' ' . $keterangan;
    //         $this->sync_journal($transaction_name, $rf, $st, $nominal, $request->payment_id, $waktu, 4, 'payment', $this->user_id_staff($request->userid) );
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Sync Jurnal Berhasil',
    //     ]);
    // }

    public function piutangBayarKe(Request $request)
    {
        $input = $request->all();
        $current_asset = MlCurrentAsset::where('userid', $this->user_id_staff($input['userid']))->get();
        $fixed_asset = \App\Models\MlFixedAsset::where('userid', $this->user_id_staff($input['userid']))->get();
        $income = \App\Models\MlIncome::where('userid', $this->user_id_staff($input['userid']))->get();
        $admin_fee = \App\Models\MlAdminGeneralFee::where('userid', $this->user_id_staff($input['userid']))->get();
        $selling_cost = \App\Models\MlSellingCost::where('userid', $this->user_id_staff($input['userid']))->get();

        $data = array_merge($current_asset->toArray(), $fixed_asset->toArray(), $income->toArray(), $admin_fee->toArray(), $selling_cost->toArray());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }



    public function paymentDelete(Request $request) {
        $input = $request->all();
        $payment = ReceivablePaymentHistory::findorFail($input['id']);
        if($payment->sync_status == 1) {
            $jurnal = Journal::where('relasi_trx', 'pembayaran_'. $input['id']);
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
