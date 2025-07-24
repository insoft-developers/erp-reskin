<?php

namespace App\Http\Controllers\Main;

use App\Exports\PajakExport;
use App\Http\Controllers\Controller;
use App\Models\MdProduct;
use App\Models\MlAccount;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPajakController extends Controller
{
    public function index(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'laporan-pajak';
        $bulan = [
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
        $tahun = range($currentYear - 5, $currentYear + 5);

        return view('main.report.tax.index', compact('view', 'bulan', 'tahun', 'userKey', 'from'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('reference', function ($data) {
                return $data->reference;
            })
            ->addColumn('created', function ($data) {
                return $data->created;
            })
            ->addColumn('customer', function ($data) {
                return $data->cust_name ?? ($data->customer->name ?? '-');
            })
            ->addColumn('paid', function ($data) {
                return 'Rp. ' . number_format($data->paid, 0, ',', '.');
            })
            ->addColumn('tax', function ($data) {
                return 'Rp. ' . number_format($data->tax, 0, ',', '.');
            })
            ->addColumn('payment_method', function ($data) {
                // return paymentMethodCast($data->payment_method);
                return $data->payment_method;
            })

            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'user_id',
            'reference',
            'created',
            'customer_id',
            'cust_name',
            'paid',
            'tax',
            'payment_method',
        ];

        $keyword = $request->keyword;
        $month = $request->month ?? '';
        $year = $request->year ?? '';

        $user_id = session('id') ?? Auth::user()->id;
        $ownerId = $this->get_owner_id($user_id);
        $checkUser = MlAccount::find($user_id);
        $user_id = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');


        $data = Penjualan::orderBy('id', 'asc')
            ->select($columns)
            ->when($month, function ($query) use ($month) {
                if ($month != '') {
                    return $query->whereMonth('created', $month);
                }
            })
            ->when($year, function ($query) use ($year) {
                if ($year != '') {
                    return $query->whereYear('created', $year);
                }
            })
            ->whereIn('user_id', $user_id)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('reference', 'like', '%' . $keyword . '%');
            });

        foreach ($data as $key => $value) {
            $penjualanProduct = $value->products();

            $penjualanProduct->select('quantity', 'price', 'created')->get();
            $pajak_akun = MlAccount::where('id', $ownerId)->select('tax')->first();
            $value['total_pajak'] = ($value->paid * $pajak_akun->tax) / 100;
        }

        return $data;
    }

    public function chart(Request $request)
    {
        $data = $this->getData($request)->get();

        $data['total_pajak'] = 'Rp. ' . number_format(collect($data)->sum('tax'));

        return $data;
    }

    public function export(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $data = $this->getData($request)->get();

        $dateName = Carbon::create($year, $month, 1)->locale('id')->isoFormat('MMMM YYYY');

        return Excel::download(new PajakExport($data), "Laporan Pajak $dateName.xlsx");
    }
}
