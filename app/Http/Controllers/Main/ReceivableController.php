<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceivablePaymentHistoryRequest;
use App\Http\Requests\ReceivableRequest;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCapital;
use App\Models\MlCurrentAsset;
use App\Models\MlFixedAsset;
use App\Models\MlIncome;
use App\Models\MlLongtermDebt;
use App\Models\MlNonBussinessIncome;
use App\Models\MlSellingCost;
use App\Models\MlShorttermDebt;
use App\Models\Receivable;
use App\Models\ReceivablePaymentHistory;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReceivableController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $view = 'pitang_piutang';

        return view('main.utang_piutang.piutang.index', compact('view'));
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
            ->addColumn('receivable_from', function ($data) {
                return $data->receivable_from($data->receivable_from)->name ?? null;
            })
            ->addColumn('save_to', function ($data) {
                return $data->ml_current_asset->name ?? null;
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('type', function ($data) {
                return $data->type;
            })
            ->addColumn('sub_type', function ($data) {
                return $data->sub_type;
            })
            ->addColumn('amount', function ($data) {
                return 'Rp. ' . number_format($data->amount, 0, ',', '.');
            })
            ->addColumn('balance', function ($data) {
                return 'Rp. ' . number_format($data->balance(), 0, ',', '.');
            })
            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })
            ->addColumn('date', function ($data) {
                return Carbon::parse($data->date)->format('d F Y');
            })
            ->addColumn('note', function ($data) {
                return $data->note;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if ($data->sync_status == 1) {
                    $btn .= '<a onclick="unsync(' . $data->id . ')" title="Unsync Jurnal" style="margin-right:2px;" href="javascript:void(0);" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a>';
                } else {
                    $btn .= '<a title="Sync Jurnal" style="margin-right:2px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a>';
                }

                $btn .= '<a title="Hapus" style="margin-right:2px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a>';
                if ($data->balance() > 0) {
                    $btn .= '<a title="Bayar" style="margin-right:2px;" href="javascript:void(0);" onclick="payment(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-dollar"></i></a>';
                } else {
                    $btn .= '<a title="Lunas" style="margin-right:2px;" href="javascript:void(0);" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-check"></i></a>';
                }
                $btn .= '<a title="History" style="margin-right:2px;" href="javascript:void(0);" onclick="detail(' . $data->id . ')" class="avatar-text avatar-md bg-primary text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-history"></i></a>';
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = ['id', 'receivable_from', 'save_to', 'name', 'type', 'sub_type', 'amount', 'note', 'user_id', 'sync_status', 'date'];

        $keyword = $request->keyword;
        $type = $request->type;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = Receivable::orderBy('updated_at', 'desc')
            ->where('user_id', userOwnerId())
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
                $q->whereMonth('created_at', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            })
            ->get();

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = 'pitang-create';

        return view('main.utang_piutang.piutang.create', compact('view'));
    }

    public function show($id)
    {
        $data = Receivable::find($id);
        $view = 'pitang-show';

        return view('main.utang_piutang.piutang.detail', compact('data', 'view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReceivableRequest $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $request) {
                $data['user_id'] = userOwnerId();
                $data['amount'] = str_replace('.', '', $data['amount']);

                $rf = explode('_', $data['receivable_from']);
                $data['receivable_from'] = $rf[0];
                $data['account_code_id'] = $rf[1];

                $create = Receivable::create($data);

                $this->single_sync_id($create->id);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Tambahkan!',
            ]);
        }
    }

    public function todoPayment($id)
    {
        $view = 'bayar-hutang';
        $data = Receivable::find($id);
        $ml_current_asset = MlCurrentAsset::where('userid', userOwnerId())->get();
        $ml_fixed_asset = MlFixedAsset::where('userid', userOwnerId())->get();
        $ml_selling_cost = MlSellingCost::where('userid', userOwnerId())->get();
        $ml_general = MlAdminGeneralFee::where('userid', userOwnerId())->get();
        $ml_income = MlIncome::where('userid', userOwnerId())->get();
        $ml_non_bussiness_income = MlNonBussinessIncome::where('userid', userOwnerId())->get();

        $dt = array_merge($ml_current_asset->toArray(),$ml_fixed_asset->toArray(), $ml_selling_cost->toArray(), $ml_general->toArray(), $ml_income->toArray(), $ml_non_bussiness_income->toArray());

        return view('main.utang_piutang.piutang.payment', compact('data', 'view', 'dt'));
    }

    public function payment(ReceivablePaymentHistoryRequest $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $payment_to_ids = explode("_", $data['payment_to_id']);
                  
                $receivable = Receivable::find($id);
                $data['user_id'] = userOwnerId();
                $data['amount'] = str_replace('.', '', $data['amount']);
                $data['balance'] = $receivable->balance() - $data['amount'];
                $data['receivable_id'] = $receivable->id;
                $data['payment_to_id'] = $payment_to_ids[0];
                $data['account_code_id'] = $payment_to_ids[1];
                $data['payment_from_id'] = $receivable->save_to;

                $create = ReceivablePaymentHistory::create($data);
                $this->live_sync_payment($create->id);

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
        $data = Receivable::findOrFail($id);
        $view = 'pitang-edit';

        return view('main.utang_piutang.piutang.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReceivableRequest $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = Receivable::findOrFail($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Update!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Update!',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
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

    public function type(Request $request)
    {
        $keyword = $request->keyword;

        $data = [['id' => 'Piutang Jangka Panjang', 'name' => 'Piutang Jangka Panjang'], ['id' => 'Piutang Jangka Pendek', 'name' => 'Piutang Jangka Pendek']];

        if ($keyword != '') {
            $data = collect($data)
                ->filter(function ($item) use ($keyword) {
                    return Str::contains(strtolower($item['name']), strtolower($keyword));
                })
                ->values()
                ->all();
        }

        return response()->json($data);
    }

    public function subType(Request $request)
    {
        $keyword = $request->keyword;
        $type = $request->type;

        $sort = [['id' => 'Piutang Usaha (Accounts Receivable)', 'name' => 'Piutang Usaha (Accounts Receivable)'], ['id' => 'Piutang Non-usaha (Non-trade Receivables)', 'name' => 'Piutang Non-usaha (Non-trade Receivables)'], ['id' => 'Piutang - Karyawan (Employee Receivables)', 'name' => 'Piutang - Karyawan (Employee Receivables)'], ['id' => 'Piutang Bunga (Interest Receivables)', 'name' => 'Piutang Bunga (Interest Receivables)'], ['id' => 'Piutang Pajak (Tax Receivables)', 'name' => 'Piutang Pajak (Tax Receivables)']];

        $long = [['id' => 'Piutang Wesel Jangka Panjang (Long-term Notes Receivable)', 'name' => 'Piutang Wesel Jangka Panjang (Long-term Notes Receivable)'], ['id' => 'Piutang Sewa Jangka Panjang (Long-term Rent Receivables)', 'name' => 'Piutang Sewa Jangka Panjang (Long-term Rent Receivables)'], ['id' => 'Piutang Lain-lain Jangka Panjang (Other Long-term Receivables)', 'name' => 'Piutang Lain-lain Jangka Panjang (Other Long-term Receivables)']];

        if ($type == 'Piutang Jangka Panjang') {
            $data = $long;
        } elseif ($type == 'Piutang Jangka Pendek') {
            $data = $sort;
        }

        if ($keyword != '') {
            $data = collect($data)
                ->filter(function ($item) use ($keyword) {
                    return Str::contains(strtolower($item['name']), strtolower($keyword));
                })
                ->values()
                ->all();
        }

        return response()->json($data);
    }

    public function from(Request $request)
    {
        $columns = ['id', 'name', 'userid', 'account_code_id'];
        $keyword = $request->keyword;
        $data = [];

        $MlCurrentAsset = MlCurrentAsset::orderBy('id', 'asc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        $MlIncome = MlIncome::orderBy('id', 'asc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        $MlNonBussinessIncome = MlNonBussinessIncome::orderBy('id', 'asc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        $MlCapital = MlCapital::orderBy('id', 'asc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        $data = array_merge($MlCurrentAsset->toArray(), $MlIncome->toArray(), $MlNonBussinessIncome->toArray(), $MlCapital->toArray());

        return response()->json($data);
    }

    public function saveTo(Request $request)
    {
        $columns = ['id', 'name', 'userid'];

        $keyword = 'piutang ' . $request->keyword;

        $data = MlCurrentAsset::orderBy('id', 'asc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return response()->json($data);
    }

    public function sync(Request $request)
    {
        $input = $request->all();

        foreach ($input['ids'] as $id) {
            $dt = Receivable::where('id', $id)->first();
            if ($dt->sync_status !== 1) {
                $untuk = $dt->save_to;
                $accode_code_id = 1;
                $keterangan = $dt->sub_type;
                $ac_id = 1;

                $rf = $dt->receivable_from . '_' . $dt->account_code_id;
                $st = $untuk . '_' . $accode_code_id;
                $nominal = $dt->amount;
                $tanggal = date('Y-m-d', strtotime($dt->created_at));
                $waktu = strtotime($dt->date);
                $transaction_name = $this->get_transaction_name($dt->save_to, $ac_id, $keterangan);
                $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 3, 'piutang');
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
        $dt = Receivable::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $dt->save_to;
            $accode_code_id = 1;
            $keterangan = $dt->sub_type;
            $ac_id = 1;

            $rf = $dt->receivable_from . '_' . $dt->account_code_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->amount;
            $tanggal = date('Y-m-d', strtotime($dt->created_at));
            $waktu = strtotime($dt->date);
            $transaction_name = $this->get_transaction_name($dt->save_to, $ac_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 5, 'piutang');
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function single_sync_id($id)
    {
        $dt = Receivable::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $dt->save_to;
            $accode_code_id = 1;
            $keterangan = $dt->sub_type;
            $ac_id = 1;

            $rf = $dt->receivable_from . '_' . $dt->account_code_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->amount;
            $tanggal = date('Y-m-d', strtotime($dt->created_at));
            $waktu = strtotime($dt->date);
            $transaction_name = $this->get_transaction_name($dt->save_to, $ac_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 5, 'piutang');
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
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

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $transaction_id, $relasi)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => userOwnerId(),
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

    public function sync_payment(Request $request)
    {
        $input = $request->all();
        $payment = ReceivablePaymentHistory::findorFail($input['ids']);
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
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $input['ids'], $waktu, 6, 'pembayaran');
        }

        return response()->json([
            'success' => true,
        ]);
    }

    protected function live_sync_payment($id)
    {
        $payment = ReceivablePaymentHistory::findorFail($id);
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
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 6, 'pembayaran');
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function single_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'piutang_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            Receivable::where('id', $input['id'])->update([
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

    public function unsync_payment(Request $request)
    {
        $input = $request->all();
        $code = 'pembayaran_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
       
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            ReceivablePaymentHistory::where('id', $input['id'])->update([
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


    public function delete_payment(Request $request) {
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
