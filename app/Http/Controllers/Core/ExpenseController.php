<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MdExpense;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCurrentAsset;
use App\Models\MlNonBusinessExpense;
use App\Models\MlSellingCost;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    use JournalTrait;
    use CommonApiTrait;
    
    public function list(Request $request) {
        $input = $request->all();
        $query = MdExpense::where('user_id', $this->user_id_staff($input['userid']))
        ->orderBy('id','desc');

        $query->whereMonth('date', $input['month']);
        $query->whereYear('date', $input['year']);
        if(! empty($input['category']))
        {
            $query->where('expense_category_id', $input['category']);
        }

        if(! empty($input['cari'])) {
            $query->where('keterangan', 'LIKE',  '%' . $input['cari'] . '%');
        }

        $data = $query->get();

        $rows = [];
        foreach($data as $d) {
            $row['id'] = $d->id;
            $row['expense_category_id'] = $d->expense_category_id;
            $row['date'] = $d->date;
            $row['dari'] = $d->dari;
            $row['untuk'] = $d->untuk;
            $row['amount'] = $d->amount;
            $row['keterangan'] = $d->keterangan;
            $row['account_code_id'] = $d->account_code_id;
            $row['sync_status'] = $d->sync_status;
            $row['created'] = $d->created;
            $row['user_id'] = $d->user_id;
            $row['category_name'] = $d->md_expense_category->name ?? null;
            $row['from_name'] = $d->from->name;
            $row['to_name'] = $d->to_api($d->untuk, $this->user_id_staff($input['userid']))->name;
            array_push($rows, $row);
        }

        return response()->json([
            "success" =>true,
            "data" => $rows
        ]);
    }

    public function account_from(Request $request) {
        $input = $request->all();
        $data = MlCurrentAsset::where('userid', $this->user_id_staff($input['userid']))->get();
        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function account_to(Request $request)
    {
        
        $input = $request->all();

        $ml_admin_general_fee = MlAdminGeneralFee::orderBy('name', 'desc')
            ->where('userid', $this->user_id_staff($input['userid']))
            ->get();

        $ml_non_business_expense = MlNonBusinessExpense::orderBy('name', 'desc')
            ->where('userid', $this->user_id_staff($input['userid']))
            ->get();

        $ml_selling_cost = MlSellingCost::orderBy('name', 'desc')
            ->where('userid', $this->user_id_staff($input['userid']))
            ->get();

        $data = $ml_admin_general_fee->merge($ml_non_business_expense)->merge($ml_selling_cost);

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
        
    }


    public function store(Request $request)
    {
        $data = $request->all();

        $rules = array(
            "date"=> "required",
            "expense_category_id" => "required",
            "dari" => "required",
            "untuk" => "required",
            "amount" => "required"
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
                $data['date'] = date('Y-m-d', strtotime($data['date']));
                $data['amount'] = str_replace('.', '', $data['amount']);
                $data['user_id'] = $user_id;
                $untuk = explode('_', $data['untuk']);
                $data['untuk'] = $untuk[0];
                $data['account_code_id'] = $untuk[1];

                $query = MdExpense::create($data);
                $this->live_sync($query->id, $user_id);

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil di Tambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal di Tambahkan!',
            ]);
        }
    }

   
    public function sync(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];
        $expenses = MdExpense::where('id', $id)->first();
        if ($expenses->sync_status !== 1) {
            $untuk = $expenses->untuk;
            $accode_code_id = $expenses->account_code_id;
            $keterangan = $expenses->keterangan;
            $rf = $expenses->dari . '_' . 1;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $expenses->amount;
            $waktu = strtotime($expenses->date);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $this->user_id_staff($input['userid']));
        }
        

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }


    protected function live_sync($id, $userid)
    {
        
        $expenses = MdExpense::where('id', $id)->first();
        if ($expenses->sync_status !== 1) {
            $untuk = $expenses->untuk;
            $accode_code_id = $expenses->account_code_id;
            $keterangan = $expenses->keterangan;
            $rf = $expenses->dari . '_' . 1;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $expenses->amount;
            $waktu = strtotime($expenses->date);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $userid);
        }
        

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }


    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';
        if ($accode_code_id == 9) {
            $account = MlSellingCost::findorFail($untuk);
            $transaction_name = $account->name.' ('.$keterangan.')';
        } elseif ($accode_code_id == 10) {
            $account = MlAdminGeneralFee::findorFail($untuk);
            $transaction_name = $account->name.' ('.$keterangan.')';
        } elseif ($accode_code_id == 12) {
            $account = MlNonBusinessExpense::findorFail($untuk);
            $transaction_name = $account->name.' ('.$keterangan.')';
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
            $transaction_name = $account->name.' ('.$keterangan.')';
        }

        return $transaction_name;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $userid)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $userid,
            'journal_id' => 0,
            'transaction_id' => 2,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal,
            'total_balance' => $nominal,
            'color_date' => $this->set_color(2),
            'created' => $waktu,
            'relasi_trx' => 'biaya_' . $id,
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

        $me = MdExpense::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        try {
            return $this->atomic(function () use ($id) {
                $expense = MdExpense::findorFail($id);
                if ($expense->sync_status == 1) {
                    $journal = Journal::where('relasi_trx', 'biaya_' . $id)->first();
                    JournalList::where('journal_id', $journal->id)->delete();
                    Journal::findorFail($journal->id)->delete();
                }

                $delete = MdExpense::find($id)->delete();

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

}
