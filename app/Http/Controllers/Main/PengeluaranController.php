<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengeluaranRequest;
use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlAccount;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCurrentAsset;
use App\Models\MlNonBusinessExpense;
use App\Models\MlSellingCost;
use App\Models\MtPengeluaranOutlet;
use App\Models\MtRekapitulasiHarian;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $view = 'pengeluaran';

        return view('main.rekapitulasi_harian.pengeluaran.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                $checkbox =
                    '<div class="custom-control custom-checkbox">
                    <input class="custom-control-input checkbox" id="checkbox' .
                    $data->id .
                    '" type="checkbox" value="' .
                    $data->id .
                    '" />
                    <label class="custom-control-label" for="checkbox' .
                    $data->id .
                    '"></label>
                </div>';

                return $checkbox;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d-M-Y');
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('amount', function ($data) {
                return 'Rp. ' . number_format($data->amount, 0, ',', '.');
            })
            ->addColumn('name_cashier', function ($data) {
                return $data->user->username ?? null;
            })
            ->addColumn('branch', function ($data) {
                return $data->user->branch->name ?? '-';
            })
            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })
            ->addColumn('action', function ($data) {
                // $btn = '<div class="d-flex">';
                // $btn .= '<a href="javascript:void(0)" class="edit btn btn-warning btn-sm m-1" onclick="editData(' . $data->id . ')">Ubah</a>';
                // $btn .= '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm m-1" onclick="deleteData(event, ' . $data->id . ')">Hapus</a>';
                // $btn .= '</div>';
                // return $btn;

                if ($data->sync_status == 1) {
                    return '<div class="d-flex">
                            <a onclick="unsync('.$data->id.')" title="Unsync Jurnal" style="margin-right:3px;" href="javascript:void(0);" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a>
                            <a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                } else {
                    return '<div class="d-flex">
                    <a onclick="syncData(' . $data->id . ')" title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a>
                    <a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                }
            })

            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = ['id', 'user_id', 'name', 'amount', 'sync_status', 'created_at'];
        // $userId = $this->get_owner_id(session('id'));\
        $userId = session('id');
        $checkUser = MlAccount::find($userId);
        $user_id = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');

        $keyword = $request->keyword;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = MtPengeluaranOutlet::orderBy('id', 'desc');
        $data->select($columns)
        ->whereIn('user_id', $user_id)

            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('created_at', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            });

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = 'pengeluaran-create';
        $userId = session('id');
        $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();

        if (isset($rekapitulasiHarian) && $rekapitulasiHarian->kasKecil->close_cashier_at != null) {
            return response()->json([
                'status' => false,
                'message' => 'Kasir sudah ditutup!, silahkan membuka kasir terlebih dahulu!',
            ]);
        } elseif ($rekapitulasiHarian == null) {
            return response()->json([
                'status' => false,
                'message' => 'Silahkan membuka kasir terlebih dahulu!',
            ]);
        }

        return view('main.rekapitulasi_harian.pengeluaran.create', compact('view'));
    }

    public function show($id)
    {
        $data = MtPengeluaranOutlet::find($id);
        $view = 'pengeluaran-show';

        return view('main.rekapitulasi_harian.pengeluaran.detail', compact('data', 'view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PengeluaranRequest $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $request) {
                $user = Account::where('id', session('id'))->first();
                // $data['user_id'] = $this->get_owner_id(session('id'));
                $data['user_id'] = session('id');
                $data['branch_id'] = $user->branch_id ?? 0;
                $data['amount'] = str_replace('.', '', $data['amount']);

                $create = MtPengeluaranOutlet::create($data);
                $this->live_sync($create->id);

                $this->updateRekapitulasiHarian($request, $create);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $data = MtPengeluaranOutlet::findOrFail($id);
        $view = 'pengeluaran-edit';

        return view('main.rekapitulasi_harian.pengeluaran.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengeluaranRequest $request, string $id)
    {
        $data = $request->all();

        // try {
            return $this->atomic(function () use ($data, $request, $id) {
                $data['amount'] = str_replace('.', '', $data['amount']);
                $update = MtPengeluaranOutlet::findOrFail($id);
                $this->updateRekapitulasiHarian($request, $update, $id);
                $update->update($data);


                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Update!',
                ]);
            });
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Data Gagal di Update!',
        //     ]);
        // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $expense = MtPengeluaranOutlet::findorFail($id);
                if ($expense->sync_status == 1) {
                    $journal = Journal::where('relasi_trx', 'rekap_' . $id)->first();
                    JournalList::where('journal_id', $journal->id)->delete();
                    Journal::findorFail($journal->id)->delete();
                }

                $this->deleteActionUpdateRekapitulasi($expense);

                $delete = MtPengeluaranOutlet::find($id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                foreach ($ids as $key => $id) {
                    $expense = MtPengeluaranOutlet::findorFail($id);
                    if ($expense->sync_status == 1) {
                        $journal = Journal::where('relasi_trx', 'rekap_' . $id)->first();
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }

                    $this->deleteActionUpdateRekapitulasi($expense);

                    $delete = MtPengeluaranOutlet::find($id)->delete();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }

    public function deleteActionUpdateRekapitulasi($data)
    {
        $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', session('id'))->whereDate('created_at', $data->created_at)->orderBy('id', 'desc')->first();

        $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] + $data['amount'];
        $rekapitulasiHarian['outlet_output'] = $rekapitulasiHarian['outlet_output'] - $data['amount'];
        $rekapitulasiHarian->save();
    }

    public function updateRekapitulasiHarian(Request $request, $data, $id = null)
    {
        $userId = session('id');
        $amount = (int)str_replace('.', '', $request->amount);

        $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();
        if ($id != null) {
            if ($amount > $data['amount']) {
                $amount = $amount - $data['amount'];
                $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] + $amount;
                $rekapitulasiHarian['outlet_output'] = $rekapitulasiHarian['outlet_output'] + $amount;
            } else {
                $amount = $data['amount'] - $amount;
                $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] - $amount;
                $rekapitulasiHarian['outlet_output'] = $rekapitulasiHarian['outlet_output'] - $amount;
            }
        }else{
            $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] - $amount;
            $rekapitulasiHarian['outlet_output'] = $rekapitulasiHarian['outlet_output'] + $amount;
        }
        $rekapitulasiHarian->save();

        return true;
    }

    public function sync(Request $request)
    {
        $input = $request->all();

        foreach ($input['ids'] as $id) {
            $expenses = MtPengeluaranOutlet::where('id', $id)->first();
            if ($expenses->sync_status !== 1) {
                $untuk = $this->get_first_account_code(9);
                $accode_code_id = 9;
                $keterangan = $expenses->name;
                $rf = $this->get_first_account_code(1) . '_' . 1;
                $st = $untuk . '_' . $accode_code_id;
                $nominal = $expenses->amount;
                $cost_time = date('Y-m-d', strtotime($expenses->created_at));
                $waktu = strtotime($cost_time);
                $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
                $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function single_sync(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $expenses = MtPengeluaranOutlet::where('id', $id)->first();
        if ($expenses->sync_status !== 1) {
            $untuk = $this->get_first_account_code(9);
            $accode_code_id = 9;
            $keterangan = $expenses->name;
            $rf = $this->get_first_account_code(1) . '_' . 1;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $expenses->amount;
            $cost_time = date('Y-m-d', strtotime($expenses->created_at));
            $waktu = strtotime($cost_time);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }



    public function live_sync($id)
    {
        
        $expenses = MtPengeluaranOutlet::where('id', $id)->first();
        if ($expenses->sync_status !== 1) {
            $untuk = $this->get_first_account_code(9);
            $accode_code_id = 9;
            $keterangan = $expenses->name;
            $rf = $this->get_first_account_code(1) . '_' . 1;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $expenses->amount;
            $cost_time = date('Y-m-d', strtotime($expenses->created_at));
            $waktu = strtotime($cost_time);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu);
        }

    }



    protected function get_first_account_code($id)
    {
        if ($id == 1) {
            $data = MlCurrentAsset::where('userid', $this->get_owner_id(session('id') ?? Auth::user()->id))->where('code', 'kas')->first();
        } elseif ($id == 9) {
            $data = MlSellingCost::where('userid', $this->get_owner_id(session('id') ?? Auth::user()->id))->where('code', 'biaya-penjualan-lain-lain')->first();
        }

        return $data->id;
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

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            // 'userid' => $this->user_id_manage(session('id')) ?? $this->get_owner_id(Auth::user()->id),
            'userid' => session('id') == null ? $this->get_owner_id(Auth::user()->id) : $this->user_id_manage(session('id')),
            'journal_id' => 0,
            'transaction_id' => 2,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal,
            'total_balance' => $nominal,
            'color_date' => $this->set_color(2),
            'created' => $waktu,
            'relasi_trx' => 'rekap_' . $id,
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

        $me = MtPengeluaranOutlet::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }


    public function single_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'rekap_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            MtPengeluaranOutlet::where('id', $input['id'])->update([
                'sync_status' => 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Unsync journal success!',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unsync journal failed!',
            ]);
        }
    }
}
