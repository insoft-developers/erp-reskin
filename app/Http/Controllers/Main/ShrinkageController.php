<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShrinkageRequest;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlAccumulatedDepreciation;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCurrentAsset;
use App\Models\MlFixedAsset;
use App\Models\MlLongtermDebt;
use App\Models\MlShorttermDebt;
use App\Models\Shrinkage;
use App\Models\ShrinkageSimulate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;

class ShrinkageController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $view = 'penyusutan';

        return view('main.penyusutan.lists.index', compact('view'));
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
            ->addColumn('ml_fixed_asset', function ($data) {
                return $data->ml_fixed_asset->name ?? null;
            })
            ->addColumn('ml_accumulated_depreciation', function ($data) {
                return $data->ml_accumulated_depreciation->name ?? null;
            })
            ->addColumn('ml_admin_general_fee', function ($data) {
                return $data->ml_admin_general_fee->name ?? null;
            })
            ->addColumn('name', function ($data) {
                $cek = Shrinkage::where('is_lost', 1)->where('relasi_trx', $data->id);
                if ($cek->count() > 0) {
                    $html = '';

                    $html .= $data->name . '<br>( ' . $data->quantity . ' Unit )';
                    $html .= '<div style="color:red;">';
                    $html .= '<ul>';
                    foreach ($cek->get() as $ld) {
                        $html .= '<li>' . $ld->name . '<br>' . $ld->quantity . ' unit</li>';
                    }
                    $html .= '</ul>';
                    $html .= '</div>';
                    return $html;
                } else {
                    if ($data->quantity == null) {
                        return $data->name;
                    } else {
                        return $data->name . '<br>( ' . $data->quantity . ' Unit )';
                    }
                }
            })
            ->addColumn('initial_value', function ($data) {
                return 'Rp. ' . number_format($data->initial_value, 0, ',', '.');
            })
            ->addColumn('residual_value', function ($data) {
                return 'Rp. ' . number_format($data->residual_value, 0, ',', '.');
            })
            ->addColumn('useful_life', function ($data) {
                return $data->useful_life . ' Bulan';
            })
            ->addColumn('date', function ($data) {
                return Carbon::parse($data->date)->format('d F Y');
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
                    $html = '';
                    $html .= '<div class="d-flex">';
                    $html .= '<a onclick="unsync(' . $data->id . ')" title="Unsync Jurnal" style="margin-right:3px;" href="javascript:void(0);" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a>';

                    $html .= '<a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a>';

                    if ($data->is_lost != 1) {
                        $html .= '<a title="Simulasi" style="margin-right:3px;" href="javascript:void(0);" onclick="detail(' . $data->id . ')" class="avatar-text avatar-md bg-primary text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-list"></i></a>';
                        $html .= '<a title="kurangi asset" style="margin-right:3px;" href="javascript:void(0);" onclick="kurangi_asset(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-fill-drip"></i></a>';
                    }

                    $html .= '</div>';

                    return $html;
                } else {
                    return '<div class="d-flex"><a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="sync(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a><a title="Simulasi" style="margin-right:3px;" href="javascript:void(0);" onclick="detail(' . $data->id . ')" class="avatar-text avatar-md bg-primary text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-list"></i></a></div>';
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = ['id', 'ml_fixed_asset_id', 'ml_accumulated_depreciation_id', 'ml_admin_general_fee_id', 'name', 'initial_value', 'useful_life', 'residual_value', 'note', 'user_id', 'sync_status', 'date', 'quantity', 'is_lost'];

        $keyword = $request->keyword;
        $ml_fixed_asset = $request->ml_fixed_asset;
        $ml_accumulated_depreciation = $request->ml_accumulated_depreciation;
        $ml_admin_general_fee = $request->ml_admin_general_fee;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = Shrinkage::orderBy('id', 'desc')
            ->where('user_id', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->whereHas('ml_fixed_asset', function ($q) use ($ml_fixed_asset) {
                if ($ml_fixed_asset != '') {
                    $q->where('id', $ml_fixed_asset);
                }
            })
            ->whereHas('ml_accumulated_depreciation', function ($q) use ($ml_accumulated_depreciation) {
                if ($ml_accumulated_depreciation != '') {
                    $q->where('id', $ml_accumulated_depreciation);
                }
            })
            ->whereHas('ml_admin_general_fee', function ($q) use ($ml_admin_general_fee) {
                if ($ml_admin_general_fee != '') {
                    $q->where('id', $ml_admin_general_fee);
                }
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('date', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('date', $tahun);
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
        $view = 'penyusutan-create';
        $userid = $this->user_id_manage(session('id'));
        $lancar = MlCurrentAsset::where('userid', $userid)->get();
        $pendek = MlShorttermDebt::where('userid', $userid)->get();
        $panjang = MlLongtermDebt::where('userid', $userid)->get();

        return view('main.penyusutan.lists.create', compact('view', 'lancar', 'pendek', 'panjang'));
    }

    public function show($id)
    {
        $data = Shrinkage::find($id);
        $view = 'penyusutan-show';

        return view('main.penyusutan.lists.simulate', compact('data', 'view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShrinkageRequest $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $data['user_id'] = userOwnerId();
                $data['initial_value'] = str_replace('.', '', $data['initial_value']);
                $data['residual_value'] = str_replace('.', '', $data['residual_value']);
                $data['sync_status'] = 0;
                $data['quantity'] = $data['quantity'] == null ? 1 : $data['quantity'];
                $data['buying_price'] = $data['quantity'] == null ? $data['initial_value'] : $data['initial_value'] / $data['quantity'];
                $data['buying_with_account'] = $data['buying_with_account'];
                $create = Shrinkage::create($data);

                $arr = explode('_', $data['buying_with_account']);

                if ($arr[2] == 2) {
                    $debt = new Debt();
                    $debt->debt_from = $arr[0];
                    $debt->save_to = $data['ml_fixed_asset_id'];
                    $debt->name = 'Pembelian Aset ' . $data['name'];
                    $debt->type = $arr[1] == '4' ? 'Utang Jangka Pendek' : 'Utang Jangka Panjang';
                    $debt->sub_type = $arr[1] == '4' ? 'Utang Usaha (Accounts Payable)' : 'Utang Bank Jangka Panjang (Long-term Bank Loans)';
                    $debt->amount = $data['initial_value'];
                    $debt->note = 'Pembelian Aset ' . $data['name'];
                    $debt->user_id = $this->user_id_manage(session('id'));
                    $debt->sync_status = 0;
                    $debt->relasi_trx = 'penyusutan_' . $create->id;
                    $debt->created_at = date('Y-m-d H:i:s');
                    $debt->updated_at = date('Y-m-d H:i:s');
                    $debt->date = $data['date'];
                    $debt->save();

                    $_controller = new DebtController();
                    $_controller->live_sync($debt->id);
                }

                // PENYUSUTAN AMBIL DARI RUMUS
                // NILAI BUKU AWAL DARI NILAI AWAL PNYESUAIAN YANG DIINPUT
                // NILAI BUKU AKHIR BUKU AWAL-PENYUSUTAN

                // BULAN DIAMBIL DARI INPUT DI BULAN BERIKUTNYA (TANGGAL 1, BULAN BERJALAN + 1, TAHUN)
                $initial_book_value = $data['initial_value'];
                for ($value = 0; $value < $data['useful_life']; $value++) {
                    $month = Carbon::parse($data['date'])
                        ->addMonths($value + 1)
                        ->format('F Y');
                    $date = Carbon::parse($data['date'])
                        ->addMonths($value + 1)
                        ->format('Y-m');
                    $shrinkage = ($data['initial_value'] - $data['residual_value']) / $data['useful_life'];
                    $final_book_value = $initial_book_value - $shrinkage;

                    $dataSimulate = [
                        'shrinkage_id' => $create->id,
                        'month' => "1 $month",
                        'initial_book_value' => $initial_book_value,
                        'shrinkage' => $shrinkage,
                        'final_book_value' => $final_book_value,
                        'user_id' => userOwnerId(),
                        'date' => $date . '-01',
                    ];

                    ShrinkageSimulate::create($dataSimulate);

                    $initial_book_value = $final_book_value;
                }
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $data = Shrinkage::findOrFail($id);
        $view = 'penyusutan-edit';

        return view('main.penyusutan.lists.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShrinkageRequest $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = Shrinkage::findOrFail($id)->update($data);

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
                $dt = Shrinkage::findorFail($id);
                if ($dt->sync_status == 1) {
                    $journals = Journal::where('relasi_trx', 'penyusutan_' . $id)->get();
                    foreach ($journals as $journal) {
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                }

                if ($dt->is_lost == 1) {
                    $s_id = $dt->relasi_trx;
                    $new = Shrinkage::findorFail($s_id);
                    $stok_awal = $dt->quantity;
                    $stok_akhir = $stok_awal + $new->quantity;
                    $new->quantity = $stok_akhir;
                    $new->save();
                } else {
                    $lost_list = Shrinkage::where('relasi_trx', $id);
                    if ($lost_list->count() > 0) {
                        foreach ($lost_list->get() as $ll) {
                            if ($ll->sync_status == 1) {
                                $junal = Journal::where('relasi_trx', 'penyusutan_' . $ll->id)->get();
                                foreach ($junal as $lst) {
                                    JournalList::where('journal_id', $lst->id)->delete();
                                    Journal::findorFail($lst->id)->delete();
                                }
                            }
                        }
                    }
                    $lost_list->delete();
                }

                $delete = Shrinkage::find($id)->delete();

                if ($dt->is_lost == 1) {
                } else {
                    $deleteSimulate = ShrinkageSimulate::where('shrinkage_id', $id)->delete();

                    $utang = Debt::where('relasi_trx', 'penyusutan_' . $id);
                    if ($utang->count() > 0) {
                        $hutang = $utang->first();
                        if ($hutang->sync_status == 1) {
                            $journals = Journal::where('relasi_trx', 'utang_' . $hutang->id)->get();
                            foreach ($journals as $journal) {
                                JournalList::where('journal_id', $journal->id)->delete();
                                Journal::findorFail($journal->id)->delete();
                            }
                        }

                        $delete = Debt::find($hutang->id)->delete();

                        $dph = DebtPaymentHistory::where('debt_id', $hutang->id)->get();
                        foreach ($dph as $dh) {
                            if ($dh->sync_status == 1) {
                                $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                                foreach ($journals as $journal) {
                                    JournalList::where('journal_id', $journal->id)->delete();
                                    Journal::findorFail($journal->id)->delete();
                                }
                            }
                        }

                        $history = DebtPaymentHistory::where('debt_id', $hutang->id)->delete();
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

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                foreach ($ids as $key => $id) {
                    $dt = Shrinkage::findorFail($id);
                    if ($dt->sync_status == 1) {
                        $journals = Journal::where('relasi_trx', 'penyusutan_' . $id)->get();
                        foreach ($journals as $journal) {
                            JournalList::where('journal_id', $journal->id)->delete();
                            Journal::findorFail($journal->id)->delete();
                        }
                    }
                    if ($dt->is_lost == 1) {
                        $s_id = $dt->relasi_trx;
                        $new = Shrinkage::findorFail($s_id);
                        $stok_awal = $dt->quantity;
                        $stok_akhir = $stok_awal + $new->quantity;
                        $new->quantity = $stok_akhir;
                        $new->save();
                    } else {
                        $lost_list = Shrinkage::where('relasi_trx', $id);
                        if ($lost_list->count() > 0) {
                            foreach ($lost_list->get() as $ll) {
                                if ($ll->sync_status == 1) {
                                    $junal = Journal::where('relasi_trx', 'penyusutan_' . $ll->id)->get();
                                    foreach ($junal as $lst) {
                                        JournalList::where('journal_id', $lst->id)->delete();
                                        Journal::findorFail($lst->id)->delete();
                                    }
                                }
                            }
                        }
                        $lost_list->delete();
                    }

                    $delete = Shrinkage::find($id)->delete();

                    if ($dt->is_lost == 1) {
                    } else {
                        $deleteSimulate = ShrinkageSimulate::where('shrinkage_id', $id)->delete();

                        $utang = Debt::where('relasi_trx', 'penyusutan_' . $id);
                        if ($utang->count() > 0) {
                            $hutang = $utang->first();
                            if ($hutang->sync_status == 1) {
                                $journals = Journal::where('relasi_trx', 'utang_' . $hutang->id)->get();
                                foreach ($journals as $journal) {
                                    JournalList::where('journal_id', $journal->id)->delete();
                                    Journal::findorFail($journal->id)->delete();
                                }
                            }

                            $delete = Debt::find($hutang->id)->delete();

                            $dph = DebtPaymentHistory::where('debt_id', $hutang->id)->get();
                            foreach ($dph as $dh) {
                                if ($dh->sync_status == 1) {
                                    $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                                    foreach ($journals as $journal) {
                                        JournalList::where('journal_id', $journal->id)->delete();
                                        Journal::findorFail($journal->id)->delete();
                                    }
                                }
                            }

                            $history = DebtPaymentHistory::where('debt_id', $hutang->id)->delete();
                        }
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

    public function mlFixedAsset(Request $request)
    {
        $columns = ['id', 'name', 'userid'];
        $keyword = $request->keyword;

        $data = MlFixedAsset::orderBy('id', 'asc')
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

    public function mlAccumulateDepreciation(Request $request)
    {
        $columns = ['id', 'name', 'userid'];
        $keyword = $request->keyword;

        $data = MlAccumulatedDepreciation::orderBy('id', 'asc')
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

    public function mlAdminGeneralFee(Request $request)
    {
        $columns = ['id', 'name', 'userid'];
        $keyword = $request->keyword;

        $data = MlAdminGeneralFee::orderBy('id', 'asc')
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

    protected function live_sync($id)
    {
        $dt = Shrinkage::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $dt->ml_admin_general_fee_id;
            $accode_code_id = 10;
            $keterangan = $dt->note;
            $rf = $dt->ml_accumulated_depreciation_id . '_' . 3;
            $st = $untuk . '_' . $accode_code_id;

            if ($dt->is_lost == 1) {
                $waktu = strtotime($dt->date);
                $transaction_name = $this->get_nama_transaksi($untuk, $accode_code_id, $keterangan) . '( ' . $dt->name . ' )';
                $this->sync_journal($transaction_name, $rf, $st, $dt->initial_value, $id, $waktu, 0);
            } else {
                $sims = ShrinkageSimulate::where('shrinkage_id', $id)->get();
                if ($sims->count() > 0) {
                    foreach ($sims as $ndex => $sim) {
                        $nominal = $sim->shrinkage;
                        $waktu = strtotime($sim->date);
                        $transaction_name = $this->get_nama_transaksi($untuk, $accode_code_id, $keterangan);
                        $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $ndex);
                    }
                }
            }
        }
    }

    public function sync(Request $request)
    {
        $input = $request->all();
        $dt = Shrinkage::where('id', $input['id'])->first();
        if ($dt->sync_status !== 1) {
            $untuk = $dt->ml_admin_general_fee_id;
            $accode_code_id = 10;
            $keterangan = $dt->note;
            $rf = $dt->ml_accumulated_depreciation_id . '_' . 3;
            $st = $untuk . '_' . $accode_code_id;
            if ($dt->is_lost == 1) {
                $waktu = strtotime($dt->date);
                $transaction_name = $this->get_nama_transaksi($untuk, $accode_code_id, $keterangan) . '( ' . $dt->name . ' )';
                $this->sync_journal($transaction_name, $rf, $st, $dt->initial_value, $input['id'], $waktu, 0);
            } else {
                $sims = ShrinkageSimulate::where('shrinkage_id', $input['id'])->get();
                if ($sims->count() > 0) {
                    foreach ($sims as $ndex => $sim) {
                        $nominal = $sim->shrinkage;
                        $waktu = strtotime($sim->date);
                        $transaction_name = $this->get_nama_transaksi($untuk, $accode_code_id, $keterangan);
                        $this->sync_journal($transaction_name, $rf, $st, $nominal, $input['id'], $waktu, $ndex);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function get_nama_transaksi($untuk, $accode_code_id, $keterangan)
    {
        if ($accode_code_id == 10) {
            $data = MlAdminGeneralFee::findorFail($untuk);
            $transaction_name = $data->name;
            return $transaction_name;
        } elseif ($accode_code_id == 3) {
            $data = MlAccumulatedDepreciation::findorFail($untuk);
            $transaction_name = $data->name;
            return $transaction_name;
        }
    }

    protected function make_asset_journal($id)
    {
        $master = Shrinkage::findorFail($id);
        $arr = explode('_', $master->buying_with_account);
        $waktu = strtotime($master->date);

        $nama_asset = MlFixedAsset::findorFail($master->ml_fixed_asset_id);
        if ($arr[1] == 1) {
            $query = MlCurrentAsset::findorFail($arr[0]);
            $nama_akun = $query->name;
        } elseif ($arr[1] == 4) {
            $query = MlShorttermDebt::findorFail($arr[0]);
            $nama_akun = $query->name;
        } elseif ($arr[1] == 5) {
            $query = MlLongtermDebt::findorFail($arr[0]);
            $nama_akun = $query->name;
        }

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
            'journal_id' => 0,
            'transaction_id' => 9,
            'transaction_name' => 'Pembelian Aset - ' . $master->name,
            'rf_accode_id' => $arr[0] . '_' . $arr[1],
            'st_accode_id' => $master->ml_fixed_asset_id . '_2',
            'nominal' => $master->initial_value,
            'total_balance' => $master->initial_value,
            'color_date' => $this->set_color(9),
            'created' => $waktu,
            'relasi_trx' => 'penyusutan_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        $data_list_insert = [
            'journal_id' => $journal_id,
            'rf_accode_id' => '',
            'st_accode_id' => $master->ml_fixed_asset_id . '_2',
            'account_code_id' => 2,
            'asset_data_id' => $master->ml_fixed_asset_id,
            'asset_data_name' => $nama_asset->name,
            'credit' => 0,
            'debet' => $master->initial_value,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JOurnalList::insert($data_list_insert);

        $data_list_insert2 = [
            'journal_id' => $journal_id,
            'rf_accode_id' => $arr[0] . '_' . $arr[1],
            'st_accode_id' => '',
            'account_code_id' => $arr[1],
            'asset_data_id' => $arr[0],
            'asset_data_name' => $nama_akun,
            'credit' => $master->initial_value,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert2);
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $index)
    {
        $master = Shrinkage::findorFail($id);
        $arr = explode('_', $master->buying_with_account);
        if ($master->is_lost !== 1 && $index == 0 && $arr[2] == 1) {
            $this->make_asset_journal($id, $waktu);
        }

        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => userOwnerId(),
            'journal_id' => 0,
            'transaction_id' => 10,
            'transaction_name' => $master->is_lost == 1 ? $transaction_name : $transaction_name . ' - ' . $master->name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal,
            'total_balance' => $nominal,
            'color_date' => $this->set_color(10),
            'created' => $waktu,
            'relasi_trx' => 'penyusutan_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        $data_list_insert = [
            'journal_id' => $journal_id,
            'rf_accode_id' => '',
            'st_accode_id' => $st,
            'account_code_id' => $ex_st[1],
            'asset_data_id' => $ex_st[0],
            'asset_data_name' => $this->get_nama_transaksi($ex_st[0], $ex_st[1], ''),
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
            'asset_data_name' => $this->get_nama_transaksi($ex_rf[0], $ex_rf[1], ''),
            'credit' => $nominal,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert2);

        $me = Shrinkage::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function single_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'penyusutan_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->get();
        if ($journal->count() > 0) {
            foreach ($journal as $j) {
                JournalList::where('journal_id', $j->id)->delete();
                Journal::findorFail($j->id)->delete();
            }

            Shrinkage::where('id', $input['id'])->update([
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

    public function lost(Request $request)
    {
        $data = Shrinkage::findorFail($request->id);
        return response()->json([
            'data' => $data,
            'success' => true,
        ]);
    }

    public function lost_store(Request $request)
    {
        try {
            $input = $request->all();
            $p = Shrinkage::findorFail($input['transaction_id']);

            $data_insert = [
                'ml_fixed_asset_id' => $p->ml_fixed_asset_id,
                'ml_accumulated_depreciation_id' => $p->ml_accumulated_depreciation_id,
                'ml_admin_general_fee_id' => $p->ml_admin_general_fee_id,
                'name' => 'Pengurangan Asset ' . $input['asset_name'],
                'initial_value' => $input['lost_value'],
                'quantity' => $input['lost_quantity'],
                'buying_price' => $p->buying_price,
                'useful_life' => 1,
                'residual_value' => 0,
                'note' => $input['asset_note'],
                'user_id' => $this->user_id_manage(session('id')),
                'sync_status' => 0,
                'is_lost' => 1,
                'relasi_trx' => $input['transaction_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'date' => date('Y-m-d'),
            ];

            $sr = Shrinkage::create($data_insert);
            $this->live_sync($sr->id);

            $stok_awal = $p->quantity;
            $stok_akhir = $stok_awal - $input['lost_quantity'];
            $p->quantity = $stok_akhir;
            $p->save();

            return response()->json([
                'success' => true,
                'message' => 'Sukses Simpan Data',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
