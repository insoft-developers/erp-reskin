<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\BusinessGroup;
use App\Models\MlAccount;
use App\Models\MtRekapitulasiHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapitulasiHarianController extends Controller
{
    public function index(Request $request)
    {
        // $user = MlAccount::where('id', session('id'))->first();
        // $branch_id = MlAccount::where('branch_id', $user->branch_id)->pluck('id')->toArray();
        $ownerId = $this->get_owner_id(session('id'));
        $branch_id = MlAccount::where('id', $ownerId)->first()->branch_id;

        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'rekapitulasi-harian';
        $columns = [
            'id',
            'user_id',
            'brach_id',
            'mt_kas_kecil_id',
            'initial_cash',
            'cash_sale',
            'transfer_sales',
            'payment_gateway_sales',
            'piutang_sales',
            'outlet_output',
            'total_cash',
            'total_sales',
            'created_at',
        ];

        $date = $request->date ?? date('Y-m-d');
        $userId = $this->get_owner_id(session('id'));

        $data = MtRekapitulasiHarian::orderBy('id', 'desc')
                    ->select($columns)
                    ->where('brach_id', $branch_id)
                    ->whereDate('created_at', $date)
                    ->get();

        foreach ($data as $key => $value) {
            $branch_name = BusinessGroup::where('user_id', $userId)->first();
            $value['nama_toko'] = $branch_name->branch_name ?? $value->user()->first()->business_group->branch_name;
        }
        
        return view('main.rekapitulasi_harian.index', compact('view', 'data', 'userKey', 'from'));
    }

    public function getData(Request $request)
    {
        $user = MlAccount::where('id', session('id'))->first();
        $userKey = $request->user_key ?? null;
        $columns = [
            'id',
            'user_id',
            'brach_id',
            'mt_kas_kecil_id',
            'initial_cash',
            'cash_sale',
            'transfer_sales',
            'payment_gateway_sales',
            'piutang_sales',
            'outlet_output',
            'total_cash',
            'total_sales',
            'created_at',
        ];

        $date = $request->date ?? date('Y-m-d');

        $data = MtRekapitulasiHarian::orderBy('id', 'desc')
                    ->select($columns)
                    ->where('user_id', $user->branch_id)
                    ->whereDate('created_at', $date)
                    ->get();

        foreach ($data as $key => $value) {
            $value['nama_toko'] = $value->user->business_group->branch_name ?? null;
        }

        return response()->json($data);
    }
}
