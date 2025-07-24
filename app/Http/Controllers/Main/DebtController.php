<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebtPaymentHistoryRequest;
use App\Http\Requests\DebtRequest;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\Material;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\MlFixedAsset;
use App\Models\MlLongtermDebt;
use App\Models\MlNonBussinessIncome;
use App\Models\MlSellingCost;
use App\Models\MlShorttermDebt;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DebtController extends Controller
{
    use CommonTrait;
    public function index()
    {
        $view = 'utang_piutang';

        return view('main.utang_piutang.utang.index', compact('view'));
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
            ->addColumn('debt_from', function ($data) {
                if ($data->type == 'Utang Jangka Panjang') {
                    return $data->ml_longterm_debt->name ?? null;
                } elseif ($data->type == 'Utang Jangka Pendek') {
                    return $data->ml_shortterm_debt->name ?? null;
                }
            })
            ->addColumn('save_to', function ($data) {
                $relasi = explode('_', $data->relasi_trx);

                if ($relasi[0] == 'konversi') {
                    return $data->ml_selling_cost->name ?? null;
                } elseif ($relasi[0] == 'penyusutan') {
                    return $data->ml_fixed_asset->name ?? null;
                } else {
                    if ($data->account_code_id == 1) {
                        return $data->ml_current_asset->name ?? null;
                    } elseif ($data->account_code_id == 9) {
                        return $data->ml_selling_cost->name ?? null;
                    } elseif ($data->account_code_id == 10) {
                        return $data->ml_general_fee->name ?? null;
                    } elseif ($data->account_code_id == 2) {
                        return $data->ml_fixed_asset->name ?? null;
                    } else {
                        return $data->ml_current_asset->name ?? null;
                    }
                }
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
        $columns = ['id', 'debt_from', 'save_to', 'name', 'type', 'sub_type', 'amount', 'note', 'user_id', 'sync_status', 'date', 'relasi_trx', 'account_code_id'];

        $keyword = $request->keyword;
        $type = $request->type;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = Debt::orderBy('id', 'desc')
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
        $view = 'utang-create';

        return view('main.utang_piutang.utang.create', compact('view'));
    }

    public function show($id)
    {
        $data = Debt::find($id);
        $view = 'utang-show';

        return view('main.utang_piutang.utang.detail', compact('data', 'view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DebtRequest $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                $data['user_id'] = userOwnerId();
                $data['amount'] = str_replace('.', '', $data['amount']);
                $data['sync_status'] = 0;

                $saveto = explode('_', $data['save_to']);
                $data['save_to'] = (int)$saveto[0];
                $data['account_code_id'] = (int)$saveto[1];

                $create = Debt::create($data);
                $this->live_sync($create->id);
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
        $data = Debt::find($id);
        $ml_current_asset = MlCurrentAsset::where('userid', userOwnerId())->get();
        $ml_fixed_asset = MlFixedAsset::where('userid', userOwnerId())->get();
        $ml_cogs = MlCostGoodSold::where('userid', userOwnerId())->get();
        $ml_selling_cost = MlSellingCost::where('userid', userOwnerId())->get();
        $ml_admin_general_fee = MlAdminGeneralFee::where('userid', userOwnerId())->get();
        $ml_non_business_income = MlNonBussinessIncome::where('userid', userOwnerId())->get();

        $data_akun = array_merge($ml_current_asset->toArray(), $ml_fixed_asset->toArray(), $ml_cogs->toArray(),$ml_selling_cost->toArray(), $ml_admin_general_fee->toArray(), $ml_non_business_income->toArray());

        return view('main.utang_piutang.utang.payment', compact('data', 'view', 'data_akun'));
    }

    public function payment(DebtPaymentHistoryRequest $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $from = explode('_', $data['payment_from_id']);
                $debt = Debt::find($id);
                $data['user_id'] = userOwnerId();
                $data['amount'] = str_replace('.', '', $data['amount']);
                $data['balance'] = $debt->balance() - $data['amount'];
                $data['debt_id'] = $debt->id;
                $data['payment_to_id'] = $debt->debt_from;
                $data['payment_from_id'] = $from[0];
                $data['account_code_id'] = $from[1];

                $create = DebtPaymentHistory::create($data);
                $this->live_sync_payment($create->id);

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
        $data = Debt::findOrFail($id);
        $view = 'utang-edit';

        return view('main.utang_piutang.utang.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DebtRequest $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = Debt::findOrFail($id)->update($data);

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
                $dt = Debt::findorFail($id);
                $relasi = $dt->relasi_trx;
                if (!empty($relasi)) {
                    $rel = explode('_', $relasi);
                    if ($rel[0] == 'konversi') {
                        return response()->json([
                            'status' => false,
                            'message' => 'Data Gagal Dihapus!, Silahkan Hapus Dari Menu Konversi Stock..!',
                        ]);
                    }
                    if ($rel[0] == 'penyusutan') {
                        return response()->json([
                            'status' => false,
                            'message' => 'Data Gagal Dihapus!, Silahkan Hapus Dari Menu Penyusutan..!',
                        ]);
                    }
                }

                if ($dt->sync_status == 1) {
                    $journals = Journal::where('relasi_trx', 'utang_' . $id)->get();
                    foreach ($journals as $journal) {
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                }

                if (!empty($relasi)) {
                    $rel = explode('_', $relasi);
                    if ($rel[0] == 'product-purchase') {
                        $items = ProductPurchaseItem::where('purchase_id', $rel[1])->get();
                        foreach ($items as $item) {
                            $product = Product::findorFail($item->product_id);
                            $current_stock = $product->quantity;
                            $current_cost = $product->cost;

                            Product::where('id', $item->product_id)->update([
                                'quantity' => $current_stock - $item->quantity,
                                'cost' => $current_stock - $item->quantity == 0 ? $product->default_cost : $this->reverse_cogs($current_cost, $current_stock, $item->cost, $item->quantity),
                            ]);
                        }

                        ProductPurchase::destroy($rel[1]);
                        ProductPurchaseItem::where('purchase_id', $rel[1])->delete();
                    } elseif ($rel[0] == 'material-purchase') {
                        $items = MaterialPurchaseItem::where('purchase_id', $rel[1])->get();
                        foreach ($items as $item) {
                            $product = Material::findorFail($item->product_id);
                            $current_stock = $product->stock;
                            $current_cost = $product->cost;

                            Material::where('id', $item->product_id)->update([
                                'stock' => $current_stock - $item->quantity,
                                'cost' => $current_stock - $item->quantity == 0 ? $product->default_cost : $this->reverse_cogs($current_cost, $current_stock, $item->cost, $item->quantity),
                            ]);
                        }

                        MaterialPurchase::destroy($rel[1]);
                        MaterialPurchaseItem::where('purchase_id', $rel[1])->delete();
                    }
                }

                $delete = Debt::find($id)->delete();

                $dph = DebtPaymentHistory::where('debt_id', $id)->get();
                foreach ($dph as $dh) {
                    if ($dh->sync_status == 1) {
                        $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                        foreach ($journals as $journal) {
                            JournalList::where('journal_id', $journal->id)->delete();
                            Journal::findorFail($journal->id)->delete();
                        }
                    }
                }

                $history = DebtPaymentHistory::where('debt_id', $id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }

    protected function reverse_cogs($cogs, $quantity, $purchase_cogs, $purchase_quantity)
    {
        $old_cost = ($cogs * $quantity - $purchase_cogs * $purchase_quantity) / ($quantity - $purchase_quantity);
        return $old_cost;
    }

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                foreach ($ids as $key => $id) {
                    $dt = Debt::findorFail($id);
                    if (empty($dt->relasi_trx)) {
                        $relasi = $dt->relasi_trx;

                        $rel = explode('_', $relasi);
                        if ($rel[0] == 'konversi') {
                            return response()->json([
                                'status' => false,
                                'message' => 'Data Gagal Dihapus!, Silahkan Hapus Dari Menu Konversi Stock..!',
                            ]);
                        } elseif ($rel[0] == 'penyusutan') {
                            return response()->json([
                                'status' => false,
                                'message' => 'Data Gagal Dihapus!, Silahkan Hapus Dari Menu Penyusutan..!',
                            ]);
                        }

                        if ($dt->sync_status == 1) {
                            $journals = Journal::where('relasi_trx', 'utang_' . $id)->get();
                            foreach ($journals as $journal) {
                                JournalList::where('journal_id', $journal->id)->delete();
                                Journal::findorFail($journal->id)->delete();
                            }
                        }
                        $delete = Debt::find($id)->delete();

                        $dph = DebtPaymentHistory::where('debt_id', $id)->get();
                        foreach ($dph as $dh) {
                            if ($dh->sync_status == 1) {
                                $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                                foreach ($journals as $journal) {
                                    JournalList::where('journal_id', $journal->id)->delete();
                                    Journal::findorFail($journal->id)->delete();
                                }
                            }
                        }

                        $history = DebtPaymentHistory::where('debt_id', $id)->delete();
                    }
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

        $data = [['id' => 'Utang Jangka Panjang', 'name' => 'Utang Jangka Panjang'], ['id' => 'Utang Jangka Pendek', 'name' => 'Utang Jangka Pendek']];

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

        $sort = [['id' => 'Utang Usaha (Accounts Payable)', 'name' => 'Utang Usaha (Accounts Payable)'], ['id' => 'Utang Wesel Jangka Pendek (Short-term Notes Payable)', 'name' => 'Utang Wesel Jangka Pendek (Short-term Notes Payable)'], ['id' => 'Utang Gaji (Salaries Payable)', 'name' => 'Utang Gaji (Salaries Payable)'], ['id' => 'Utang Pajak (Taxes Payable)', 'name' => 'Utang Pajak (Taxes Payable)'], ['id' => 'Utang Bunga (Interest Payable)', 'name' => 'Utang Bunga (Interest Payable)'], ['id' => 'Utang Sewa (Rent Payable)', 'name' => 'Utang Sewa (Rent Payable)'], ['id' => 'Utang Dividen (Dividends Payable)', 'name' => 'Utang Dividen (Dividends Payable)']];

        $long = [['id' => 'Utang Wesel Jangka Panjang (Long-term Notes Payable)', 'name' => 'Utang Wesel Jangka Panjang (Long-term Notes Payable)'], ['id' => 'Utang Obligasi (Bonds Payable)', 'name' => 'Utang Obligasi (Bonds Payable)'], ['id' => 'Utang Bank Jangka Panjang (Long-term Bank Loans)', 'name' => 'Utang Bank Jangka Panjang (Long-term Bank Loans)'], ['id' => 'Dan Hipotek (Mortgage Payable)', 'name' => 'Dan Hipotek (Mortgage Payable)']];

        $data = [];
        if ($type == 'Utang Jangka Panjang') {
            $data = $long;
        } elseif ($type == 'Utang Jangka Pendek') {
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

    public function debtFrom(Request $request)
    {
        $columns = ['id', 'name', 'userid'];
        $type = $request->type;
        $keyword = $request->keyword;
        $data = [];

        if ($type == 'Utang Jangka Pendek') {
            $data = MlShorttermDebt::orderBy('id', 'asc')
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
        } elseif ($type == 'Utang Jangka Panjang') {
            $data = MlLongtermDebt::orderBy('id', 'asc')
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
        }

        return response()->json($data);
    }

    public function saveTo(Request $request)
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

        $MlFixedAsset = MlFixedAsset::orderBy('id', 'asc')
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

        $MlSellingCost = MlSellingCost::orderBy('id', 'asc')
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

        $MlGeneralFee = MlAdminGeneralFee::orderBy('id', 'asc')
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

        $data = array_merge($MlCurrentAsset->toArray(), $MlFixedAsset->toArray(), $MlSellingCost->toArray(), $MlGeneralFee->toArray());

        return response()->json($data);
    }

    public function sync(Request $request)
    {
        // $input = $request->all();

        // foreach ($input['ids'] as $id) {
        //     $dt = Debt::where('id', $id)->first();
        //     if ($dt->sync_status !== 1) {
        //         $untuk = $dt->save_to;
        //         $accode_code_id = 1;
        //         $keterangan = $dt->sub_type;
        //         $ac_id = $dt->type == 'Utang Jangka Pendek' ? 4 : 5;

        //         $rf = $dt->debt_from . '_' . $ac_id;
        //         $st = $untuk . '_' . $accode_code_id;
        //         $nominal = $dt->amount;
        //         $tanggal = date('Y-m-d', strtotime($dt->created_at));
        //         $waktu = strtotime($dt->date);
        //         $transaction_name = $this->get_transaction_name($dt->debt_from, $ac_id, $keterangan);
        //         $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 3, 'utang');
        //     }
        // }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'success',
        // ]);
    }

    public function single_sync(Request $request)
    {
        $input = $request->all();

        $id = $input['id'];
        $dt = Debt::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $relasi = explode('_', $dt->relasi_trx);

            $untuk = $dt->save_to;

            if ($relasi[0] == 'konversi') {
                $accode_code_id = 9;
            } elseif ($relasi[0] == 'penyusutan') {
                $accode_code_id = 2;
            } else {
                $accode_code_id = $dt->account_code_id;
            }
            $keterangan = $dt->name;
            $ac_id = $dt->type == 'Utang Jangka Pendek' ? 4 : 5;

            $rf = $dt->debt_from . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->amount;
            $tanggal = date('Y-m-d', strtotime($dt->created_at));
            $waktu = strtotime($dt->date);
            $transaction_name = $this->get_transaction_name($dt->debt_from, $ac_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 3, 'utang');
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function live_sync($id)
    {
        $dt = Debt::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $relasi = explode('_', $dt->relasi_trx);

            $untuk = $dt->save_to;
            if ($relasi[0] == 'konversi') {
                $accode_code_id = 9;
            } elseif ($relasi[0] == 'penyusutan') {
                $accode_code_id = 2;
            } else {
                $accode_code_id = $dt->account_code_id;
            }

            $keterangan = $dt->name;
            $ac_id = $dt->type == 'Utang Jangka Pendek' ? 4 : 5;

            $rf = $dt->debt_from . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->amount;
            $tanggal = date('Y-m-d', strtotime($dt->created_at));
            $waktu = strtotime($dt->date);
            $transaction_name = $this->get_transaction_name($dt->debt_from, $ac_id, $keterangan);
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 3, 'utang');
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';
        if ($accode_code_id == 4) {
            $account = MlShorttermDebt::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 5) {
            $account = MlLongtermDebt::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
            $transaction_name = $account->name;
        }

        return $transaction_name . ' -  ' . $keterangan;
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
        if ($relasi == 'utang') {
            $me = Debt::findorFail($id);
        } elseif ($relasi == 'payment') {
            $me = DebtPaymentHistory::findorFail($id);
        }

        $me->sync_status = 1;
        $me->save();
    }

    public function sync_payment(Request $request)
    {
        $input = $request->all();
        $payment = DebtPaymentHistory::findorFail($input['ids']);
        $utang = Debt::findorFail($payment->debt_id);

        if ($payment->sync_status !== 1) {
            $untuk = $payment->payment_to_id;
            $accode_code_id = $utang->type == 'Utang Jangka Pendek' ? 4 : 5;
            $keterangan = $payment->note;
            $ac_id = $payment->account_code_id;

            $rf = $payment->payment_from_id . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $payment->amount;
            $tanggal = date('Y-m-d', strtotime($payment->created_at));
            $waktu = strtotime($tanggal);
            $transaction_name = 'Pembayaran ' . $utang->type . ' ' . $keterangan;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $input['ids'], $waktu, 4, 'payment');
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function live_sync_payment($id)
    {
        $payment = DebtPaymentHistory::findorFail($id);
        $utang = Debt::findorFail($payment->debt_id);

        if ($payment->sync_status !== 1) {
            $untuk = $payment->payment_to_id;
            $accode_code_id = $utang->type == 'Utang Jangka Pendek' ? 4 : 5;
            $keterangan = $payment->note;
            $ac_id = $payment->account_code_id;

            $rf = $payment->payment_from_id . '_' . $ac_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $payment->amount;
            $tanggal = date('Y-m-d', strtotime($payment->created_at));
            $waktu = strtotime($tanggal);
            $transaction_name = 'Pembayaran ' . $utang->type . ' ' . $keterangan;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, 4, 'payment');
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function single_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'utang_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            Debt::where('id', $input['id'])->update([
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
        $code = 'payment_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            DebtPaymentHistory::where('id', $input['id'])->update([
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
        $payment = DebtPaymentHistory::findorFail($input['id']);
        if($payment->sync_status == 1) {
            $jurnal = Journal::where('relasi_trx', 'payment_'. $input['id']);
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
