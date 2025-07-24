<?php

namespace App\Http\Controllers\Main;

use App\DataTables\LaporanPenjualanProductDataTable;
use App\Exports\LaporanPenjualanExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ManajemenPesananController;
use App\Jobs\ExportSalesReportJob;
use App\Models\Branch;
use App\Models\JobsValidation;
use App\Models\JournalList;
use App\Models\MdExpense;
use App\Models\MdExpenseCategory;
use App\Models\MdExpenseCategoryProduct;
use App\Models\MdProduct;
use App\Models\MlAccount;
use App\Models\MlCostGoodSold;
use App\Models\MtPengeluaranOutlet;
use App\Models\PaymentMethodFlags;
use App\Models\Penjualan;
use PDF;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenjualanAdvanceController extends Controller
{
    /**
     * Display a listing of the Staffs.
     */
    public function index(LaporanPenjualanProductDataTable $dataTable)
    {
        $userKey = app()->request->user_key ?? null;

        $isFree = false;
        $user = MlAccount::find(session('id'));
        if (!$user->is_upgraded) {
            $isFree = true;
        }

        $staffs = DB::table('ml_accounts')
            ->where('branch_id', $user->branch_id)
            ->select('id', 'fullname')
            ->get();

        $flags = PaymentMethodFlags::where('user_id', $user->branch_id)->get();
        $paymentMethods = Penjualan::select('payment_method')->whereNotNull('payment_method')->distinct()->pluck('payment_method');
        $from = app()->request->from ?? 'desktop';

        return $dataTable->render('main.report.sales-advance.index', compact('userKey', 'from', 'staffs', 'flags', 'paymentMethods', 'isFree'));
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'user_id',
            'name',
            'quantity',
            'price',
            'cost'

        ];

        $keyword = $request->keyword;
        $date = $request->date ?? 'isThisMonth';
        $start_date = $request->start_date . ' 00:00:00';
        $end_date = $request->end_date . ' 23:59:59';
        $user_id = session('id') ?? $request->user_id;
        $checkUser = MlAccount::find($user_id);
        $user_id = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');
        $staff_id = $request->staff_id;
        $payment_method = $request->payment_method;
        $flag_id = $request->flag_id;
        $price_type = $request->price_type;

        $data = MdProduct::orderBy('id', 'asc')
            ->select($columns)
            ->whereIn('user_id', $user_id)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->get();

        foreach ($data as $key => $value) {
            $penjualanProduct = $value->penjualanProduct();
            if ($date == 'isToday') {
                $penjualanProduct->whereDate('created', now());
            } elseif ($date == 'isYesterday') {
                $penjualanProduct->whereDate('created', now()->subDay());
            } elseif ($date == 'isThisMonth') {
                $penjualanProduct->whereMonth('created', now()->month);
            } elseif ($date == 'isLastMonth') {
                $penjualanProduct->whereMonth('created', now()->subMonth()->month);
            } elseif ($date == 'isThisYear') {
                $penjualanProduct->whereYear('created', now()->year);
            } elseif ($date == 'isLastYear') {
                $penjualanProduct->whereYear('created', now()->subYear()->year);
            } elseif ($date == 'isRangeDate') {
                $penjualanProduct->whereBetween('created', [$start_date, $end_date]);
            }

            $penjualanProduct = $penjualanProduct->select('quantity', 'price', 'created')
                ->whereHas('penjualan', function ($query) use ($flag_id, $staff_id, $payment_method, $price_type) {
                    $query->where('payment_status', 1);
                    if ($flag_id) {
                        $query->where('flag_id', $flag_id);
                    }
                    if ($staff_id) {
                        $query->where('staff_id', $staff_id);
                    }
                    if ($payment_method) {
                        $query->where('payment_method', $payment_method);
                    }
                    if ($price_type) {
                        $query->where('price_type', $price_type);
                    }
                })
                ->get();

            $harga_jual = $value->price ?? 0;
            $jumlah_penjualan = $penjualanProduct->sum('quantity');
            $total_harga_produk_terjual = $harga_jual * $jumlah_penjualan;


            $hpp = $jumlah_penjualan * $value->cost ?? 0;

            if ($total_harga_produk_terjual > 0) {
                $margin_kotor = ($total_harga_produk_terjual - $hpp);
                $persentase_margin = ($margin_kotor * 100) / $total_harga_produk_terjual;
                $laba_rugi_bersih = $total_harga_produk_terjual - $hpp;
            } else {
                $margin_kotor = 0;
                $persentase_margin = 0;
                $laba_rugi_bersih = 0;
            }

            $manajemenPesanan = new ManajemenPesananController();
            $newRequest = new Request([
                'selected_range' => $request->date ?? 'isThisMonth',
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
                'price_type' => $request->price_type,
                'user_id' => session('id') ?? $request->user_id,
            ]);
            $penjualan = $manajemenPesanan->getDataCart($newRequest);
            $responseData = json_decode($penjualan->getContent(), true);
            $omset_penjualan = $responseData['data']['omset_penjualan_no_format'];

            $harga_jual = $value->price ?? 0;
            $value['jumlah_penjualan'] = $jumlah_penjualan;
            $value['total_harga_produk_terjual'] = $harga_jual * $jumlah_penjualan;
            $value['harga_jual'] = number_format($harga_jual);
            $value['hpp_produk'] = number_format($value->cost);
            $value['hpp'] = $hpp;
            $value['margin_kotor'] = $margin_kotor;
            $value['persentase_margin'] = round($persentase_margin, 2);
            $value['laba_rugi_bersih'] = $laba_rugi_bersih;

            $pajak_akun = MlAccount::whereIn('id', $user_id)->select('tax')->first();
            $value['total_pajak'] = ($omset_penjualan * $pajak_akun->tax) / 100;
        }

        // $data = $data->where('total_harga_produk_terjual', '>', 0)->sortByDesc('total_harga_produk_terjual')->values();
        $data = $data->filter(function ($item) {
            return $item->total_harga_produk_terjual > 0 || $item->jumlah_penjualan > 0;
        })->sortByDesc('total_harga_produk_terjual')->values();

        return $data;
    }

    public function chart(Request $request)
    {
        $date = $request->date ?? 'isThisMonth';
        $start_date = $request->start_date . ' 00:00:00';
        $end_date = $request->end_date . ' 23:59:59';
        $expense_category_id = $request->expense_category_id;
        $user_id = session('id') ?? $request->user_id;
        // $user_id = $this->get_branch_id(session('id') ?? $request->user_id);

        $akuns = MlCostGoodSold::where('userid', $this->set_user_id($user_id))->where('code', 'harga-pokok-penjualan')->first();
        $akun_hpp = $akuns->id;
        // $list = JournalList::where('asset_data_id', $akun_hpp)->where('account_code_id', 8);
        $list = Penjualan::where('branch_id', $this->get_branch_id($user_id));

        if ($request->price_type && $request->price_type !== null) {
            $list->where('price_type', $request->price_type);
        }

        // filter payment_method
        if ($request->payment_method) {
            $list->where('payment_method', $request->payment_method);
        }

        if ($request->staff_id) {
            $staff_id = $request->staff_id;
            $list->where('staff_id', $staff_id);
            // $list->whereHas('journal', function ($query) use ($staff_id) {
            //     $query->where('userid', $staff_id);
            // });
        }

        if ($request->flag_id) {
            $list->where('flag_id', $request->flag_id);
        }

        if ($request->payment_method) {
            $list->where('payment_method', $request->payment_method);
        }

        $biaya = MdExpense::where('user_id', $this->set_user_id($user_id))
            ->whereHas('md_expense_category', function ($q) use ($expense_category_id) {
                if ($expense_category_id != '') {
                    $q->where('id', $expense_category_id);
                }
            });

        $list->wherePayment_status(1);
        if ($date == 'isToday') {
            $biaya->whereDate('date', now());
            // $hpp = $list->where('created', $this->date_helper('today'))->sum('hpp');
            $hpp = $list->whereDate('custom_date', now())->sum('hpp');
        } elseif ($date == 'isYesterday') {
            $biaya->whereDate('date', now()->subDay());
            // $hpp = $list->where('custom_date', $this->date_helper('yesterday'))->sum('hpp');
            $hpp = $list->whereDate('custom_date', now()->subDay())->sum('hpp');
        } elseif ($date == 'isThisMonth') {
            $biaya->whereMonth('date', now()->month);
            // $hpp = $list->where('custom_date', '>=', $this->date_helper('this-month')['awal'])->where('created', '<=', $this->date_helper('this-month')['akhir'])->sum('hpp');
            $hpp = $list->whereMonth('custom_date', now()->month)->sum('hpp');
        } elseif ($date == 'isLastMonth') {
            $biaya->whereMonth('date', now()->subMonth()->month);
            // $hpp = $list->where('custom_date', '>=', $this->date_helper('last-month')['awal'])->where('created', '<=', $this->date_helper('last-month')['akhir'])->sum('hpp');
            $hpp = $list->whereMonth('custom_date', now()->subMonth()->month)->sum('hpp');
        } elseif ($date == 'isThisYear') {
            $biaya->whereYear('date', now()->year);
            // $hpp = $list->where('custom_date', '>=', $this->date_helper('this-year')['awal'])->where('created', '<=', $this->date_helper('this-year')['akhir'])->sum('hpp');
            $hpp = $list->whereYear('custom_date', now()->year)->sum('hpp');
        } elseif ($date == 'isLastYear') {
            $biaya->whereYear('date', now()->subYear()->year);
            // $hpp = $list->where('custom_date', '>=', $this->date_helper('last-year')['awal'])->where('created', '<=', $this->date_helper('last-year')['akhir'])->sum('hpp');
            $hpp = $list->whereYear('custom_date', now()->subYear()->year)->sum('hpp');
        } elseif ($date == 'isRangeDate') {
            $biaya->whereBetween('date', [$start_date, $end_date]);
            $waktu_1 = strtotime(date('Y-m-d', strtotime($start_date)));
            $waktu_2 = strtotime(date('Y-m-d', strtotime($end_date)));
            // $hpp = $list->where('custom_date', '>=', $waktu_1)->where('created', '<=', $waktu_2)->sum('hpp');
            $hpp = $list->whereBetween('custom_date', [$start_date, $end_date])->sum('hpp');
        }

        $checkUser = MlAccount::find($user_id);
        $user_id = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');
        // if ($checkUser->branch_id == $user_id) {
        // $user_id = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');
        // }else{
        //     $user_id = [$user_id];
        // }
        $pengeluaran = MtPengeluaranOutlet::whereIn('user_id', $user_id);

        if ($date == 'isToday') {
            $pengeluaran->whereDate('created_at', now());
        } elseif ($date == 'isYesterday') {
            $pengeluaran->whereDate('created_at', now()->subDay());
        } elseif ($date == 'isThisMonth') {
            $pengeluaran->whereMonth('created_at', now()->month);
        } elseif ($date == 'isLastMonth') {
            $pengeluaran->whereMonth('created_at', now()->subMonth()->month);
        } elseif ($date == 'isThisYear') {
            $pengeluaran->whereYear('created_at', now()->year);
        } elseif ($date == 'isLastYear') {
            $pengeluaran->whereYear('created_at', now()->subYear()->year);
        } elseif ($date == 'isRangeDate') {
            $pengeluaran->whereBetween('created_at', [$start_date, $end_date]);
        }

        $biaya = $biaya->sum('amount');
        $pengeluaran = $pengeluaran->sum('amount');

        $penjualan_product = $this->getData($request);

        $total_harga_produk_terjual = collect($penjualan_product)->sum('total_harga_produk_terjual');
        // $hpp = collect($penjualan_product)->sum('hpp');

        $laba_rugi_bersih_chart = $total_harga_produk_terjual - $hpp - $biaya;

        $manajemenPesanan = new ManajemenPesananController();
        $newRequest = new Request([
            'selected_range' => $request->date ?? 'isThisMonth',
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'price_type' => $request->price_type,
            'user_id' => session('id') ?? $request->user_id,
        ]);
        $penjualan = $manajemenPesanan->getDataCart($newRequest);

        $responseData = json_decode($penjualan->getContent(), true);

        $omset_penjualan = $responseData['data']['omset_penjualan_no_format'];
        $data['jumlah_terjual'] = collect($penjualan_product)->sum('jumlah_penjualan');
        $data['hpp'] = 'Rp. ' . customNumberFormat($hpp);
        $data['total_harga_produk_terjual'] = 'Rp. ' . customNumberFormat($total_harga_produk_terjual);
        $total_biaya_pengeluaran = $pengeluaran + $biaya;

        if ($total_biaya_pengeluaran > 0) {
            $roas = $omset_penjualan / $total_biaya_pengeluaran;
        } else {
            $roas = 0;
        }

        $data['biaya'] = 'Rp. ' . customNumberFormat($total_biaya_pengeluaran);
        $laba_rugi_bersih = $omset_penjualan - $total_biaya_pengeluaran - $hpp;
        // dd("$omset_penjualan - $total_biaya_pengeluaran - $hpp");
        $data['laba_rugi_bersih'] = 'Rp. ' . customNumberFormat($laba_rugi_bersih);
        $data['roas'] = round($roas, 2);
        // $data['total_pajak'] = 'Rp. ' . customNumberFormat(collect($penjualan_product)->sum('total_pajak'));
        $data['total_pajak'] = $responseData['data']['total_tax'];

        return $data;
    }

    public function exportExcel(Request $request)
    {
        $date = $request->date ?? 'isThisMonth';
        if ($date == 'isToday') {
            $dateName = "Hari ini";
        } elseif ($date == 'isYesterday') {
            $dateName = "Kemarin";
        } elseif ($date == 'isThisMonth') {
            $dateName = "Bulan ini";
        } elseif ($date == 'isLastMonth') {
            $dateName = "Bulan Kemarin";
        } elseif ($date == 'isThisYear') {
            $dateName = "Tahun ini";
        } elseif ($date == 'isLastYear') {
            $dateName = "Tahun Kemarin";
        } elseif ($date == 'isRangeDate') {
            $dateName = $request->start_date . ' - ' . $request->end_date;
        }

        $data = $this->getData($request);

        $cart = $this->chart($request);
        $newRequest = new Request([
            'selected_range' => $request->date  ?? 'isThisMonth',
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'price_type' => $request->price_type,
        ]);
        $manajemenPesanan = new ManajemenPesananController();
        $penjualan = $manajemenPesanan->getDataCart($newRequest);
        $responseData = json_decode($penjualan->getContent(), true);
        $cart['omset_penjualan'] = $responseData['data']['omset_penjualan'];
        $cart['total_ongkir'] = $responseData['data']['total_ongkir'];
        $cart['total_diskon'] = $responseData['data']['total_diskon'];
        // return PDF::loadView('main.report.sales.export', [
        //     'data' => $data,
        //     'cart' => $cart,
        // ])->download("Laporan Penjualan $dateName.pdf");

        return Excel::download(new LaporanPenjualanExport($data, $cart), "Laporan Penjualan $dateName.xlsx");
    }

    public function exportPdf(Request $request)
    {
        $date = $request->date ?? 'isThisMonth';
        if ($date == 'isToday') {
            $dateName = "Hari ini";
        } elseif ($date == 'isYesterday') {
            $dateName = "Kemarin";
        } elseif ($date == 'isThisMonth') {
            $dateName = "Bulan ini";
        } elseif ($date == 'isLastMonth') {
            $dateName = "Bulan Kemarin";
        } elseif ($date == 'isThisYear') {
            $dateName = "Tahun ini";
        } elseif ($date == 'isLastYear') {
            $dateName = "Tahun Kemarin";
        } elseif ($date == 'isRangeDate') {
            $dateName = $request->start_date . ' - ' . $request->end_date;
        }

        $data = $this->getData($request);

        $cart = $this->chart($request);

        $newRequest = new Request([
            'selected_range' => $request->date ?? 'isThisMonth',
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'price_type' => $request->price_type,
        ]);
        $manajemenPesanan = new ManajemenPesananController();
        $penjualan = $manajemenPesanan->getDataCart($newRequest);
        $responseData = json_decode($penjualan->getContent(), true);
        $cart['omset_penjualan'] = $responseData['data']['omset_penjualan'];
        $cart['total_ongkir'] = $responseData['data']['total_ongkir'];
        $cart['total_diskon'] = $responseData['data']['total_diskon'];

        // return view('main.report.sales.exportPdf', [
        //     'data' => $data,
        //     'cart' => $cart,
        //     'dateName' => $dateName
        // ]);
        $pdf = PDF::loadView('main.report.sales.exportPdf', [
            'data' => $data,
            'cart' => $cart,
            'dateName' => $dateName
        ]);

        // Download the generated PDF
        // return $pdf->download("Laporan Penjualan $dateName.pdf");

        // Stream the generated PDF
        return $pdf->stream("Laporan Penjualan $dateName.pdf");
    }

    public function exportExcelQueue(Request $request)
    {
        $user = MlAccount::find(session('id'));

        if (!$user || !$user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email pengguna tidak ditemukan. Silakan lengkapi email di profil Anda.'
            ], 400);
        }

        $jobValidation = JobsValidation::where('user_id', $user->id)
            ->where('job_title', 'Export Sales Report')
            ->first();

        if ($jobValidation) {
            // Job is already in progress
            return response()->json([
                'success' => false,
                'message' => 'Laporan sedang diproses. Silakan tunggu beberapa menit.'
            ], 400);
        }

        JobsValidation::create([
            'user_id' => $user->id,
            'job_title' => 'Export Sales Report'
        ]);

        // Prepare filters
        $filters = [
            'date' => $request->date ?? 'isThisMonth',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'staff_id' => $request->staff_id,
            'payment_method' => $request->payment_method,
            'flag_id' => $request->flag_id,
            'price_type' => $request->price_type,
            'expense_category_id' => $request->expense_category_id,
            'user_id' => session('id'),
        ];

        // Dispatch the job
        ExportSalesReportJob::dispatch($user->id, 'excel', $filters)->onQueue('export_laporan_advance');

        return response()->json([
            'success' => true,
            'message' => 'Laporan sedang diproses. File akan dikirim ke email Anda dalam beberapa menit.'
        ]);
    }

    public function exportPdfQueue(Request $request)
    {
        $user = MlAccount::find(session('id'));

        if (!$user || !$user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email pengguna tidak ditemukan. Silakan lengkapi email di profil Anda.'
            ], 400);
        }

        $jobValidation = JobsValidation::where('user_id', $user->id)
            ->where('job_title', 'Export Sales Report')
            ->first();

        if ($jobValidation) {
            // Job is already in progress
            return response()->json([
                'success' => false,
                'message' => 'Laporan sedang diproses. Silakan tunggu beberapa menit.'
            ], 400);
        }

        JobsValidation::create([
            'user_id' => $user->id,
            'job_title' => 'Export Sales Report'
        ]);

        // Prepare filters
        $filters = [
            'date' => $request->date ?? 'isThisMonth',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'staff_id' => $request->staff_id,
            'payment_method' => $request->payment_method,
            'flag_id' => $request->flag_id,
            'price_type' => $request->price_type,
            'expense_category_id' => $request->expense_category_id,
            'user_id' => session('id'),
        ];

        // Dispatch the job
        ExportSalesReportJob::dispatch($user->id, 'pdf', $filters)->onQueue('export_laporan_advance');

        return response()->json([
            'success' => true,
            'message' => 'Laporan sedang diproses. File akan dikirim ke email Anda dalam beberapa menit.'
        ]);
    }

    protected function set_user_id($userid)
    {
        $user = MlAccount::findorFail($userid);
        if ($user->role_code != 'staff') {
            return $user->id;
        } else {
            $branch = Branch::findorFail($user->branch_id);
            return $branch->account_id;
        }
    }

    protected function date_helper($desc)
    {
        if ($desc == 'today') {
            return strtotime(date('Y-m-d'));
        } elseif ($desc == 'yesterday') {
            return strtotime(date('Y-m-d', strtotime('yesterday')));
        } elseif ($desc == 'this-month') {
            $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
            $start = strtotime(date('Y-m-01'));
            $end = strtotime(date('Y-m-' . $tanggal_akhir));
            $data['awal'] = $start;
            $data['akhir'] = $end;
            return $data;
        } elseif ($desc == 'last-month') {
            $tanggal = date('Y-m-d', strtotime('-1 month'));
            $bulan = date('m', strtotime($tanggal));
            $tahun = date('Y', strtotime($tanggal));

            $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $start = $tahun . '-' . $bulan . '-01';
            $end = $tahun . '-' . $bulan . '-' . $tanggal_akhir;
            $data['awal'] = strtotime($start);
            $data['akhir'] = strtotime($end);
            return $data;
        } elseif ($desc == 'this-year') {
            $tanggal = date('Y-m-d');
            $bulan = date('m', strtotime($tanggal));
            $tahun = date('Y', strtotime($tanggal));

            $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, '12', $tahun);
            $start = $tahun . '-01-01';
            $end = $tahun . '-12-' . $tanggal_akhir;
            $data['awal'] = strtotime($start);
            $data['akhir'] = strtotime($end);
            return $data;
        } elseif ($desc == 'last-year') {
            $tanggal = date('Y-m-d', strtotime('-1 year'));
            $bulan = date('m', strtotime($tanggal));
            $tahun = date('Y', strtotime($tanggal));

            $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, '12', $tahun);

            $start = $tahun . '-01-01';
            $end = $tahun . '-12-' . $tanggal_akhir;
            $data['awal'] = strtotime($start);
            $data['akhir'] = strtotime($end);
            return $data;
        }
    }
}
