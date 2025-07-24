<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAccountProfileRequest;
use App\Models\Account;
use App\Models\Branch;
use App\Models\BusinessGroup;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlAccount;
use App\Models\MlBank;
use App\Models\MlCurrentAsset;
use App\Models\MlSettingUser;
use Error;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $view = 'setting';
        return view('main.setting', compact('view'));
    }

    public function generate_opening_balance()
    {
        $view = 'opening-balance';
        return view('main.generate_open_balance', compact('view'));
    }

    public function submit_opening_balance(Request $request)
    {
        $input = $request->all();
        $custom_date = $input['month'] . '-' . $input['year'];
        $this_month = $input['year'] . '-' . $input['month'] . '-01';
        $tanggal = date('Y-m-d', strtotime($this_month));
        $u_tanggal = strtotime($tanggal);

        $capital = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('code', 'modal-pemilik')
            ->first();

        $c_code = $capital->id . '_' . $capital->account_code_id;

        $get_first_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' first day of previous month'));
        $get_last_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' last day of previous month'));

        $u_from = strtotime($get_first_day_of_prev_month);
        $u_to = strtotime($get_last_day_of_prev_month);

        $prive = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('code', 'prive')
            ->first();

        $total_prive = JournalList::where('asset_data_id', $prive->id)
            ->where('account_code_id', 6)
            ->whereBetween('created', [$u_from, $u_to])
            ->sum(DB::raw('debet-credit'));

        $laba = $this->count_net_profit($u_from, $u_to);

        $journal_delete = Journal::where('userid', $this->user_id_manage(session('id')))
            ->where('is_opening_balance', 1)
            ->where('created', $u_tanggal);

        foreach ($journal_delete->get() as $jd) {
            JournalList::where('journal_id', $jd->id)->delete();
        }

        $journal_delete->delete();

        $journals = Journal::where('userid', $this->user_id_manage(session('id')))
            ->whereBetween('created', [$u_from, $u_to])
            ->get();

        $j = new Journal();
        $j->userid = $this->user_id_manage(session('id'));
        $j->journal_id = 0;
        $j->transaction_id = 0;
        $j->transaction_name = 'Saldo Awal';
        $j->rf_accode_id = '';
        $j->st_accode_id = '';
        $j->debt_data = '';
        $j->nominal = 0;
        $j->total_balance = 0;
        $j->is_opening_balance = 1;
        $j->color_date = '#' . $this->get_random_color();
        $j->created = $u_tanggal;
        $j->save();

        $id = $j->id;

        $jl = new JournalList();
        $jl->journal_id = $id;
        $jl->rf_accode_id = '';
        $jl->st_accode_id = $c_code;
        $jl->account_code_id = $capital->account_code_id;
        $jl->asset_data_id = $capital->id;
        $jl->asset_data_name = $capital->name;
        $jl->debet = $laba - $total_prive > 0 ? 0 : abs($laba - $total_prive);
        $jl->credit = $laba - $total_prive > 0 ? abs($laba - $total_prive) : 0;
        $jl->is_debt = 0;
        $jl->is_receivables = 0;
        $jl->created = $u_tanggal;
        $jl->relasi_trx = '';
        $jl->save();

        $on = 0;

        $total_nominal_debit = 0;
        $total_nominal_kredit = 0;

        foreach ($journals as $journal) {
            $lists = DB::table('ml_journal_list as jl')
                ->select('jl.*', 'j.transaction_name')
                ->join('ml_journal as j', 'j.id', '=', 'jl.journal_id', 'left')
                ->where('jl.journal_id', $journal->id)
                ->orderBy('jl.id', 'asc')
                ->get();

            foreach ($lists as $list) {
                if ($list->account_code_id == 1 || $list->account_code_id == 2 || $list->account_code_id == 3 || $list->account_code_id == 4 || $list->account_code_id == 5) {
                    $jl = new JournalList();
                    $jl->journal_id = $id;
                    $jl->rf_accode_id = $list->rf_accode_id;
                    $jl->st_accode_id = $list->st_accode_id;
                    $jl->account_code_id = $list->account_code_id;
                    $jl->asset_data_id = $list->asset_data_id;
                    $jl->asset_data_name = $list->asset_data_name;
                    $jl->debet = $list->debet;
                    $jl->credit = $list->credit;
                    $jl->is_debt = $list->is_debt;
                    $jl->is_receivables = $list->is_receivables;
                    $jl->created = $u_tanggal;
                    $jl->relasi_trx = $list->relasi_trx;
                    $jl->save();

                    $total_nominal_debit = $total_nominal_debit + $list->debet;
                    $total_nominal_kredit = $total_nominal_kredit + $list->credit;
                } elseif ($list->account_code_id == 6) {
                    if ($list->asset_data_id == $capital->id) {
                        // dd($list->is_opening_balance);
                        $jl = new JournalList();
                        $jl->journal_id = $id;
                        $jl->rf_accode_id = $list->rf_accode_id;
                        $jl->st_accode_id = $list->st_accode_id;
                        $jl->account_code_id = $list->account_code_id;
                        $jl->asset_data_id = $list->asset_data_id;
                        $jl->asset_data_name = $list->asset_data_name;
                        $jl->debet = $list->debet;
                        $jl->credit = $list->credit;
                        $jl->is_debt = $list->is_debt;
                        $jl->is_receivables = $list->is_receivables;
                        $jl->created = $u_tanggal;
                        $jl->relasi_trx = $list->relasi_trx;
                        $jl->save();

                        $total_nominal_debit = $total_nominal_debit + $list->debet;
                        $total_nominal_kredit = $total_nominal_kredit + $list->credit;

                        $on++;
                    } elseif ($list->asset_data_id == $prive->id) {
                    } else {
                        $jl = new JournalList();
                        $jl->journal_id = $id;
                        $jl->rf_accode_id = $list->rf_accode_id;
                        $jl->st_accode_id = $list->st_accode_id;
                        $jl->account_code_id = $list->account_code_id;
                        $jl->asset_data_id = $list->asset_data_id;
                        $jl->asset_data_name = $list->asset_data_name;
                        $jl->debet = $list->debet;
                        $jl->credit = $list->credit;
                        $jl->is_debt = $list->is_debt;
                        $jl->is_receivables = $list->is_receivables;
                        $jl->created = $u_tanggal;
                        $jl->relasi_trx = $list->relasi_trx;
                        $jl->save();

                        $total_nominal_debit = $total_nominal_debit + $list->debet;
                        $total_nominal_kredit = $total_nominal_kredit + $list->credit;
                    }
                }
            }
        }

        // Journal::where('id', $id)->update(['nominal' => $total_nominal_debit, 'total_balance' => $total_nominal_debit]);

        $data = JournalList::where('journal_id', $id)->groupBy('asset_data_id')->get();

        $total_d = 0;
        $total_c = 0;
        foreach ($data as $d) {
            $total_d = $total_d + $this->get_view_data($id, $d->asset_data_id, $d->account_code_id)['debet'];
            $total_c = $total_c + $this->get_view_data($id, $d->asset_data_id, $d->account_code_id)['credit'];
        }

        Journal::where('id', $id)->update(['nominal' => $total_d, 'total_balance' => $total_d]);

        return response()->json([
            'success' => true,
            'message' => 'Generate Opening Balance Success',
        ]);
    }

    public function count_net_profit($from, $to)
    {
        $awal = $from;
        $akhir = $to;

        $data = $this->list_account();
        $total_income = 0;

        foreach ($data['income'] as $i) {
            $income = DB::table('ml_journal_list')
                ->where('asset_data_id', $i->id)
                ->where('account_code_id', 7)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));
            $total_income = $total_income + $income;
        }

        $total_hpp = 0;
        foreach ($data['hpp'] as $a) {
            $hpp = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 8)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_hpp = $total_hpp + $hpp;
        }

        $laba_rugi_kotor = $total_income - $total_hpp;
        $total_selling_cost = 0;
        foreach ($data['selling_cost'] as $a) {
            $selling_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 9)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_selling_cost = $total_selling_cost + $selling_cost;
        }

        $total_general_fees = 0;
        foreach ($data['general_fees'] as $a) {
            $general_fees = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 10)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_general_fees = $total_general_fees + $general_fees;
        }
        $total_nb_income = 0;
        foreach ($data['non_business_income'] as $a) {
            $nb_income = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 11)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit-debet'));
            $total_nb_income = $total_nb_income + $nb_income;
        }

        $total_nb_cost = 0;
        foreach ($data['non_business_cost'] as $a) {
            $nb_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 12)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_nb_cost = $total_nb_cost + $nb_cost;
        }

        $laba_bersih = $laba_rugi_kotor - $total_selling_cost - $total_general_fees + $total_nb_income - $total_nb_cost;
        return $laba_bersih;
    }

    public function company_setting()
    {
        $userId = $this->get_owner_id(session('id'));
        $view = 'company-setting';
        $data = BusinessGroup::where('user_id', $userId)->first();

        $account = Account::where('id', $userId)->first();
        if ($data != null) {
            $data->tax = $account->tax;
        }

        $category = Category::all();
        $bank = MlBank::all();


        $cabang = DB::table('branches')->where('account_id', $userId)->first();

        return view('main.company_setting', compact('view', 'data', 'category', 'bank', 'cabang'));
    }

    public function company_setting_update(Request $request)
    {
        $input = $request->all();

        $rules = [
            'company_name' => 'required',
            'company_email' => 'nullable|email',
            'phone_number' => 'required',
            'address' => 'required',
            'business_category' => 'required',
            'branches_name' => 'required',
            'branches_address' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        if ($request->hasFile('logo')) {
            $input['logo'] = $request->file('logo')->store('bussiness/logo', 'public');
        }

        $userId = $this->get_owner_id(session('id'));
        $cek = DB::table('business_groups')->where('user_id', $userId);
        if ($cek->count() > 0) {
            $update = BusinessGroup::find($input['id']);

            $dataUpdate = [
                'company_email' => $input['company_email'],
                'branch_name' => $input['company_name'],
                'business_phone' => $input['phone_number'],
                'business_address' => $input['address'],
                'business_category' => $input['business_category'] ?? null,
                'npwp' => $input['npwp'],
                'no_rekening' => $input['no_rekening'] ?? $update->no_rekening,
                'rekening_name' => $input['rekening_name'] ?? $update->rekening_name,
                'bank_id' => $input['bank_id'] ?? $update->bank_id,
                'province_id' => $input['province_id'],
                'city_id' => $input['city_id'],
                'logo' => $input['logo'] ?? $update->logo,
                'district_id' => $input['district_id'],
            ];

            $update->update($dataUpdate);
        } else {
            DB::table('business_groups')->insert([
                'company_email' => $input['company_email'],
                'branch_name' => $input['company_name'],
                'business_phone' => $input['phone_number'],
                'business_address' => $input['address'],
                'business_category' => $input['business_category'] ?? null,
                'npwp' => $input['npwp'],
                'no_rekening' => $input['no_rekening'],
                'rekening_name' => $input['rekening_name'],
                'bank_id' => $input['bank_id'],
                'province_id' => $input['province_id'],
                'city_id' => $input['city_id'],
                'district_id' => $input['district_id'],
                'logo' => $input['logo'],
                'user_id' => $userId,
            ]);
        }

        $branch = DB::table('branches')->where('account_id', $userId);
        if ($branch->first()) {
            DB::table('branches')
                ->where('account_id', $userId)
                ->update([
                    'name' => $input['branches_name'],
                    'address' => $input['branches_address'] ?? '-',
                    'phone' => $input['branches_phone'] ?? '-',
                    'district_id' => $input['branches_district_id'] ?? '-',
                ]);
        } else {
            DB::table('branches')->insert([
                'id' => $userId,
                'name' => $input['branches_name'],
                'address' => $input['branches_address'] ?? '-',
                'phone' => $input['branches_phone'] ?? '-',
                'district_id' => $input['branches_district_id'] ?? '-',
                'account_id' => $userId,
            ]);
        }

        $account = Account::where('id', $userId)->first();
        $account->tax = $input['tax'] ?? 0;
        $account->save();

        return Redirect::back()->with('success', 'Company Setting Successfully Updated !');
    }

    public function initial_capital()
    {
        $view = 'initial-capital';
        $akun = $this->get_account_select();
        $data_query = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('transaction_name', 'Saldo Awal')
            ->where('is_opening_balance', null);
        $data = $data_query->first();
        if ($data_query->count() > 0) {
            $detail = DB::table('ml_journal_list')
                ->where('journal_id', $data->id)
                ->orderBy('id')
                ->get();
        } else {
            $detail = [];
        }

        return view('main.initial_capital', compact('view', 'akun', 'data', 'detail'));
    }

    public function get_account_select()
    {
        $data = [];
        $group = [];

        $user_id = $this->user_id_manage(session('id'));
        $query = DB::table('ml_current_assets')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Aktiva Lancar';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Aktiva Lancar');

        $query = DB::table('ml_fixed_assets')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Aktiva Tetap';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Aktiva Tetap');

        $query = DB::table('ml_accumulated_depreciation')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Akumulasi Penyusutan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Akumulasi Penyusutan');

        $query = DB::table('ml_shortterm_debt')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Utang Jangka Pendek';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Utang Jangka Pendek');

        $query = DB::table('ml_longterm_debt')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Utang Jangka Panjang';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Utang Jangka Panjang');

        $query = DB::table('ml_capital')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Modal';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Modal');

        $query = DB::table('ml_income')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Pendapatan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Pendapatan');

        $query = DB::table('ml_cost_good_sold')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Harga Pokok Penjualan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Harga Pokok Penjualan');

        $query = DB::table('ml_selling_cost')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Penjualan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Penjualan');

        $query = DB::table('ml_admin_general_fees')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Umum Admin';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Umum Admin');

        $query = DB::table('ml_non_business_income')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Pendapatan Di Luar Usaha';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Pendapatan Di Luar Usaha');

        $query = DB::table('ml_non_business_expenses')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Diluar Usaha';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Diluar Usaha');

        $data['data'] = $data;
        $data['group'] = $group;

        return $data;
    }

    public function save_initial_capital(Request $request)
    {
        $input = $request->all();

        $rules = [
            'akun.*' => 'required',
            'debit.*' => 'required_without:kredit.*',
            'kredit.*' => 'required_without:debit.*',
            'transaction_date' => 'required',
            'transaction_name' => 'required',
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
                $html .= str_replace(':', '', $o) . '<br>';
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $nominal = '';
        if (empty($input['debit'][0])) {
            $nominal = $input['kredit'][0];
            $input['debit'][0] = 0;
        }
        if (empty($input['kredit'][0])) {
            $nominal = $input['debit'][0];
            $input['kredit'][0] = 0;
        }

        $date = strtotime($input['transaction_date']);

        $get_id_transaction = $this->initTransactionId($input['akun'][0], $input['akun'][1]);

        $ids = $input['transaction_id'];

        $journal_id = 0;

        if (empty($ids)) {
            $data_journal = [
                'userid' => $this->user_id_manage(session('id')),
                'transaction_id' => $get_id_transaction,
                'transaction_name' => 'Saldo Awal',
                'rf_accode_id' => $input['akun'][0],
                'st_accode_id' => $input['akun'][1],
                'nominal' => $nominal,
                'color_date' => '#' . $this->get_random_color(),
                'created' => $date,
            ];

            $journal_id = DB::table('ml_journal')->insertGetId($data_journal);
        } else {
            $data_journal = [
                'userid' => $this->user_id_manage(session('id')),
                'transaction_id' => $get_id_transaction,
                'transaction_name' => 'Saldo Awal',
                'rf_accode_id' => $input['akun'][0],
                'st_accode_id' => $input['akun'][1],
                'nominal' => $nominal,
                'color_date' => '#' . $this->get_random_color(),
                'created' => $date,
            ];
            DB::table('ml_journal')->where('id', $ids)->update($data_journal);
            DB::table('ml_journal_list')->where('journal_id', $ids)->delete();
            $journal_id = $ids;

            DB::table('ml_initial_capital')
                ->where('userid', $this->user_id_manage(session('id')))
                ->delete();
        }

        for ($i = 0; $i < count($input['akun']); $i++) {
            if (!empty($input['debit'][$i])) {
                $debit = $input['debit'][$i] == null ? 0 : $input['debit'][$i];

                if ($input['akun'][$i] !== '') {
                    $account_code_id = explode('_', $input['akun'][$i]);
                    $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_manage(session('id')), $account_code_id[0], $account_code_id[1]);
                }

                $data_debit = [
                    'journal_id' => $journal_id,
                    'rf_accode_id' => $input['akun'][$i],
                    'account_code_id' => $account_code_id[1],
                    'asset_data_id' => $account_code_id[0],
                    'asset_data_name' => $asset_data_name,
                    'debet' => $debit,
                    'created' => $date,
                ];
                DB::table('ml_journal_list')->insert($data_debit);
            }

            if (!empty($input['kredit'][$i])) {
                $credit = $input['kredit'][$i] == null ? 0 : $input['kredit'][$i];

                if ($input['akun'][$i] !== '') {
                    $account_code_id = explode('_', $input['akun'][$i]);
                    $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_manage(session('id')), $account_code_id[0], $account_code_id[1]);
                }

                $data_credit = [
                    'journal_id' => $journal_id,
                    'st_accode_id' => $input['akun'][$i],
                    'account_code_id' => $account_code_id[1],
                    'asset_data_id' => $account_code_id[0],
                    'asset_data_name' => $asset_data_name,
                    'credit' => $credit,
                    'created' => $date,
                ];
                DB::table('ml_journal_list')->insert($data_credit);
            }
        }

        // Update total saldo
        $reCalculateTotalSaldo = $this->checkTotalBalance($journal_id);

        DB::table('ml_journal')
            ->where('id', $journal_id)
            ->update(['total_balance' => $reCalculateTotalSaldo]);

        if (is_array($input['akun'])) {
            for ($i = 0; $i < count($input['akun']); $i++) {
                if (!empty($input['debit'][$i])) {
                    $debit = str_replace(',', '', $input['debit'][$i]);

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                    }

                    $data_debit = [
                        'transaction_name' => 'Saldo Awal',
                        'userid' => $this->user_id_manage(session('id')),
                        'rf_accode_id' => '',
                        'st_accode_id' => $input['akun'][$i],
                        'account_code_id' => $account_code_id[1],
                        // 'asset_data_id'		=> $account_code_id[0],
                        // 'asset_data_name'	=> getAllListAssetWithAccDataId(session('id'), $account_code_id[0], $account_code_id[1]),
                        'debet' => $debit,
                        'credit' => 0,
                        'created' => $date,
                    ];

                    // First insert data to ml_initial_capital
                    DB::table('ml_initial_capital')->insert($data_debit);
                }

                if (!empty($input['kredit'][$i])) {
                    $credit = str_replace(',', '', $input['kredit'][$i]);

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                    }

                    $data_credit = [
                        'transaction_name' => 'Saldo Awal',
                        'userid' => $this->user_id_manage(session('id')),
                        'rf_accode_id' => $input['akun'][$i],
                        'st_accode_id' => '',
                        'account_code_id' => $account_code_id[1],
                        // 'asset_data_id'		=> $account_code_id[0],
                        // 'asset_data_name'	=> getAllListAssetWithAccDataId(session('id'), $account_code_id[0], $account_code_id[1]),
                        'debet' => 0,
                        'credit' => $credit,
                        'created' => $date,
                    ];

                    // First insert data to ml_initial_capital
                    DB::table('ml_initial_capital')->insert($data_credit);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function initTransactionId($account_code_id1, $account_code_id2)
    {
        $var_rf_cid = explode('_', $account_code_id1);
        $rf_id = $var_rf_cid[0];
        $rf_code_id = $var_rf_cid[1];

        $var_st_cid = explode('_', $account_code_id2);
        $st_id = $var_st_cid[0];
        $st_code_id = $var_st_cid[1];

        // $res1 = $this->db->sql_prepare("select * from ml_transaction_subcat where account_code_id = :id and received_from_status = 0 order by id");
        // $bindParam1 = $this->db->sql_bindParam(['id' => $rf_code_id], $res1);
        // while ($row1 = $this->db->sql_fetch_single($bindParam1))
        // {
        // 	if ($row1['transaction_id'] !== 3 && $row1['transaction_id'] !== 5)
        // 	{
        // 		$output1[] = $row1['transaction_id'];
        // 	}
        // }

        $row1 = DB::table('ml_transaction_subcat')->where('account_code_id', $rf_code_id)->where('received_from_status', 0)->orderBy('id')->get();
        foreach ($row1 as $rw1) {
            if ($rw1->transaction_id !== 3 && $rw1->transaction_id !== 5) {
                $output1[] = $rw1->transaction_id;
            }
        }

        // $res2 = $this->db->sql_prepare("select * from ml_transaction_subcat where account_code_id = :id and saved_to_status = 0 order by id");
        // $bindParam2 = $this->db->sql_bindParam(['id' => $st_code_id], $res2);
        // while ($row2 = $this->db->sql_fetch_single($bindParam2))
        // {
        // 	if ($row2['transaction_id'] !== 3 && $row2['transaction_id'] !== 5)
        // 	{
        // 		$output2[] = $row2['transaction_id'];
        // 	}
        // }

        $row2 = DB::table('ml_transaction_subcat')->where('account_code_id', $st_code_id)->where('saved_to_status', 0)->orderBy('id')->get();
        foreach ($row2 as $rw2) {
            if ($rw2->transaction_id !== 3 && $rw2->transaction_id !== 5) {
                $output2[] = $rw2->transaction_id;
            }
        }

        $output3 = array_uintersect($output1, $output2, 'strcasecmp');

        $k = array_rand($output3);
        $new_output = $output3[$k];

        return $new_output;
    }

    public function checkTotalBalance($journal_id)
    {
        $total_all_debit = 0;
        $total_all_credit = 0;

        $i = 0;
        // $res_journal_list = $this->db->sql_prepare("select * from ml_journal_list where journal_id = :journal_id order by id");
        // $bindParam_journal_list = $this->db->sql_bindParam(['journal_id' => $journal_id], $res_journal_list);

        $bindParam_journal_list = DB::table('ml_journal_list')->where('journal_id', $journal_id)->get();

        // while ($row_journal_list = $this->db->sql_fetch_single($bindParam_journal_list))
        // {
        // 	$total_all_debit += $row_journal_list['debet'];
        // 	$total_all_credit += $row_journal_list['credit'];
        // }

        foreach ($bindParam_journal_list as $key) {
            $total_all_debit += $key->debet;
            $total_all_credit += $key->credit;
        }

        $new_output['total_all_debit'] = $total_all_debit;
        $new_output['total_all_credit'] = $total_all_credit;

        if ($new_output['total_all_debit'] == $new_output['total_all_credit']) {
            $output = $new_output['total_all_debit'];
        } else {
            $new_total_all_dc = $new_output['total_all_debit'] - $new_output['total_all_credit'];

            $output = $new_total_all_dc;
        }

        // echo $output;
        // exit;

        return $output;
    }

    public function getListInitialCapital()
    {
        $rows = DB::table('ml_initial_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->orderBy('id', 'asc')
            ->get();

        foreach ($rows as $row) {
            if (!empty($row->rf_accode_id)) {
                $row['selected_accode_id'] = $row->rf_accode_id;
            } elseif (!empty($row->st_accode_id)) {
                $row['selected_accode_id'] = $row->st_accode_id;
            }

            $row['debet'] = $row->debet == 0 ? '' : number_format($row->debet, 0, '.', ',');
            $row['credit'] = $row->credit == 0 ? '' : number_format($row->credit, 0, '.', ',');
            $row['form_debet'] = $row->debet == '' ? 'disabled' : false;
            $row['form_credit'] = $row->credit == '' ? 'disabled' : false;
            $row['inputDebitInitialCapital'] = $row['debet'];
            $row['inputCreditInitialCapital'] = $row['credit'];

            $output[] = $row;
        }

        if ($rows->count() > 0) {
            $output[] = ['data' => '', 'selected_accode_id' => '', 'inputDebitInitialCapital' => '', 'inputCreditInitialCapital' => '', 'form_debet' => false, 'form_credit' => false];
            $output[] = ['data' => '', 'selected_accode_id' => '', 'inputDebitInitialCapital' => '', 'inputCreditInitialCapital' => '', 'form_debet' => false, 'form_credit' => false];
        }

        return response()->json([
            'success' => true,
            'data' => $output,
        ]);
    }

    public function account_setting()
    {
        $view = 'account-setting';
        $data = $this->get_account_select();

        return view('main.account_setting', compact('view', 'data'));
    }

    public function account_detail($account)
    {
        $view = 'account-setting-detail';

        $table = '';
        $title = '';
        if ($account == 'current_assets') {
            $table = 'ml_current_assets';
            $title = 'Aktiva Lancar';
        } elseif ($account == 'fixed_assets') {
            $table = 'ml_fixed_assets';
            $title = 'Aktiva Tetap';
        } elseif ($account == 'accumulated_depreciation') {
            $table = 'ml_accumulated_depreciation';
            $title = 'Akumulasi Penyusutan';
        } elseif ($account == 'short_term_debt') {
            $table = 'ml_shortterm_debt';
            $title = 'Utang Jangka Pendek';
        } elseif ($account == 'long_term_debt') {
            $table = 'ml_longterm_debt';
            $title = 'Utang Jangka Panjang';
        } elseif ($account == 'capital') {
            $table = 'ml_capital';
            $title = 'Modal';
        } elseif ($account == 'income') {
            $table = 'ml_income';
            $title = 'Pendapatan';
        } elseif ($account == 'selling_cost') {
            $table = 'ml_selling_cost';
            $title = 'Biaya Penjualan';
        } elseif ($account == 'cost_good_sold') {
            $table = 'ml_cost_good_sold';
            $title = 'Harga Pokok Penjualan';
        } elseif ($account == 'admin_general_fees') {
            $table = 'ml_admin_general_fees';
            $title = 'Biaya Umum Admin';
        } elseif ($account == 'non_business_income') {
            $table = 'ml_non_business_income';
            $title = 'Pendapatan diluar Usaha';
        } elseif ($account == 'non_business_expenses') {
            $table = 'ml_non_business_expenses';
            $title = 'Biaya diluar Usaha';
        }

        $data = DB::table($table)
            ->where('userid', $this->user_id_manage(session('id')))
            ->orderBy('id')
            ->get();

        return view('main.account_setting_detail', compact('view', 'data', 'title', 'table'));
    }

    public function save_setting_account(Request $request)
    {
        $input = $request->all();
        $table_name = $input['account_table'];

        $rules = [
            'account_item.*' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}'];
            $html = '';
            foreach ($pesanarr as $p) {
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= str_replace(':', '', $o) . '<br>';
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        foreach ($input['id'] as $key => $value) {
            $cek = DB::table($table_name)->where('id', $value);
            $slug_str = str_replace(' ', '-', $input['account_item'][$key]);
            $slug = strtolower($slug_str);

            if ($cek->count() > 0) {
                DB::table($table_name)
                    ->where('id', $value)
                    ->update([
                        'name' => $input['account_item'][$key],
                        'code' => $slug,
                    ]);
            } else {
                DB::table($table_name)->insert([
                    'userid' => $this->user_id_manage(session('id')),
                    'transaction_id' => 0,
                    'account_code_id' => $input['account_code_id'][$key],
                    'code' => $slug,
                    'name' => $input['account_item'][$key],
                    'can_be_deleted' => 3,
                    'created' => time(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function petty_cash()
    {
        $view = 'petty-cash';
        $data = DB::table('ml_accounts')
            ->where('id', $this->user_id_manage(session('id')))
            ->first();

        return view('main.petty_cash', compact('view', 'data'));
    }

    public function petycash_update(Request $request)
    {
        $input = $request->all();
        $rules = [
            'petty_cash' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return Redirect()->back()->with('error', 'Petty Cash Is Required');
        }

        $cek = DB::table('ml_accounts')->where('id', $this->user_id_manage(session('id')));
        if ($cek->count() > 0) {
            DB::table('ml_accounts')
                ->where('id', $this->user_id_manage(session('id')))
                ->update(['petty_cash' => $input['petty_cash']]);
        } else {
            DB::table('ml_company')->insert([
                'userid' => $this->user_id_manage(session('id')),
                'company_email' => uniqid() . '@gmail.com',
                'company_name' => 'Randu App',
                'address' => '-',
                'domicile' => '-',
                'business_fields' => 'Randu',
                'npwp' => '-',
                'phone_number' => '-',
                'updated' => time(),
                'created' => time(),
                'tax' => 0,
                'petty_cash' => $input['petty_cash'],
            ]);
        }

        if ($input['petty_cash'] == 1) {
            $periksa = DB::table('ml_current_assets')
                ->where('userid', $this->user_id_manage(session('id')))
                ->where('code', 'kas-kecil');

            if ($periksa->count() > 0) {
            } else {
                DB::table('ml_current_assets')->insert([
                    'userid' => $this->user_id_manage(session('id')),
                    'transaction_id' => 0,
                    'account_code_id' => 1,
                    'code' => 'kas-kecil',
                    'name' => 'Kas Kecil',
                    'can_be_deleted' => 1,
                    'created' => time(),
                ]);
            }
        }

        return Redirect()->back()->with('success', 'Successfully Updated !');
    }

    public function ribuan($angka)
    {
        $angka_ribuan = number_format($angka);
        $angka_baru = str_replace(',', '.', $angka_ribuan);
        return $angka_baru;
    }

    public function initial_delete()
    {
        $view = 'initial-delete';
        return view('main.initial_delete', compact('view'));
    }
    public function payment_method_setting()
    {
        $view = 'payment-method-setting';
        $info = DB::table('ml_account_info')->where('user_id', session('id'))->first();
        if ($info && $info->payment_method != 'null') {
            $payment = base64_encode($info->payment_method);
        } else {
            $payment = null;
        }
        return view('main.payment_method_setting', compact('view', 'payment'));
    }
    public function confirm_hapus_saldo(Request $request)
    {
        $input = $request->all();

        $awal = $input['tahun'] . '-' . $input['bulan'] . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['bulan'], $input['tahun']);
        $end = $input['tahun'] . '-' . $input['bulan'] . '-' . $tanggal_akhir;

        $awal_time = strtotime($awal);
        $akhir_time = strtotime($end);

        $jurnal = Journal::where('userid', $this->user_id_manage(session('id')))
            ->where('transaction_name', 'Saldo Awal')
            ->where('is_opening_balance', 1)
            ->where('created', '>=', $awal_time)
            ->where('created', '<=', $akhir_time);

        foreach ($jurnal->get() as $j) {
            JournalList::where('journal_id', $j->id)->delete();
        }

        $jurnal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Saldo Awal Deleted !',
        ]);
    }

    public function store_payment_method(Request $request)
    {
        $payment_method = json_encode($request->paymentData, JSON_PRETTY_PRINT);
        $user_id = session('id');
        $account = DB::table('ml_account_info')->where('user_id', $user_id)->first();

        if ($account) {
            DB::table('ml_account_info')
                ->where('user_id', $user_id)
                ->update([
                    'payment_method' => $payment_method,
                ]);
            $accounts = true;
        } else {
            $accounts = DB::table('ml_account_info')->insert([
                'user_id' => $user_id,
                'payment_method' => $payment_method,
            ]);
        }

        if ($accounts) {
            $input = $request->all();
            $is_transfer = $input['paymentData'][2]['selected'];

            if ($is_transfer == 'true') {
                $cek = MlCurrentAsset::where('userid', session('id'))->where('code', 'bank-lain')->get();
                if ($cek->count() > 0) {
                } else {
                    MlCurrentAsset::insert([
                        'userid' => session('id'),
                        'transaction_id' => 0,
                        'account_code_id' => 1,
                        'code' => 'bank-lain',
                        'name' => 'Bank Lain',
                        'can_be_deleted' => 1,
                        'created' => time(),
                    ]);
                }
            }
            $response = ['title' => 'Berhasil', 'text' => 'Data Berhasil Disimpan', 'icon' => 'success'];
        } else {
            $response = ['title' => 'Gagal', 'text' => 'Data Gagal Disimpan', 'icon' => 'error'];
        }
        return response()->json($response);
    }

    public function showAccount()
    {
        $view = 'account-profile-settings';
        $accountID = session('id');
        $account = Account::find($accountID);
        return view('main.settings.account_profile', compact('view', 'account'));
    }

    public function updateAccount(UpdateAccountProfileRequest $request, string $id)
    {
        try {
            $id = (int) $id;
            $isChangedPassword = false;
            $account = Account::find($id);
            $imagePath = null;
            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $account->password)) {
                    throw new Error('Password lama tidak sesuai');
                }
                $isChangedPassword = true;
            }

            if ($isChangedPassword) {
                $account->password = bcrypt($request->password);
            }
            if ($request->filled('pin')) {
                $account->pin = $request->pin;
            }
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                if (!is_null($account->profile_picture)) {
                    Storage::delete('public/' . $account->profile_picture);
                }
                $imagePath = $file->store('images/' . $account->id, 'public');
                $account->profile_picture = $imagePath;
                session('profile_picture', $imagePath);
            }

            $account->clock_in = $request->clock_in;
            $account->clock_out = $request->clock_out;
            $account->holiday = json_encode($request['holiday']);
            $account->fullname = $request->fullname;
            $account->save();
            return redirect('/setting')->with('success', 'Sukses');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Data gagal diubah');
        }
    }

    public function printer_setting()
    {
        $view = 'printer-setting';

        $account = MlAccount::where('id', session('id'))->first();
        if (!isset($account->mlSettingUser)) {
            $data = MlSettingUser::create([
                'user_id' => $account->id,
            ]);
        } else {
            $data = $account->mlSettingUser;
        }

        return view('main.settings.printer_setting', compact('view', 'data'));
    }

    public function store_printer(Request $request)
    {
        $data = $request->all();
        $account = MlSettingUser::where('user_id', session('id'))->first();
        $account->update([
            'printer_connection' => $data['printer_connection'],
            'printer_paper_size' => $data['printer_paper_size'],
            'printer_custom_footer' => $data['printer_custom_footer'],
            'is_rounded' => $data['is_rounded'] == 1 ? true : false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diubah',
        ]);
    }

    public function confirmHapusAkun(Request $request)
    {
        $input = $request->all();

        $cek = JournalList::where('account_code_id', $input['code'])
            ->where('asset_data_id', $input['id'])
            ->count();
        if ($cek > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hapus gagal, Akun ini sudah digunakan dalam transaksi..!',
            ]);
        }

        if ($input['code'] == 1) {
            $table = 'ml_current_assets';
        } elseif ($input['code'] == 2) {
            $table = 'ml_fixed_assets';
        } elseif ($input['code'] == 3) {
            $table = 'ml_accumulated_depreciation';
        } elseif ($input['code'] == 4) {
            $table = 'ml_shortterm_debt';
        } elseif ($input['code'] == 5) {
            $table = 'ml_longterm_debt';
        } elseif ($input['code'] == 6) {
            $table = 'ml_capital';
        } elseif ($input['code'] == 7) {
            $table = 'ml_income';
        } elseif ($input['code'] == 9) {
            $table = 'ml_selling_cost';
        } elseif ($input['code'] == 8) {
            $table = 'ml_cost_good_sold';
        } elseif ($input['code'] == 10) {
            $table = 'ml_admin_general_fees';
        } elseif ($input['code'] == 11) {
            $table = 'ml_non_business_income';
        } elseif ($input['code'] == 12) {
            $table = 'ml_non_business_expenses';
        }

        DB::table($table)
            ->where('id', $input['id'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }
}
