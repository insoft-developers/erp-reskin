<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BusinessGroup;
use App\Models\Category;
use App\Models\MlAccount;
use App\Models\MlBank;
use App\Models\RoCity;
use App\Models\RoDistrict;
use App\Models\RoProvince;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\MlCurrentAsset;
use App\Models\MlSettingUser;

class SettingController extends Controller
{
    use CommonApiTrait;
    use JournalTrait;

    public function company_setting(Request $request)
    {
        $input = $request->all();

        $userId = $this->user_id_staff($input['userid']);
        $data = BusinessGroup::with('province', 'city', 'district')->where('user_id', $userId)->first();

        $account = Account::where('id', $userId)->first();
        if ($data != null) {
            $data->tax = $account->tax;
        }

        $category = Category::all();
        $bank = MlBank::all();
        $cabang = DB::table('branches')->where('account_id', $userId)->first();

        $province = RoProvince::all();

        $response['data'] = $data;
        $response['category'] = $category;
        $response['bank'] = $bank;
        $response['cabang'] = $cabang;
        $response['province'] = $province;

        return response()->json([
            'success' => true,
            'data' => $response,
        ]);
    }

    public function city(Request $request)
    {
        $input = $request->all();

        $data = RoCity::where('province_id', $input['province'])->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function district(Request $request)
    {
        $input = $request->all();
        $data = RoDistrict::where('city_id', $input['city'])->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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

        $userId = $this->user_id_staff($input['userid']);
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

        return response()->json([
            'success' => true,
            'message' => 'Company Setting Successfully Updated !',
        ]);
    }

    public function upload(Request $request)
    {
        $ids = $request->ids;

        $path = storage_path('app/public/bussiness/logo');

        try {
            if ($request->has('image')) {
                $manager = new ImageManager(new Driver());
                $file = $request->image;
                $filename = uniqid() . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $img = $manager->read($file->path());
                $img->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path . '/' . $filename);
            } else {
                return response()->json(['message' => trans('/storage/test/' . 'def.png.')], 200);
            }

            $data = BusinessGroup::findorFail((int) $ids);
            $data->logo = 'bussiness/logo/' . $filename;
            $data->save();

            return response()->json(['message' => trans('/storage/test/' . $filename)], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function petty_cash(Request $request)
    {
        $input = $request->all();
        $data = DB::table('ml_accounts')
            ->where('id', $this->user_id_staff($input['userid']))
            ->first();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function petycash_update(Request $request)
    {
        $input = $request->all();
        $rules = [
            'petty_cash' => 'required',
        ];

        $validator = Validator::make($input, $rules);
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

        $cek = DB::table('ml_accounts')->where('id', $this->user_id_staff($input['userid']));
        if ($cek->count() > 0) {
            DB::table('ml_accounts')
                ->where('id', $this->user_id_staff($input['userid']))
                ->update(['petty_cash' => $input['petty_cash']]);
        } else {
            DB::table('ml_company')->insert([
                'userid' => $this->user_id_staff($input['userid']),
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
                ->where('userid', $this->user_id_staff($input['userid']))
                ->where('code', 'kas-kecil');

            if ($periksa->count() > 0) {
            } else {
                DB::table('ml_current_assets')->insert([
                    'userid' => $this->user_id_staff($input['userid']),
                    'transaction_id' => 0,
                    'account_code_id' => 1,
                    'code' => 'kas-kecil',
                    'name' => 'Kas Kecil',
                    'can_be_deleted' => 1,
                    'created' => time(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated!',
        ]);
    }

    public function payment_setting(Request $request)
    {
        $input = $request->all();
        $info = DB::table('ml_account_info')
            ->where('user_id', $this->user_id_staff($input['userid']))
            ->first();
        if ($info && $info->payment_method != 'null') {
            $payment = json_decode($info->payment_method);
        } else {
            $payment = null;
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    public function payment_setting_update(Request $request)
    {
        $input = $request->all();
        //  return response()->json([
        //     'success' => true,
        //     'message' => $input
        // ]);

        $user_id = $this->user_id_staff($input['userid']);
        $account = DB::table('ml_account_info')->where('user_id', $user_id)->first();

        if ($account) {
            $data = [];

            $banks = [];

            $template_banks = ['Bank BCA', 'Bank Mandiri', 'Bank BNI', 'Bank BRI', ''];

            $remarks = ['bank-bca', 'bank-mandiri', 'bank-bni', 'bank-bri', 'bank-lain'];

            for ($i = 0; $i < count($input['banks']); $i++) {
                $r['id'] = $i + 1;
                $r['bank'] = $input['banks'][$i] == null ? $template_banks[$i] : $input['banks'][$i];
                $r['remark'] = $remarks[$i];
                $r['bankOwner'] = $input['owners'][$i];
                $r['bankAccountNumber'] = $input['rekenings'][$i];
                $r['selected'] = $input['bank_selecteds'][$i] == true ? 'true' : 'false';

                array_push($banks, $r);
            }

            $row['id'] = 1;
            $row['method'] = 'Cash';
            $row['selected'] = $input['cash'] == 0 ? 'true' : 'false';

            array_push($data, $row);

            $row2['id'] = 2;
            $row2['method'] = 'Online-Payment';
            $row2['selected'] = $input['pg'] == 0 ? 'true' : 'false';

            array_push($data, $row2);

            $row3['id'] = 3;
            $row3['method'] = 'Transfer';
            $row3['selected'] = $input['transfer'] == 0 ? 'true' : 'false';
            $row3['banks'] = $banks;

            array_push($data, $row3);

            $row4['id'] = 4;
            $row4['method'] = 'COD';
            $row4['selected'] = $input['cod'] == 0 ? 'true' : 'false';

            array_push($data, $row4);

            $row5['id'] = 5;
            $row5['method'] = 'Marketplace';
            $row5['selected'] = $input['marketplace'] == 0 ? 'true' : 'false';

            array_push($data, $row5);

            $row6['id'] = 6;
            $row6['method'] = 'Piutang';
            $row6['selected'] = $input['piutang'] == 0 ? 'true' : 'false';

            array_push($data, $row6);

            $row7['id'] = 7;
            $row7['method'] = 'QRIS';
            $row7['selected'] = $input['qris'] == 0 ? 'true' : 'false';

            array_push($data, $row7);

            $payment_method = json_encode($data, JSON_PRETTY_PRINT);
            DB::table('ml_account_info')
                ->where('user_id', $user_id)
                ->update([
                    'payment_method' => $payment_method,
                ]);
            $accounts = true;
        } else {
            $data2 = [
                [
                    'id' => '1',
                    'method' => 'Cash',
                    'selected' => 'false',
                ],
                [
                    'id' => '2',
                    'method' => 'Online-Payment',
                    'selected' => 'false',
                ],
                [
                    'id' => '3',
                    'method' => 'Transfer',
                    'selected' => 'false',
                    'banks' => [
                        [
                            'id' => '1',
                            'bank' => 'Bank BCA',
                            'remark' => 'bank-bca',
                            'bankOwner' => null,
                            'bankAccountNumber' => null,
                            'selected' => 'false',
                        ],
                        [
                            'id' => '2',
                            'bank' => 'Bank Mandiri',
                            'remark' => 'bank-mandiri',
                            'bankOwner' => null,
                            'bankAccountNumber' => null,
                            'selected' => 'false',
                        ],
                        [
                            'id' => '3',
                            'bank' => 'Bank BNI',
                            'remark' => 'bank-bni',
                            'bankOwner' => null,
                            'bankAccountNumber' => null,
                            'selected' => 'false',
                        ],
                        [
                            'id' => '4',
                            'bank' => 'Bank BRI',
                            'remark' => 'bank-bri',
                            'bankOwner' => null,
                            'bankAccountNumber' => null,
                            'selected' => 'false',
                        ],
                        [
                            'id' => '5',
                            'bank' => 'Bank Lain',
                            'remark' => 'bank-lain',
                            'bankOwner' => null,
                            'bankAccountNumber' => null,
                            'selected' => 'false',
                        ],
                    ],
                ],
                [
                    'id' => '4',
                    'method' => 'COD',
                    'selected' => 'false',
                ],
                [
                    'id' => '5',
                    'method' => 'Marketplace',
                    'selected' => 'false',
                ],
                [
                    'id' => '6',
                    'method' => 'Piutang',
                    'selected' => 'false',
                ],
                [
                    'id' => '7',
                    'method' => 'QRIS',
                    'selected' => 'false',
                ],
            ];
            $payment_method = json_encode($data2, JSON_PRETTY_PRINT);
            $accounts = DB::table('ml_account_info')->insert([
                'user_id' => $user_id,
                'payment_method' => $payment_method,
            ]);
        }

        if ($accounts) {
            $input = $request->all();
            $is_transfer = $input['transfer'];

            if ($is_transfer == 0) {
                $cek = MlCurrentAsset::where('userid', $user_id)->where('code', 'bank-lain')->get();
                if ($cek->count() > 0) {
                } else {
                    MlCurrentAsset::insert([
                        'userid' => $user_id,
                        'transaction_id' => 0,
                        'account_code_id' => 1,
                        'code' => 'bank-lain',
                        'name' => 'Bank Lain',
                        'can_be_deleted' => 1,
                        'created' => time(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan data!',
            ]);
        }
    }


    public function printer_setting(Request $request) {
        $input = $request->all();
        $account = MlAccount::where('id', $this->user_id_staff($input['userid']))->first();
        if (!isset($account->mlSettingUser)) {
            $data = MlSettingUser::create([
                'user_id' => $account->id,
            ]);
        } else {
            $data = $account->mlSettingUser;
        }

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }


    public function printer_setting_update(Request $request) {
       
        $data = $request->all();
        $account = MlSettingUser::where('user_id', $this->user_id_staff($data['userid']))->first();
        $account->update([
            'printer_connection' => $data['printer_connection'],
            'printer_paper_size' => $data['printer_paper_size'],
            'printer_custom_footer' => $data['printer_custom_footer'],
            'is_rounded' => $data['is_rounded'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diubah',
        ]);
    
    }
 }
