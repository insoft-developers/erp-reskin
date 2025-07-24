<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlCurrentAsset;
use App\Models\MlLongtermDebt;
use App\Models\MlShorttermDebt;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\JournalTrait;
use App\Traits\LogUserTrait;

class HutangSyncController extends Controller
{
    use CommonTrait;
    use JournalTrait;
    use LogUserTrait;
  

   

    public function single_sync($trans_id, $userid)
    {
        
        $id = $trans_id;
        $dt = Debt::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $dt->save_to;
            $accode_code_id = $dt->account_code_id;
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

   

    public function hutang_current_asset(Request $request) {
        $data = MlCurrentAsset::where('userid', $this->user_id_staff($request->userid))->get();
        return response()->json([
            "success" =>true,
            "data" => $data
        ]);
    }
}
