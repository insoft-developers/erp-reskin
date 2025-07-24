<?php

namespace App\DataTables;

use App\Models\MdProduct;
use App\Models\MlAccount;
use App\Models\PenjualanProduct;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class LaporanPenjualanProductDataTable extends DataTable
{
    public function get_branch_id(int $user_id)
    {
        $user = MlAccount::where('id', $user_id)->first();
        if ($user->role_code === 'staff') {
            return $user->branch_id;
        } else {
            return $user->id;
        }
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $user_id = session('id') ?? app()->request->user_id;
        $request = app()->request;
        $date = $request->date ?? 'isThisMonth';
        $staff_id = $request->staff_id;
        $payment_method = $request->payment_method;
        $flag_id = $request->flag_id;
        $price_type = $request->price_type;

        $query = MdProduct::with(['penjualanProduct' => function ($query) use ($flag_id, $staff_id, $payment_method, $date, $price_type) {
            $query->whereHas('penjualan', function ($query) use ($flag_id, $staff_id, $payment_method, $date, $price_type) {
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

                if ($date) {
                    switch ($date) {
                        case 'isToday':
                            $query->whereDate('created', Carbon::today());
                            break;
                        case 'isYesterday':
                            $query->whereDate('created', Carbon::yesterday());
                            break;
                        case 'isThisMonth':
                            $query->whereMonth('created', Carbon::now()->month)
                                ->whereYear('created', Carbon::now()->year);
                            break;
                        case 'isLastMonth':
                            $query->whereMonth('created', Carbon::now()->subMonth()->month)
                                ->whereYear('created', Carbon::now()->year);
                            break;
                        case 'isThisYear':
                            $query->whereYear('created', Carbon::now()->year);
                            break;
                        case 'isLastYear':
                            $query->whereYear('created', Carbon::now()->subYear()->year);
                            break;
                        case 'isRangeDate':
                            if (request()->start_date && request()->end_date) {
                                $query->whereBetween('created', [request()->start_date, request()->end_date]);
                            }
                            break;
                    }
                }
            });
        }, 'penjualanProduct.penjualan' => function ($query) use ($flag_id, $staff_id, $payment_method, $date) {
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

            if ($date) {
                switch ($date) {
                    case 'isToday':
                        $query->whereDate('created', Carbon::today());
                        break;
                    case 'isYesterday':
                        $query->whereDate('created', Carbon::yesterday());
                        break;
                    case 'isThisMonth':
                        $query->whereMonth('created', Carbon::now()->month)
                            ->whereYear('created', Carbon::now()->year);
                        break;
                    case 'isLastMonth':
                        $query->whereMonth('created', Carbon::now()->subMonth()->month)
                            ->whereYear('created', Carbon::now()->year);
                        break;
                    case 'isThisYear':
                        $query->whereYear('created', Carbon::now()->year);
                        break;
                    case 'isLastYear':
                        $query->whereYear('created', Carbon::now()->subYear()->year);
                        break;
                    case 'isRangeDate':
                        if (request()->start_date && request()->end_date) {
                            $query->whereBetween('created', [request()->start_date, request()->end_date]);
                        }
                        break;
                }
            }
        }]);

        $query = $query
            ->where('user_id', $this->get_branch_id($user_id));

        $query->whereHas('penjualanProduct', function ($query) use ($flag_id, $staff_id, $payment_method, $date) {
            $query->whereHas('penjualan', function ($query) use ($flag_id, $staff_id, $payment_method, $date) {
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

                if ($date) {
                    switch ($date) {
                        case 'isToday':
                            $query->whereDate('created', Carbon::today());
                            break;
                        case 'isYesterday':
                            $query->whereDate('created', Carbon::yesterday());
                            break;
                        case 'isThisMonth':
                            $query->whereMonth('created', Carbon::now()->month)
                                ->whereYear('created', Carbon::now()->year);
                            break;
                        case 'isLastMonth':
                            $query->whereMonth('created', Carbon::now()->subMonth()->month)
                                ->whereYear('created', Carbon::now()->year);
                            break;
                        case 'isThisYear':
                            $query->whereYear('created', Carbon::now()->year);
                            break;
                        case 'isLastYear':
                            $query->whereYear('created', Carbon::now()->subYear()->year);
                            break;
                        case 'isRangeDate':
                            if (request()->start_date && request()->end_date) {
                                $query->whereBetween('created', [request()->start_date, request()->end_date]);
                            }
                            break;
                    }
                }
            });
        });

        $user = MlAccount::find(session('id'));
        if (!$user->is_upgraded) {
            $query->where('id', 0);
        }

        $dataTable = new EloquentDataTable($query);

        return $dataTable
            ->addColumn('name', function ($query) {
                return $query->name;
            })
            ->addColumn('jumlah_terjual', function ($query) {
                $count = 0;
                foreach ($query->penjualanProduct as $penjualanProduct) {
                    $count += $penjualanProduct->quantity;
                }
                return $count;
            })
            ->orderColumn('jumlah_terjual', function ($query, $order) {
                $query->withSum('penjualanProduct', 'quantity')
                    ->orderBy('penjualan_product_sum_quantity', $order);
            })
            ->addColumn('price', function ($query) {
                return 'Rp. ' . number_format($query->price);
            })
            ->orderColumn('price', function ($query, $order) {
                $query->orderBy('price', $order);
            })
            ->addColumn('total_harga_produk_terjual', function ($query) {
                $harga_jual = $query->price ?? 0;
                $jumlah_penjualan = $query->penjualanProduct->sum('quantity');
                $total_harga_produk_terjual = $harga_jual * $jumlah_penjualan;
                return 'Rp. ' . number_format($total_harga_produk_terjual);
            })
            ->orderColumn('total_harga_produk_terjual', function ($query, $order) {
                $query->withSum('penjualanProduct', 'quantity')
                    ->orderByRaw('price * penjualan_product_sum_quantity ' . $order);
            })
            ->addColumn('hpp_product', function ($query) {
                return 'Rp. ' . number_format($query->cost);
            })
            ->orderColumn('hpp_product', function ($query, $order) {
                $query->orderBy('cost', $order);
            })
            ->addColumn('hpp', function ($query) {
                $jumlah_penjualan = $query->penjualanProduct->sum('quantity');
                $hpp = $jumlah_penjualan * $query->cost ?? 0;
                return 'Rp. ' . number_format($hpp);
            })
            ->orderColumn('hpp', function ($query, $order) {
                $query->withSum('penjualanProduct', 'quantity')
                    ->orderByRaw('cost * penjualan_product_sum_quantity ' . $order);
            })
            ->addColumn('margin_kotor', function ($query) {
                $harga_jual = $query->price ?? 0;
                $jumlah_penjualan = $query->penjualanProduct->sum('quantity');
                $hpp = $jumlah_penjualan * $query->cost ?? 0;
                $total_harga_produk_terjual = $harga_jual * $jumlah_penjualan;
                if ($total_harga_produk_terjual > 0) {
                    $margin_kotor = ($total_harga_produk_terjual - $hpp);
                } else {
                    $margin_kotor = 0;
                }

                return 'Rp. ' . number_format($margin_kotor);
            })
            ->orderColumn('margin_kotor', function ($query, $order) {
                $query->withSum('penjualanProduct', 'quantity')
                    ->orderByRaw('(price * penjualan_product_sum_quantity - cost * penjualan_product_sum_quantity) ' . $order);
            })
            ->addColumn('persentase_margin', function ($query) {
                $harga_jual = $query->price ?? 0;
                $jumlah_penjualan = $query->penjualanProduct->sum('quantity');
                $hpp = $jumlah_penjualan * $query->cost ?? 0;
                $total_harga_produk_terjual = $harga_jual * $jumlah_penjualan;
                if ($total_harga_produk_terjual > 0) {
                    $margin_kotor = ($total_harga_produk_terjual - $hpp);
                    $persentase_margin = ($margin_kotor * 100) / $total_harga_produk_terjual;
                } else {
                    $persentase_margin = 0;
                }

                return round($persentase_margin, 2) . '%';
            })
            ->orderColumn('persentase_margin', function ($query, $order) {
                $query->withSum('penjualanProduct', 'quantity')
                    ->orderByRaw('((price * penjualan_product_sum_quantity - cost * penjualan_product_sum_quantity) * 100 / (price * penjualan_product_sum_quantity)) ' . $order);
            })
        ;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\MdProduct $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MdProduct $model)
    {
        $query = $model->newQuery();

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->addAction(['width' => '170px', 'printable' => false])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'order'     => [[0, 'desc']],
                // 'responsive' => true,
                // 'columnDefs' => [
                //     ['className' => 'none', 'targets' => 0, 'orderable' => true],
                //     ['className' => 'all', 'targets' => 1, 'orderable' => true],
                //     ['className' => 'all', 'targets' => 2, 'orderable' => true],
                //     ['className' => 'all', 'targets' => 3, 'orderable' => true],
                //     ['className' => 'all', 'targets' => 4, 'orderable' => true],
                //     ['className' => 'all', 'targets' => 5, 'orderable' => true],
                //     ['className' => 'all', 'targets' => 6, 'orderable' => true],
                //     ['className' => 'none', 'targets' => 7, 'orderable' => true],
                //     ['className' => 'none', 'targets' => 8, 'orderable' => true],
                //     ['className' => 'none', 'targets' => 9, 'orderable' => true],
                //     ['className' => 'none', 'targets' => 10, 'orderable' => true],
                //     ['className' => 'none', 'targets' => 11, 'orderable' => true],
                // ],
                'buttons'   => [
                    ['extend' => 'excel', 'className' => 'btn btn-default btn-sm no-corner', 'text' => 'Export to Excel'],
                    // ['extend' => 'pdf', 'className' => 'btn btn-default btn-sm no-corner', 'text' => 'Export to PDF'],
                    // ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner', 'text' => 'Print'],
                    // Enable Buttons as per your need
                    //                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
                    //                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
                    //                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
                    //                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
                    //                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'name',
            'jumlah_terjual',
            'price' => ['title' => 'Harga Jual'],
            'total_harga_produk_terjual' => ['title' => 'Omset Penjualan'],
            'hpp_product' => ['title' => 'HPP Product'],
            'hpp' => ['title' => 'HPP Total'],
            'margin_kotor' => ['title' => 'Margin Kotor'],
            'persentase_margin'
            // 'title',
            // 'staff',
            // 'created_at' => ['data' => 'created_at', 'name' => 'test_outputs.created_at', 'title' => 'Created At'],
            // 'start',
            // 'end',
            // 'duration' => ['orderable' => false],
            // 'retest' => ['orderable' => false],
            // 'description',
            // 'job_match',
            // 'mask_public_self',
            // 'core_private_self',
            // 'mirror_perceived_self',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'outputs_disc_datatable_' . time();
    }
}
