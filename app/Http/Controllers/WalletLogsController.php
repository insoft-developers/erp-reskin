<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppMessageGlobal;
use App\Models\Account;
use App\Models\BusinessGroup;
use App\Models\MlAccount;
use App\Models\MlUserInformation;
use App\Models\WalletLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\DuitkuTrait;
use Illuminate\Support\Carbon;

class WalletLogsController extends Controller
{
    use DuitkuTrait;

    public function index()
    {
        $data['view'] = 'wallet-logs-list';
        $userId = $this->get_owner_id(session('id'));
        $bussinesGroup = BusinessGroup::where('user_id', $userId)->whereNotNull('no_rekening')->first();
        $config = DB::table('ml_site_config')->first();
        $data['min_withdraw'] = $config->min_withdraw;
        $data['min_topup'] = $config->min_topup;
        $data['fee_payment_gateway'] = $config->fee_payment_gateway;
        $data['fee_withdraw_in_rp'] = $this->formatRupiah($config->fee_withdraw);
        $data['min_withdraw_in_rp'] = $this->formatRupiah($config->min_withdraw);
        $data['min_topup_in_rp'] = $this->formatRupiah($config->min_topup);
        $data['latestBalance'] = DB::table('ml_accounts')
            ->where('id', session('id'))->first();

        if (!$bussinesGroup) {
            return redirect('/company_setting')->with('warning', 'Anda belum melengkapi data rekening bank anda, silahkan anda lengkapi terlebih dahulu');
        }

        $data['bulan'] = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $currentYear = date('Y');
        $data['tahun'] = range($currentYear - 5, $currentYear + 5);

        $data['sum1'] = WalletLogs::where('user_id', $userId)->where('group', 'withdraw')->sum('amount');
        $sumPlus = WalletLogs::where('user_id', $userId)->where('status', 3)->where('type', '+')->whereIn('group', ['income-pos', 'storefront'])->sum('amount');
        $sumMinus = WalletLogs::where('user_id', $userId)->where('status', 3)->where('type', '-')->whereIn('group', ['transaction-fee'])->sum('amount');
        $sum = $sumPlus - $sumMinus;
        $data['sum2'] = $sum;


        // yang process
        $sumPlus2 = WalletLogs::where('user_id', $userId)->where('status', 2)->where('type', '+')->whereIn('group', ['income-pos', 'storefront'])->sum('amount');
        $sumMinus2 = WalletLogs::where('user_id', $userId)->where('status', 2)->where('type', '-')->whereIn('group', ['transaction-fee'])->sum('amount');
        $sum2 = $sumPlus2 - $sumMinus2;
        $data['sum3'] = $sum2;


        // yang waiting
        $sumPlus3 = WalletLogs::where('user_id', $userId)->where('status', 0)->where('type', '+')->whereIn('group', ['income-pos', 'storefront'])->sum('amount');
        $sumMinus3 = WalletLogs::where('user_id', $userId)->where('status', 0)->where('type', '-')->whereIn('group', ['transaction-fee'])->sum('amount');
        $sum3 = $sumPlus3 - $sumMinus3;
        $data['sum4'] = $sum3;

        return view('main.wallet_logs_list', $data);
    }

    public function filterData(Request $request)
    {
        $userId = $this->get_owner_id(session('id'));
        $query = WalletLogs::where('user_id', $userId);
        $query2 = WalletLogs::where('user_id', $userId);

        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
            $query2->whereYear('created_at', $request->year);
        }

        if ($request->has('month') && $request->month != 0) {
            $query->whereMonth('created_at', $request->month);
            $query2->whereMonth('created_at', $request->month);
        }

        if ($request->sumOf === '1') {
            $sum = $query->where('group', 'withdraw')->sum('amount');
        } else if ($request->sumOf === '2') {
            $sumPlus = $query->where('status', 3)->where('type', '+')->whereIn('group', ['income-pos', 'storefront'])->sum('amount');
            $sumMinus = $query2->where('status', 3)->where('type', '-')->whereIn('group', ['transaction-fee'])->sum('amount');
            $sum = $sumPlus - $sumMinus;
        } else if ($request->sumOf === '3') {
            $sumPlus2 = $query->where('status', 2)->where('type', '+')->whereIn('group', ['income-pos', 'storefront'])->sum('amount');
            $sumMinus2 = $query2->where('status', 2)->where('type', '-')->whereIn('group', ['transaction-fee'])->sum('amount');
            $sum = $sumPlus2 - $sumMinus2;
        } else if ($request->sumOf === '4') {
            $sumPlus3 = $query->where('status', 0)->where('type', '+')->whereIn('group', ['income-pos', 'storefront'])->sum('amount');
            $sumMinus3 = $query2->where('status', 0)->where('type', '-')->whereIn('group', ['transaction-fee'])->sum('amount');
            $sum = $sumPlus3 - $sumMinus3;
        }

        return response()->json(['sum' => $sum]);
    }

    public function getData(Request $request)
    {
        $userId = $this->get_owner_id($request->session()->get('id'));
        $query = WalletLogs::where('user_id', $userId)->orderBy('id', 'desc');

        return Datatables::of($query)
            // ->addColumn('pilih', function ($row) {
            //     return '<input type="checkbox" id="id" data-id="' . $row->id . '">';
            // })
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('reference', function ($row) {
                return $row->reference;
            })
            ->addColumn('note', function ($row) {
                return $row->note;
            })
            ->addColumn('amount', function ($row) {
                return 'Rp. ' . number_format($row->amount, 0, ',', '.');
            })
            ->addColumn('type', function ($row) {
                return $row->type;
            })
            ->addColumn('payment_at', function ($row) {
                return Carbon::parse($row->updated_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('status', function ($row) {
                $btn = '<div class="d-flex flex-column">';
                switch ($row->status) {
                    case -1:
                        $btn .= '<div class="badge bg-danger">Rejected</div>';
                        break;
                    case 0:
                        $btn .= '<div class="badge bg-warning">Waiting</div>';
                        break;
                    case 1:
                        $btn .= '<div class="badge bg-info">Pending</div>';
                        break;
                    case 2:
                        $btn .= '<div class="badge bg-primary">Process</div>';
                        break;
                    case 3:
                        $btn .= '<div class="badge bg-success">Complete</div>';
                        break;
                    case 4:
                        $btn .= '<div class="badge bg-danger">Canceled</div>';
                        break;
                    default:
                        $btn .= '<div class="badge bg-secondary">Unknown</div>';
                        break;
                }

                $canceledCondition = Carbon::parse($row->payment_start_at)->addMinutes(10)->isPast();
                if (!$canceledCondition && $row->status === 0 && $row->payment_return_url) {
                    $btn .= '<a href="' . $row->payment_return_url . '" target="_blank" class="btn btn-success btn-sm mt-1" style="text-transform: none">Payment</a>';
                }

                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function topupDuitku(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|integer',
            'topup_charge' => 'required|integer'
        ]);

        $newTimestamp = date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s') . " +10 minutes"));

        $userId = $request->session()->get('id');
        $existingWallet = WalletLogs::where('user_id', $userId)
            ->where('status', '0')
            ->where('type', '+')
            ->where('created_at', '>', $newTimestamp)->first();
        if ($existingWallet) {
            return redirect()->away($this->getPaymentUrl($existingWallet->reference));
        } else {
            $amount = $request->amount;
            $productDetails = 'Transaksi Topup Wallet';
            $itemsDetails = [[
                'name' => 'Topup Transaksi ' . $amount,
                'price' => (int)$amount,
                'quantity' => 1,
            ]];

            $currentAccount = Account::where('id', session()->get('id'))->first();
            $detailAccount = MlUserInformation::where('user_id', $currentAccount->id)->first();
            $detailUser = [
                'email' => $currentAccount->email,
                'phone' => $detailAccount->phone_number ?? '',
                'username' => $currentAccount->username,
                'fullname' => $currentAccount->fullname,
            ];
            $invoice = $this->createInvoice($productDetails, $itemsDetails, route('wallet.index'), 'topup_wallet', $detailUser);
            $result = $invoice['result'];
            if ($invoice['httpCode'] === 200) {
                WalletLogs::create([
                    'user_id' => $request->session()->get('id'),
                    'amount' => $request->topup_charge,
                    'type' => '-',
                    'from' => 'Topup Wallet',
                    'group' => 'transaction-fee',
                    'note' => 'Transaksi Fee Randu Wallet - Topup Wallet',
                    'reference' => $result->reference,
                    'status' => '0',
                ]);
                WalletLogs::create([
                    'user_id' => $request->session()->get('id'),
                    'amount' => $request->amount,
                    'type' => '+',
                    'from' => 'Topup Wallet',
                    'group' => 'topup',
                    'note' => 'Topup Wallet Sebesar Rp. ' . number_format($request->amount, 0, ',', '.'),
                    'reference' => $result->reference,
                    'status' => '0',
                    'payment_return_url' => $result->paymentUrl,
                ]);

                return redirect()->away($result->paymentUrl);
            }
            return redirect()->back()->withErrors(['message' => $result]);
        }
    }

    public function withdrawDuitku(Request $request)
    {
        $request->merge([
            'amount' => preg_replace('/\D/', '', $request->input('amount'))
        ]);
        $config = DB::table('ml_site_config')->first();
        $min_withdraw = $config->min_withdraw;
        $fee_withdraw = $config->fee_withdraw;
        $request->validate([
            'amount' => 'required|numeric|integer|min:' . $min_withdraw . '|max:50000000',
        ], [
            'amount.required' => 'Jumlah penarikan harus diisi',
            'amount.numeric' => 'Jumlah penarikan harus berupa angka',
            'amount.integer' => 'Jumlah penarikan harus berupa bilangan bulat',
            'amount.min' => 'Jumlah penarikan minimal Rp. ' . number_format($min_withdraw, 0, ',', '.'),
            'amount.max' => 'Jumlah penarikan maksimal Rp. 50.000.000',
        ]);
        $userId = $request->session()->get('id');
        $mlAccount = DB::table('ml_accounts')->where('id', $userId)->first();

        if (!$mlAccount || ($request->amount + $fee_withdraw) > $mlAccount->balance) {
            return redirect()->back()->with('error', 'Maaf saldo tidak mencukupi');
        }

        $amount = $request->amount;
        DB::table('ml_accounts')->where('id', $userId)
            ->update(['balance' => $mlAccount->balance - ($amount + $fee_withdraw)]);

        $ref = $this->generateRandomString(24, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        WalletLogs::create([
            'user_id' => $userId,
            'amount' => $fee_withdraw,
            'reference' => $ref,
            'type' => '-',
            'from' => 'Withdraw',
            'group' => 'withdraw-fee',
            'payment_at' => null,
            'note' => 'Biaya Admin Penarikan Wallet',
            'status' => 1,
        ]);
        WalletLogs::create([
            'user_id' => $userId,
            'amount' => $amount,
            'reference' => $ref,
            'type' => '-',
            'from' => 'Withdraw',
            'group' => 'withdraw',
            'payment_at' => null,
            'note' => 'Penarikan Wallet Sebesar Rp. ' . number_format($amount, 0, ',', '.'),
            'status' => 1,
        ]);
        // ubah kalimat dibawah ini sesuai dengan kebutuhan admin finance
        $phoneFinance = env('RANDU_FINANCE_NUMBER', '6282233405862');
        $message = 'Pengajuan penarikan wallet sebesar Rp. ' . number_format($amount, 0, ',', '.') . ' oleh ' . $mlAccount->fullname . ' (' . $mlAccount->email . ')';
        SendWhatsAppMessageGlobal::dispatch($phoneFinance, $message)->onQueue('account_reset');

        return redirect()->back()->with('success', 'Pengajuan penarikan berhasil diajukan');
    }
}
