<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MdExpense;
use App\Models\MdExpenseCategory;
use App\Models\MlAccount;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCurrentAsset;
use App\Models\MlNonBusinessExpense;
use App\Models\MlSellingCost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;

class ExpenseController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $view = 'expense';
        $data['count_cat_expense'] = MdExpenseCategory::where('user_id', userOwnerId())->count();

        return view('main.manage-expense.expense.index', compact('view', 'data'));
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
            ->addColumn('category', function ($data) {
                return $data->md_expense_category->name ?? null;
            })
            ->addColumn('date', function ($data) {
                return Carbon::parse($data->date)->format('d F Y');
            })
            ->addColumn('dari', function ($data) {
                return $data->from->name ?? null;
            })
            ->addColumn('untuk', function ($data) {
                return $data->to($data->untuk)->name ?? null;
            })
            ->addColumn('amount', function ($data) {
                return 'Rp. ' . number_format($data->amount);
            })
            ->addColumn('keterangan', function ($data) {
                return $data->keterangan;
            })
            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })

            ->addColumn('action', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div class="d-flex"><a onclick="unsync(' . $data->id . ')" title="Unsync Jurnal" style="margin-right:3px;" href="javascript:void(0);" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                } else {
                    return '<div class="d-flex"><a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = ['id', 'expense_category_id', 'date', 'dari', 'untuk', 'amount', 'keterangan', 'sync_status', 'user_id'];
        $keyword = $request->keyword;
        $category = $request->category;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = MdExpense::orderBy('id', 'desc')
            ->where('user_id', userOwnerId())
            ->select($columns)
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('date', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('date', $tahun);
            })
            ->whereHas('md_expense_category', function ($q) use ($category) {
                if ($category != '') {
                    $q->where('id', $category);
                }
            })
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
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
        $view = 'expense-create';

        return view('main.manage-expense.expense.create', compact('view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $user_id = userOwnerId();
                $data['amount'] = str_replace('.', '', $data['amount']);
                $data['user_id'] = $user_id;
                $untuk = explode('_', $data['untuk']);
                $data['untuk'] = $untuk[0];
                $data['account_code_id'] = $untuk[1];

                $mdExpenseCategory = MdExpense::create($data);
                $this->live_sync($mdExpenseCategory->id);

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $data = MdExpense::findOrFail($id);
        $view = 'category-expense-edit';

        return view('main.manage-expense.expense.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $user_id = userOwnerId();

                $mdExpenseCategory = MdExpense::find($id)->update($data);

                return redirect()->back()->with('success', 'Data Berhasil di Ubah!');
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Gagal di Ubah!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
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
                    $expense = MdExpense::findorFail($id);
                    if ($expense->sync_status == 1) {
                        $journal = Journal::where('relasi_trx', 'biaya_' . $id)->first();
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                    $delete = MdExpense::find($id)->delete();
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

    public function from(Request $request)
    {
        $columns = ['id', 'name', 'userid'];
        $keyword = $request->keyword;

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

    public function to(Request $request)
    {
        $columns = ['id', 'name', 'userid', 'account_code_id'];
        $keyword = $request->keyword;

        $ml_admin_general_fee = MlAdminGeneralFee::orderBy('name', 'desc')
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

        $ml_non_business_expense = MlNonBusinessExpense::orderBy('name', 'desc')
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

        $ml_selling_cost = MlSellingCost::orderBy('name', 'desc')
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

        $data = $ml_admin_general_fee->merge($ml_non_business_expense)->merge($ml_selling_cost);

        return response()->json($data);
    }

    public function sync(Request $request)
    {
        $input = $request->all();

        foreach ($input['ids'] as $id) {
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
                $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }


    public function live_sync($id)
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
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu);
        }
    }


    public function single_sync(Request $request)
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
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu);
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
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        } elseif ($accode_code_id == 10) {
            $account = MlAdminGeneralFee::findorFail($untuk);
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        } elseif ($accode_code_id == 12) {
            $account = MlNonBusinessExpense::findorFail($untuk);
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        }

        return $transaction_name;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => userOwnerId(),
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


    public function single_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'biaya_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            MdExpense::where('id', $input['id'])->update([
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
