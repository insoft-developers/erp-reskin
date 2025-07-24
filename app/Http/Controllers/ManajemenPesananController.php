<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\PosController;
use App\Models\Account;
use App\Models\Branch;
use App\Models\FollowUp;
use App\Models\InterProduct;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MdProduct;
use App\Models\MlAccount;
use App\Models\MlCurrentAsset;
use App\Models\MlIncome;
use App\Models\MtRekapitulasiHarian;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManajemenPesananController extends Controller
{
    use CommonTrait;

    public function index(Request $request)
    {
        $userId = $this->get_owner_id($request->session()->get('id'));
        $branch = Branch::where('account_id', $userId)->get();
        $paymentMethods = Penjualan::select('payment_method')->whereNotNull('payment_method')->distinct()->pluck('payment_method');
        $view = 'landing_page';

        $user = MlAccount::where('id', session('id'))->first();
        $staff = DB::table('ml_accounts')
            ->where('branch_id', $user->branch_id)
            ->select(['id', 'fullname'])
            ->get();

        return view('main.manajemen_pesanan.index', compact('view', 'branch', 'paymentMethods', 'staff'));
    }

    public function getData(Request $request)
    {
        $id = $request->session()->get('id');
        $user = Account::whereId($id)->first();
        $branch_id = $user->branch_id;
        $userId = $this->get_owner_id($request->session()->get('id'));
        $query = Penjualan::query();

        if ($branch_id) {
            $query->whereBranch_id($branch_id);
        }

        if ($request->has('flag') && $request->flag !== '') {
            $flagId = $request->flag;
            $query->where('flag_id', $flagId);
        }

        if ($request->has('price_type') && $request->price_type !== null) {
            $priceType = $request->price_type;
            $query->where('price_type', $priceType);
        }

        if ($request->has('selected_range')) {
            $selectedRange = $request->input('selected_range');
            $today = Carbon::today();
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            switch ($selectedRange) {
                case 'isToday':
                    $query->whereDate('created', $today);
                    break;
                case 'isYesterday':
                    $yesterday = (clone $today)->subDay();
                    $query->whereDate('created', $yesterday);
                    break;
                case 'isThisWeek':
                    $query->whereBetween('created', [$today->startOfWeek(), $today->endOfWeek()]);
                    break;
                case 'isLastWeek':
                    $lastWeek = (clone $today)->subWeek();
                    $query->whereBetween('created', [$lastWeek->startOfWeek(), $lastWeek->endOfWeek()]);
                    break;
                case 'isThisMonth':
                    $query->whereMonth('created', $today->month)->whereYear('created', $today->year);
                    break;
                case 'isLastMonth':
                    $lastMonth = (clone $today)->subMonth();
                    $query->whereMonth('created', $lastMonth->month)->whereYear('created', $lastMonth->year);
                    break;
                case 'isThisYear':
                    $query->whereYear('created', $today->year);
                    break;
                case 'isLastYear':
                    $lastYear = (clone $today)->subYear();
                    $query->whereYear('created', $lastYear->year);
                    break;
                case 'isRangeDate':
                    $query->whereBetween('created', [$startDate, $endDate]);
                    break;
            }
        }

        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            $query->where(function ($query) use ($keyword) {
                $query
                    ->where('reference', 'like', '%' . $keyword . '%')
                    ->orWhere('cust_name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('customer', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere('detail', 'like', '%' . $keyword . '%')
                    ->orWhere('status', 'like', '%' . $keyword . '%');
            });
        }

        if (isset($request->staff)) {
            $staff = $request->staff;
            $query->where('staff_id', $staff);
        }

        // Filter by branch
        if ($request->has('branch') && $request->branch !== '') {
            $branchId = $request->branch;
            $query->where('branch_id', $branchId);
        }

        // Filter by transaction_status
        if ($request->has('transaction_status') && $request->transaction_status !== '') {
            $transactionStatus = $request->transaction_status;
            $query->where('status', $transactionStatus);
        }

        // Filter by payment_status
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $paymentStatus = $request->payment_status;
            $query->where('payment_status', $paymentStatus);
        }

        // Filter by payment_method
        if ($request->has('payment_method') && $request->payment_method !== '') {
            $paymentMethod = $request->payment_method;
            $query->where('payment_method', $paymentMethod);
        }

        // Order by
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $columns = $request->input('columns');
            $orderColumn = $columns[$orderColumnIndex]['data'];

            $allowedColumns = [
                'reference',
                'date',
                //'detail_order',
                'paid',
                'diskon',
                'tax',
                'shipping_cost',
                'order_total',
                'payment_method',
                'desk',
                'branch',
                'staff',
                'sync_status',
            ];

            if (in_array($orderColumn, $allowedColumns)) {
                if ($orderColumn == 'date') {
                    $orderColumn = 'created'; // Assuming the column name in the database is 'created'
                }
                $query->orderBy($orderColumn, $orderDirection);
            }
        }

        $config = DB::table('ml_site_config')->first();

        $query->where('user_id', $userId);
        return DataTables::of($query)
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->editColumn('price_type', function ($row) {
                if ($row->price_type == 'price_cus') {
                    return 'Custom';
                } elseif ($row->price_type == 'price_mp') {
                    return 'Marketplace';
                } elseif ($row->price_type == 'price_ta') {
                    return 'Takeaway - Delivery';
                } else {
                    return 'Default - Dine In';
                }
            })
            ->addColumn('flag', function ($row) {
                return $row->flag ? $row->flag->flag : '-';
            })
            ->addColumn('reference', function ($row) {
                return $row->reference;
            })
            ->addColumn('opsi', function ($row) {
                if ($row->sync_status == 1) {
                    $btn = '';
                    $btn .= '<div class="d-flex">';

                    if ($row->payment_status != -1 && $row->payment_status != -2) {
                        $btn .= '<a title="UnSync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="unsync(' . $row->id . ')" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a>';
                    }

                    if ($row->payment_status == 1) {
                        $btn .= '<a title="Refund" style="margin-right:3px;" href="javascript:void(0);" onclick="refund(' . $row->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-file-invoice-dollar"></i></a>';

                        $btn .= '<a title="Send transaction to void" style="margin-right:3px;" href="javascript:void(0);" onclick="voidd(' . $row->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-cancel"></i></a>';
                    } elseif ($row->payment_status == -1) {
                    } else {
                        if ($row->payment_method == 'randu-wallet') {
                        } else {
                            $btn .= '<a title="Update to PAID" style="margin-right:3px;" href="javascript:void(0);"  onclick="payData(' . $row->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-dollar"></i></a>';
                        }
                    }
                    $btn .= '</div>';
                    return $btn;
                } else {
                    $btn = '';
                    $btn .= '<div class="d-flex">';
                    if ($row->payment_status != -2) {
                        $btn .= '<a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);"  onclick="syncData(' . $row->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a>';
                    }

                    if ($row->payment_status == 1) {
                        // $btn .= '<a title="Refund" style="margin-right:3px;" href="javascript:void(0);" onclick="refund('.$row->id.')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-file-invoice-dollar"></i></a>';

                        if ($row->payment_method == 'randu-wallet') {
                        } else {
                        }
                    } elseif ($row->payment_status == -1 || $row->payment_status == -2) {
                    } else {
                        $btn .= '<a title="Update to PAID" style="margin-right:3px;" href="javascript:void(0);"  onclick="payData(' . $row->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-dollar"></i></a>';
                    }

                    $btn .= '</div>';

                    return $btn;
                }
            })
            ->addColumn('date', function ($row) {
                return Carbon::parse($row->created)->format('d-m-Y H:i:s');
            })
            ->addColumn('customer', function ($row) {
                $data = '';
                if ($row->customer_id) {
                    $data .= $row->customer->name;
                    if ($row->customer->alamat) {
                        $data .= ' | ' . $row->customer->alamat;
                    }

                    if ($row->customer->phone) {
                        $data .= ' | ' . $row->customer->phone;
                    }

                    if ($row->customer->email) {
                        $data .= ' | ' . $row->customer->email;
                    }
                    return $data;
                } else {
                    return $row->cust_name ?? '-';
                }
            })
            ->addColumn('transaction_status', function ($row) {
                return $row->status === '0' ? '<div class="text-warning"><span class="mdi mdi-clock"></span> Pending</div>' : ($row->status === '1' ? '<div class="text-warning"><span class="mdi mdi-clock"></span> Process</div>' : ($row->status === '2' ? '<div class="text-warning"><span class="mdi mdi-clock"></span> Cooking/Packing</div>' : ($row->status === '3' ? '<div class="text-success"><span class="mdi mdi-truck"></span> Shipped</div>' : ($row->status === '4' ? '<div class="text-success"><span class="mdi mdi-check-circle"></span> Complete</div>' : ($row->status === '5' ? '<div class="text-danger"><span class="mdi mdi-close-circle"></span> Canceled</div>' : ($row->status === '-2' ? '<div class="text-danger"><span class="mdi mdi-close-circle"></span> Void</div>' : ''))))));
            })
            ->addColumn('payment_status', function ($row) use ($config) {
                // return $row->payment_status < 0 ? 'Canceled' : ($row->payment_status === 1 ? 'Paid' : 'UnPaid');
                $canceledCondition = Carbon::parse($row->payment_start_at)->addMinutes(10)->isPast();

                return $canceledCondition
                    ? ($row->payment_status === 1
                        ? '<div class="text-success"><span class="mdi mdi-check-decagram"></span> Paid</div>'
                        : '<div class="text-danger"><span class="mdi mdi-close-circle"></span> Canceled</div>')
                    : ($row->payment_status === 1
                        ? '<div class="text-success"><span class="mdi mdi-check-decagram"></span> Paid</div>'
                        : ($row->payment_status === -1
                            ? '<div class="text-danger"><span class="mdi mdi-close-circle"></span> Refunded</div>'
                            : // Tambahkan pengecekan untuk status -2
                            ($row->payment_status === -2
                                ? '<div class="text-danger"><span class="mdi mdi-close-circle"></span> Void</div>'
                                : '<div class="text-warning"><span class="mdi mdi-clock-alert"></span> UnPaid</div>')));
            })
            ->addColumn('detail_order', function ($row) {
                return $row->detail;
            })
            ->addColumn('total_order', function ($row) {
                return 'Rp ' . number_format($row->paid ?? 0, 0, ',', ',');
            })
            ->addColumn('discount', function ($row) {
                return 'Rp ' . number_format($row->diskon ?? 0, 0, ',', ',');
            })
            ->addColumn('tax', function ($row) {
                return 'Rp ' . number_format($row->tax ?? 0, 0, ',', ',');
            })
            ->addColumn('shipping_cost', function ($row) {
                return 'Rp ' . number_format($row->shipping ?? 0, 0, ',', ',');
            })
            ->addColumn('order_total', function ($row) {
                $calc = $row->paid + $row->tax - $row->diskon;
                // return $row->order_total ?? $calc;
                return 'Rp ' . number_format($row->order_total ?? $calc, 0, ',', ',');
            })
            ->addColumn('cs', function ($row) {
                return $row->cs ? $row->cs->name : null;
            })
            ->addColumn('payment_method', function ($row) {
                return $row->payment_method;
            })
            ->addColumn('desk', function ($row) {
                return $row->qr_codes_id ? $row->desk->no_meja ?? '-' : '-';
            })
            ->addColumn('branch', function ($row) {
                return $row->branch_id ? $row->branch->name : '-';
            })
            ->addColumn('staff', function ($row) {
                return $row->staff_id ? $row->staff->fullname : '-';
            })
            ->addColumn('processing', function ($data) {
                $cust_phone = $data->customer->phone ?? $data->cust_phone;
                $cust_name = $data->customer->name ?? $data->cust_name;
                $cust_kecamatan = $data->customer->kecamatan ?? $data->cust_kecamatan;
                $cust_kelurahan = $data->customer->kelurahan ?? $data->cust_kelurahan;
                $cust_alamat = $data->customer->alamat ?? $data->cust_alamat;

                $phone = isset($cust_phone) ? validationPhoneNumber($cust_phone) : '';
                $baseUrlWa = "https://wa.me/$phone?text=";
                $processing = FollowUp::orderBy('id', 'asc')
                    ->whereIn('name', ['Text Welcome', 'Text Success', 'Text COD'])
                    ->where('account_id', session('id'))
                    ->get();

                $btn = '<div class="dropdown m-2">';
                $btn .= '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">';
                $btn .= 'Text Processing';
                $btn .= '</button>';
                $btn .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
                foreach ($processing as $key => $value) {
                    $plainText = str_replace(['[name]', '[phone]', '[kecamatan]', '[kelurahan]', '[alamat]'], [$cust_name, $cust_phone, $cust_kecamatan, $cust_kelurahan, $cust_alamat], $value->text);

                    $btn .= '<li><a class="dropdown-item" href="' . $baseUrlWa . $plainText . '" target="_blank">' . $value->name . '</a></li>';
                }
                $btn .= '</ul>';
                $btn .= '</div>';

                return $btn;
            })
            ->addColumn('struk', function ($data) {
                // return $row->payment_status < 0 ? 'Canceled' : ($row->payment_status === 1 ? 'Paid' : 'UnPaid');
                $canceledCondition = Carbon::parse($data->payment_start_at)->addMinutes(10)->isPast();

                // if ($data->status === 'completed') {
                $btn = '<div class="d-flex flex-column gap-2">';
                $btn .= $data->detail;
                $btn .= '<button class="btn btn-primary view-struk-button" penjualan-id="' . $data->reference . '" type="button">';
                $btn .= 'Detail Pesanan';
                $btn .= '</button>';

                if (!$canceledCondition && $data->payment_status === 0 && $data->payment_return_url) {
                    // if ($data->payment_return_url) {
                    $btn .= '<a href="' . $data->payment_return_url . '" target="_blank" class="btn btn-success btn-sm">Payment</a>';
                }

                $btn .= '</div>';

                return $btn;
                // } else {
                //     return '-';
                // }
            })
            ->addColumn('followup', function ($data) {
                $cust_phone = $data->customer->phone ?? $data->cust_phone;
                $cust_name = $data->customer->name ?? $data->cust_name;
                $cust_kecamatan = $data->customer->kecamatan ?? $data->cust_kecamatan;
                $cust_kelurahan = $data->customer->kelurahan ?? $data->cust_kelurahan;
                $cust_alamat = $data->customer->alamat ?? $data->cust_alamat;

                $phone = isset($cust_phone) ? validationPhoneNumber($cust_phone) : '';
                $baseUrlWa = "https://wa.me/$phone?text=";
                $followup = FollowUp::orderBy('id', 'asc')
                    ->where('type', 'followup')
                    ->whereNotIn('name', ['Text Welcome', 'Text Success', 'Text COD'])
                    ->where('account_id', session('id'))
                    ->get();

                $btn = '<div class="dropdown m-2">';
                $btn .= '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">';
                $btn .= 'Followup';
                $btn .= '</button>';
                $btn .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
                foreach ($followup as $key => $value) {
                    $plainText = str_replace(['[name]', '[phone]', '[kecamatan]', '[kelurahan]', '[alamat]'], [$cust_name, $cust_phone, $cust_kecamatan, $cust_kelurahan, $cust_alamat], $value->text);

                    $btn .= '<li><a class="dropdown-item" href="' . $baseUrlWa . $plainText . '" target="_blank">' . $value->name . '</a></li>';
                }
                $btn .= '</ul>';
                $btn .= '</div>';

                return $btn;
            })
            ->addColumn('upselling', function ($data) {
                $cust_phone = $data->customer->phone ?? $data->cust_phone;
                $cust_name = $data->customer->name ?? $data->cust_name;
                $cust_kecamatan = $data->customer->kecamatan ?? $data->cust_kecamatan;
                $cust_kelurahan = $data->customer->kelurahan ?? $data->cust_kelurahan;
                $cust_alamat = $data->customer->alamat ?? $data->cust_alamat;

                $phone = isset($cust_phone) ? validationPhoneNumber($cust_phone) : '';
                $baseUrlWa = "https://wa.me/$phone?text=";
                $upselling = FollowUp::orderBy('id', 'asc')
                    ->where('type', 'upselling')
                    ->whereNotIn('name', ['Text Welcome', 'Text Success', 'Text COD'])
                    ->where('account_id', session('id'))
                    ->get();

                $btn = '<div class="dropdown m-2">';
                $btn .= '<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">';
                $btn .= 'Upselling';
                $btn .= '</button>';
                $btn .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
                foreach ($upselling as $key => $value) {
                    $plainText = str_replace(['[name]', '[phone]', '[kecamatan]', '[kelurahan]', '[alamat]'], [$cust_name, $cust_phone, $cust_kecamatan, $cust_kelurahan, $cust_alamat], $value->text);

                    $btn .= '<li><a class="dropdown-item" href="' . $baseUrlWa . $plainText . '" target="_blank">' . $value->name . '</a></li>';
                }
                $btn .= '</ul>';
                $btn .= '</div>';

                return $btn;
            })
            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataCart(Request $request)
    {
        $id = session('id') ?? $request->user_id;
        $user = Account::whereId($id)->first();
        $branch_id = $user->branch_id;
        $userId = $this->get_owner_id($id);
        $query = Penjualan::query();
        $query->where('payment_status', 1);

        if ($branch_id) {
            $query->whereBranch_id($branch_id);
        }

        if ($request->has('price_type') && $request->price_type !== null) {
            $priceType = $request->price_type;
            $query->where('price_type', $priceType);
        }

        if ($request->has('selected_range')) {
            $selectedRange = $request->input('selected_range');
            $today = Carbon::today();
            $startDate = $request->input('startDate') . ' 00:00:00';
            $endDate = $request->input('endDate') . ' 23:59:59';

            switch ($selectedRange) {
                case 'isToday':
                    $query->whereDate('created', $today);
                    break;
                case 'isYesterday':
                    $yesterday = (clone $today)->subDay();
                    $query->whereDate('created', $yesterday);
                    break;
                case 'isThisWeek':
                    $query->whereBetween('created', [$today->startOfWeek(), $today->endOfWeek()]);
                    break;
                case 'isLastWeek':
                    $lastWeek = (clone $today)->subWeek();
                    $query->whereBetween('created', [$lastWeek->startOfWeek(), $lastWeek->endOfWeek()]);
                    break;
                case 'isThisMonth':
                    $query->whereMonth('created', $today->month)->whereYear('created', $today->year);
                    break;
                case 'isLastMonth':
                    $lastMonth = (clone $today)->subMonth();
                    $query->whereMonth('created', $lastMonth->month)->whereYear('created', $lastMonth->year);
                    break;
                case 'isThisYear':
                    $query->whereYear('created', $today->year);
                    break;
                case 'isLastYear':
                    $lastYear = (clone $today)->subYear();
                    $query->whereYear('created', $lastYear->year);
                    break;
                case 'isRangeDate':
                    $query->whereBetween('created', [$startDate, $endDate]);
                    break;
            }
        } else {
            $today = Carbon::today();
            $query->whereMonth('created', $today->month)->whereYear('created', $today->year);
        }

        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            $query->where(function ($query) use ($keyword) {
                $query
                    ->where('reference', 'like', '%' . $keyword . '%')
                    ->orWhere('cust_name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('customer', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere('detail', 'like', '%' . $keyword . '%')
                    ->orWhere('status', 'like', '%' . $keyword . '%');
            });
        }

        if (isset($request->staff)) {
            $staff = $request->staff;
            $query->where('staff_id', $staff);
        }

        if (isset($request->staff_id)) {
            $staff_id = $request->staff_id;
            $query->where('staff_id', $staff_id);
        }

        if ($request->has('flag_id') && $request->flag_id) {
            $flagId = $request->flag_id;
            $query->where('flag_id', $flagId);
        }

        // Filter by branch
        if ($request->has('branch') && $request->branch) {
            $branchId = $request->branch;
            $query->where('branch_id', $branchId);
        }

        // Filter by transaction_status
        if ($request->has('transaction_status') && $request->transaction_status) {
            $transactionStatus = $request->transaction_status;
            $query->where('status', $transactionStatus);
        }

        // Filter by payment_status
        if ($request->has('payment_status') && $request->payment_status) {
            $paymentStatus = $request->payment_status;
            $query->where('payment_status', $paymentStatus);
        }

        // Filter by payment_method
        if ($request->has('payment_method') && $request->payment_method) {
            $paymentMethod = $request->payment_method;
            $query->where('payment_method', $paymentMethod);
        }

        // Order by
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $columns = $request->input('columns');
            $orderColumn = $columns[$orderColumnIndex]['data'];

            $allowedColumns = [
                'reference',
                'date',
                //'detail_order',
                'paid',
                'diskon',
                'tax',
                'shipping_cost',
                'order_total',
                'payment_method',
                'desk',
                'branch',
                'staff',
                'sync_status',
            ];

            if (in_array($orderColumn, $allowedColumns)) {
                if ($orderColumn == 'date') {
                    $orderColumn = 'created'; // Assuming the column name in the database is 'created'
                }
                $query->orderBy($orderColumn, $orderDirection);
            }
        }

        $config = DB::table('ml_site_config')->first();

        $query->where('user_id', $userId)->where('payment_status', 1)->get();

        $omset_penjualan = $query->sum('paid');
        $total_penjualan = $query->sum('order_total');
        $total_ongkir = $query->sum('shipping');
        $total_diskon = $query->sum('diskon');
        $total_tax = $query->sum('tax');
        $data = [
            'omset_penjualan_no_format' => $omset_penjualan,
            'omset_penjualan' => 'Rp ' . number_format($omset_penjualan, 0, ',', '.'),
            'total_penjualan' => 'Rp ' . number_format($total_penjualan, 0, ',', '.'),
            'total_ongkir' => 'Rp ' . number_format($total_ongkir, 0, ',', '.'),
            'total_diskon' => 'Rp ' . number_format($total_diskon, 0, ',', '.'),
            'total_tax' => 'Rp ' . number_format($total_tax, 0, ',', '.'),
        ];

        // dd($data, $request->all());

        return response()->json([
            'data' => $data,
            'status' => true,
            'message' => 'Data Berhasil Diambil!',
        ]);
    }

    public function bulk_update_status(Request $request)
    {
        $penjualans = $request->input('penjualans');
        $status = $request->input('status');

        // Update status transaksi di database
        Penjualan::whereIn('id', $penjualans)
            //->where('payment_status', 1)
            ->update(['status' => $status]);

        return response()->json(['message' => 'Status transaksi berhasil diubah.']);
    }

    public function bulk_payment_status(Request $request)
    {
        $penjualans = $request->input('penjualans');
        $status = $request->input('status');

        try {
            $user = MlAccount::where('id', session('id'))->first();

            if ($user->petty_cash == 1 && $user->status_cashier == 0) {
                return response()->json(['status' => false, 'message' => 'Mengubah status pembayaran hanya bisa dilakukan saat status kasir terbuka.']);
            }

            // Update status transaksi di database
            $getPenjualan = Penjualan::whereIn('id', $penjualans)->get();

            foreach ($getPenjualan as $key => $value) {
                if ($value->payment_status == $status) {
                    return response()->json(['status' => false, 'message' => 'Ubah Status Pembayaran Ini Sudah Dilakukan Sebelumnya.']);
                }
                if ($value->payment_method == 'randu-wallet') {
                    return response()->json(['status' => false, 'message' => 'Metode pembayaran payment gateway tidak dapat diubah.']);
                }
            }

            Penjualan::whereIn('id', $penjualans)
                ->where('payment_method', '!=', 'randu-wallet')
                ->update(['payment_status' => $status]);

            $status = true;

            foreach ($penjualans as $key => $value) {
                $penjualan = Penjualan::find($value);

                if ($penjualan->payment_method == 'randu-wallet') {
                    $status = false;
                } else {
                    if ($penjualan->payment_status != 1) {
                        $this->updateRekapitulasiHarian($penjualan);

                        // $this->reverseQuantity($penjualan->id);
                    } else {
                        $this->send_to_journal($value);
                        $this->addRekapitulasiHarian($penjualan);

                        // indra
                        $pen = Penjualan::find($value);

                        $detail_penjualans = PenjualanProduct::where('penjualan_id', $pen->id)->get();
                        foreach ($detail_penjualans as $key => $detail_penjualan) {
                            // PENGURANGAN MANUFAKTURE
                            $detail_product = Product::find($detail_penjualan->product_id);
                            if ($detail_product->created_by == 1 && $pen->payment_status == 1) {
                                $this->decrementStock($detail_product->id, $detail_penjualan->quantity, $this->user_id_manage(session('id')));
                            } elseif ($detail_product->created_by != 1 && $pen->payment_status == 1) {
                                $stock_sekarang = $detail_product->quantity;
                                $stock_akhir = $stock_sekarang - $detail_penjualan->quantity;
                                $dp = Product::findorFail($detail_product->id);
                                $dp->quantity = $stock_akhir;
                                $dp->save();
                            }
                        }
                    }
                }
            }

            return response()->json(['status' => true, 'message' => 'Status pembayaran berhasil diubah.']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function updateRekapitulasiHarian($penjualan)
    {
        try {
            // $userId = $penjualan->user_id;
            $userId = Auth::user()->id ?? session('id');

            $order_total = $penjualan->paid;
            $payment_method = $penjualan->payment_method;

            $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();

            if ($payment_method == 'kas') {
                $rekapitulasiHarian['cash_sale'] = $rekapitulasiHarian['cash_sale'] - $order_total;
                $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] - $order_total;
            } elseif ($payment_method == 'bank-bca' || $payment_method == 'bank-bni' || $payment_method == 'bank-mandiri' || $payment_method == 'bank-bri' || $payment_method == 'bank-lain') {
                $rekapitulasiHarian['transfer_sales'] = $rekapitulasiHarian['transfer_sales'] - $order_total;
            } elseif ($payment_method == 'randu-wallet') {
                $rekapitulasiHarian['payment_gateway_sales'] = $rekapitulasiHarian['payment_gateway_sales'] - $order_total;
            } elseif ($payment_method == 'piutang-marketplace' || $payment_method == 'piutang-cod' || $payment_method == 'piutang-usaha') {
                $rekapitulasiHarian['piutang_sales'] = $rekapitulasiHarian['piutang_sales'] - $order_total;
            }

            $rekapitulasiHarian['total_sales'] = $rekapitulasiHarian['total_sales'] - $order_total;
            $rekapitulasiHarian->save();

            return true;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Silahkan membuka kasir terlebih dahulu',
            ]);
        }
    }

    public function addRekapitulasiHarian($penjualan)
    {
        try {
            // $userId = $penjualan->user_id;
            $userId = Auth::user()->id ?? session('id');

            $order_total = $penjualan->paid;
            $payment_method = $penjualan->payment_method;

            $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();

            if ($payment_method == 'kas') {
                $rekapitulasiHarian['cash_sale'] = $rekapitulasiHarian['cash_sale'] + $order_total;
                $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] + $order_total;
            } elseif ($payment_method == 'bank-bca' || $payment_method == 'bank-bni' || $payment_method == 'bank-mandiri' || $payment_method == 'bank-bri' || $payment_method == 'bank-lain') {
                $rekapitulasiHarian['transfer_sales'] = $rekapitulasiHarian['transfer_sales'] + $order_total;
            } elseif ($payment_method == 'randu-wallet') {
                $rekapitulasiHarian['payment_gateway_sales'] = $rekapitulasiHarian['payment_gateway_sales'] + $order_total;
            } elseif ($payment_method == 'piutang-marketplace' || $payment_method == 'piutang-cod' || $payment_method == 'piutang-usaha') {
                $rekapitulasiHarian['piutang_sales'] = $rekapitulasiHarian['piutang_sales'] + $order_total;
            }

            $rekapitulasiHarian['total_sales'] = $rekapitulasiHarian['total_sales'] + $order_total;
            $rekapitulasiHarian->save();

            return true;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Silahkan membuka kasir terlebih dahulu',
            ]);
        }
    }

    public function reverseQuantity($id)
    {
        $userId = Auth::user()->id ?? session('id');
        $penjualan_product = PenjualanProduct::where('penjualan_id', $id)->get();
        foreach ($penjualan_product as $key => $value) {
            $product = MdProduct::find($value['product_id']);

            // JIKA TIDAK MENGGUNAKAN STOCK
            if ($product->buffered_stock == 1) {
                if (isset($product)) {
                    $product['quantity'] = $product['quantity'] + $value['quantity'];
                    $product->save();
                }
                $log_stock_product = LogStock::where('relation_id', $product->id)->where('table_relation', 'md_product')->where('user_id', $userId)->where('stock_out', $value['quantity'])->orderBy('id', 'desc')->first();
                if ($log_stock_product) {
                    $log_stock_product->delete();
                }
            }

            $ingredients = ProductComposition::where('product_id', $value['product_id'])->get();

            if ($product->created_by == 1) {
                foreach ($ingredients as $key => $ingredient) {
                    $stock_use = $value->quantity * $ingredient->quantity;

                    if ($ingredient->product_type == 2) {
                        // JIKA BAHAN SETENGAH JADI
                        $inter_product_id = $ingredient->material_id;
                        $inter_product = InterProduct::find($inter_product_id);
                        $inter_product->stock = $inter_product->stock + $stock_use;
                        $inter_product->save();

                        $log_stock_inter_product = LogStock::where('relation_id', $inter_product->id)->where('table_relation', 'md_inter_product')->where('user_id', $userId)->where('stock_out', $stock_use)->orderBy('id', 'desc')->first();
                        if (isset($log_stock_inter_product)) {
                            $log_stock_inter_product->delete();
                        }
                    } elseif ($ingredient->product_type == 1) {
                        // JIKA BAHAN BAKU
                        $material_id = $ingredient->material_id;
                        $material = Material::find($material_id);
                        $material->stock = $material->stock + $stock_use;
                        $material->save();

                        $log_stock_material = LogStock::where('relation_id', $material->id)->where('table_relation', 'md_material')->where('user_id', $userId)->where('stock_out', $stock_use)->orderBy('id', 'desc')->first();
                        if (isset($log_stock_material)) {
                            $log_stock_material->delete();
                        }
                    }
                }
            }
        }
    }

    public function logStock($table, $id, $stock_in, $stock_out, $user_id)
    {
        try {
            LogStock::create([
                'user_id' => $user_id,
                'relation_id' => $id,
                'table_relation' => $table,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function bulk_payment_method(Request $request)
    {
        $penjualans = $request->input('penjualans');
        $status = $request->input('status');

        // Update status transaksi di database
        Penjualan::whereIn('id', $penjualans)
            ->where('payment_method', '!=', 'randu-wallet')
            ->update(['payment_method' => $status]);

        return response()->json(['message' => 'Metode pembayaran berhasil diubah.']);
    }

    public function bulk_sync_status(Request $request)
    {
        $input = $request->all();
        $ids = $input['ids'];

        foreach ($ids as $id) {
            $this->send_to_journal($id);
        }

        // $penjualans = $request->input('penjualans');
        // $status = $request->input('status');

        return response()->json(['message' => 'Sinkronisasi status berhasil dirubah.']);
    }

    public function single_sync(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $this->send_to_journal($id);

        return response()->json(['message' => 'Sinkronisasi status berhasil dirubah.']);
    }

    // public function single_paid(Request $request)
    // {
    //     $input = $request->all();
    //     $id = $input['id'];

    //     $user = MlAccount::where('id', session('id'))->first();

    //     if ($user->petty_cash == 1 && $user->status_cashier == 0) {
    //         return response()->json(['status' => false, 'message' => 'Mengubah status pembayaran hanya bisa dilakukan saat status kasir terbuka.']);
    //     }

    //     Penjualan::where('id', $id)->update(['payment_status' => 1]);
    //     $this->send_to_journal($id);

    //     $pen = Penjualan::find($id);
    //     // $detail_penjualans = PenjualanProduct::where('penjualan_id', $pen->id)->get();
    //     // foreach ($detail_penjualans as $key => $detail_penjualan) {
    //     //     // PENGURANGAN MANUFAKTURE
    //     //     $detail_product = Product::find($detail_penjualan->product_id);
    //     //     if ($detail_product->created_by == 1 && $pen->payment_status == 1) {
    //     //         $this->decrementStock($detail_product->id, $detail_penjualan->quantity);
    //     //     } else if ($detail_product->created_by != 1 && $pen->payment_status == 1) {
    //     //         $stock_sekarang = $detail_product->quantity;
    //     //         $stock_akhir = $stock_sekarang - $detail_penjualan->quantity;
    //     //         $dp = Product::findorFail($detail_product->id);
    //     //         $dp->quantity = $stock_akhir;
    //     //         $dp->save();
    //     //     }
    //     // }

    //     $detail_penjualans = PenjualanProduct::where('penjualan_id', $pen->id)->get();
    //     foreach ($detail_penjualans as $key => $detail_penjualan) {
    //         $product = Product::find($detail_penjualan->product_id);

    //         // PENGURANGAN MANUFAKTURE
    //         $detail_product = Product::find($detail_penjualan->product_id);
    //         if ($detail_product->created_by == 1 && $pen->payment_status == 1) {
    //             $this->decrementStock($detail_product->id, $detail_penjualan->quantity, $this->user_id_manage(session('id')));
    //         } elseif ($detail_product->created_by != 1 && $pen->payment_status == 1) {
    //             $stock_sekarang = $detail_product->quantity;
    //             $stock_akhir = $stock_sekarang - $detail_penjualan->quantity;
    //             $dp = Product::findorFail($detail_product->id);
    //             $dp->quantity = $stock_akhir;
    //             $dp->save();
    //         }
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Status Pembayaran berhasil dirubah.',
    //     ]);
    // }

    public function single_paid(Request $request)
    {
        try {
            $user = MlAccount::where('id', session('id'))->first();

            if ($user->petty_cash == 1 && $user->status_cashier == 0) {
                return response()->json(['status' => false, 'message' => 'Mengubah status pembayaran hanya bisa dilakukan saat status kasir terbuka.']);
            }

            $penjualans = [$request->input('id')];
            $status = 1;

            // Update status transaksi di database
            $getPenjualan = Penjualan::whereIn('id', $penjualans)->where('payment_method', '!=', 'randu-wallet')->get();

            foreach ($getPenjualan as $key => $value) {
                if ($value->payment_status == $status) {
                    return response()->json(['status' => false, 'message' => 'Ubah Status Pembayaran Ini Sudah Dilakukan Sebelumnya.']);
                }
            }

            Penjualan::whereIn('id', $penjualans)
                ->where('payment_method', '!=', 'randu-wallet')
                ->update(['payment_status' => $status]);

            $status = true;

            if ($status) {
                foreach ($penjualans as $key => $value) {
                    $penjualan = Penjualan::find($value);

                    if ($penjualan->payment_method == 'randu-wallet') {
                        $status = false;
                    } else {
                        if ($penjualan->payment_status != 1) {
                            $this->updateRekapitulasiHarian($penjualan);

                            $this->reverseQuantity($penjualan->id);
                        } else {
                            $this->send_to_journal($value);
                            $this->addRekapitulasiHarian($penjualan);

                            // indra
                            $pen = Penjualan::find($value);

                            $detail_penjualans = PenjualanProduct::where('penjualan_id', $pen->id)->get();
                            foreach ($detail_penjualans as $key => $detail_penjualan) {
                                // PENGURANGAN MANUFAKTURE
                                $detail_product = Product::find($detail_penjualan->product_id);
                                if ($detail_product->created_by == 1 && $pen->payment_status == 1) {
                                    $this->decrementStock($detail_product->id, $detail_penjualan->quantity, $this->user_id_manage(session('id')));
                                } elseif ($detail_product->created_by != 1 && $pen->payment_status == 1 && $detail_product->buffered_stock == 1) {
                                    $this->logStock('md_product', $detail_product->id, 0, $detail_penjualan->quantity, $this->user_id_manage(session('id')));
                                    $stock_sekarang = $detail_product->quantity;
                                    $stock_akhir = $stock_sekarang - $detail_penjualan->quantity;
                                    $dp = Product::findorFail($detail_product->id);
                                    $dp->quantity = $stock_akhir;
                                    $dp->save();
                                }
                            }
                        }
                    }
                }

                return response()->json(['status' => true, 'message' => 'Status pembayaran berhasil diubah.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Metode pembayaran payment gateway tidak dapat diubah.']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function decrementStock($product_id, $quantity, $user_id)
    {
        try {
            $ingredients = ProductComposition::where('product_id', $product_id)->get();
            foreach ($ingredients as $key => $ingredient) {
                $stock_use = $quantity * $ingredient->quantity;

                if ($ingredient->product_type == 2) {
                    // JIKA BAHAN SETENGAH JADI
                    $inter_product_id = $ingredient->material_id;
                    $inter_product = InterProduct::find($inter_product_id);
                    $inter_product->stock = $inter_product->stock - $stock_use;
                    $inter_product->save();

                    $this->logStock('md_inter_product', $inter_product->id, 0, $stock_use, $user_id);
                } elseif ($ingredient->product_type == 1) {
                    // JIKA BAHAN BAKU
                    $material_id = $ingredient->material_id;
                    $material = Material::find($material_id);
                    $material->stock = $material->stock - $stock_use;
                    $material->save();

                    $this->logStock('md_material', $material->id, 0, $stock_use, $user_id);
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function single_refund(Request $request)
    {
        $input = $request->all();

        try {
            $penjualan = Penjualan::findorfail($input['id']);
            if ($penjualan->staff_id != session('id')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Refund Gagal, Hanya Bisa Dilakukan Oleh User yang bersangkutan.',
                ]);
            }

            if ($penjualan->payment_method == 'randu-wallet') {
                return response()->json([
                    'status' => false,
                    'message' => 'Refund Gagal, Metode pembayaran randu-wallet tidak dapat di refund.',
                ]);
            }

            $penjualan->payment_status = -1;
            $penjualan->save();
            $this->make_refund_journal($input['id']);
            $this->updateRekapitulasiHarian($penjualan);

            // UPDATE PIUTANG
            if ($penjualan->payment_method == 'piutang-cod' || $penjualan->payment_method == 'piutang-usaha' || $penjualan->payment_method == 'piutang-marketplace') {
                $this->updatePiutang($penjualan);
            }

            return response()->json([
                'status' => true,
                'message' => 'Refund Berhasil.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function make_refund_journal($id)
    {
        try {
            $penjualan = Penjualan::findorFail($id);

            $payment = $penjualan->payment_method;
            $akun_satu = MlCurrentAsset::where('userid', $this->user_id_manage($penjualan->user_id))
                ->where('code', $payment)
                ->first();

            $akun_dua = MlIncome::where('userid', $this->user_id_manage($penjualan->user_id))
                ->where('code', 'penjualan-produk')
                ->first();

            $cost_time = date('Y-m-d', strtotime($penjualan->created));
            $waktu = strtotime($cost_time);

            $data_journal = [
                'userid' => $this->user_id_manage($penjualan->user_id),
                'journal_id' => 0,
                'transaction_id' => 10,
                'transaction_name' => 'Refund Penjualan ' . $penjualan->reference,
                'rf_accode_id' => $akun_satu->id . '_1',
                'st_accode_id' => $akun_dua->id . '_7',
                'debt_data' => '',
                'nominal' => $penjualan->paid,
                'total_balance' => $penjualan->paid,
                'is_opening_balance' => 0,
                'color_date' => $this->set_color(10),
                'edit_count' => 0,
                'created' => $waktu,
                'relasi_trx' => 'refundx',
                'description' => 'Refund Penjualan ' . $penjualan->reference . ' ' . $penjualan->cust_name,
            ];

            $journal_id = Journal::insertGetId($data_journal);

            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_satu->id . '_1',
                'st_accode_id' => '',
                'account_code_id' => 1,
                'asset_data_id' => $akun_satu->id,
                'asset_data_name' => $akun_satu->name,
                'credit' => $penjualan->paid,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $akun_dua->id . '_7',
                'account_code_id' => 7,
                'asset_data_id' => $akun_dua->id,
                'asset_data_name' => $akun_dua->name,
                'credit' => 0,
                'debet' => $penjualan->paid,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function single_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'pos_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            Penjualan::where('id', $input['id'])->update([
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

    public function live_unsync($id)
    {
        $code = 'pos_' . $id;

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            Penjualan::where('id', $id)->update([
                'sync_status' => 0,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unsync journal failed!',
            ]);
        }
    }

    public function single_void(Request $request)
    {
        $input = $request->all();

        try {
            $penjualan = Penjualan::findorfail($input['id']);
            $userId = Auth::user()->id ?? session('id');
            if ($penjualan->staff_id != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Void Gagal, Hanya Bisa Dilakukan Oleh User yang bersangkutan.',
                ]);
            }

            if ($penjualan->payment_method == 'randu-wallet') {
                return response()->json([
                    'status' => false,
                    'message' => 'Void Gagal, Metode pembayaran randu-wallet tidak dapat di refund.',
                ]);
            }

            $penjualan->payment_status = -2;
            $penjualan->status = -2;
            $penjualan->save();
            $this->live_unsync($input['id']);


            // $this->restore_persediaan($input['id'])   memang di comment;
            
            
            $this->reverseQuantity($input['id']);
            $this->updateRekapitulasiHarian($penjualan);

            // UPDATE PIUTANG
            if ($penjualan->payment_method == 'piutang-cod' || $penjualan->payment_method == 'piutang-usaha' || $penjualan->payment_method == 'piutang-marketplace') {
                $this->updatePiutang($penjualan);
            }

            return response()->json([
                'status' => true,
                'message' => 'Send to void success.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updatePiutang($penjualan)
    {
        $save_to = MlCurrentAsset::orderBy('id', 'asc')->where('code', $penjualan->payment_method)->where('userid', userOwnerId())->first();

        // if($penjualan->payment_method === 'piutang-usaha') {
        //      $piutang = Receivable::where('save_to', $save_to->id)->where('penjualan_id', $penjualan->id)->where('user_id', userOwnerId())->first();
        // } else {
        if ($penjualan->customer_id == null) {
            $customer_name = 'Walk In Customer';
        } else {
            $customer_name = $penjualan->customer->name;
        }
        $s_name = 'Penjualan ' . $customer_name . ' Piutang';

        $piutang = Receivable::where('save_to', $save_to->id)->where('name', $s_name)->where('user_id', userOwnerId())->first();
        // }

        $piutang->amount = $piutang->amount - $penjualan->paid;
        // $piutang->amount = 0;
        $piutang->save();
    }

    protected function restore_persediaan($value)
    {
        try {
            $pen = Penjualan::find($value);

            $detail_penjualans = PenjualanProduct::where('penjualan_id', $pen->id)->get();
            foreach ($detail_penjualans as $key => $detail_penjualan) {
                // PENGURANGAN MANUFAKTURE
                $detail_product = Product::find($detail_penjualan->product_id);
                if ($detail_product->created_by == 1) {
                    $this->incrementStock($detail_product->id, $detail_penjualan->quantity, $this->user_id_manage(session('id')));
                } elseif ($detail_product->created_by != 1) {
                    $stock_sekarang = $detail_product->quantity;
                    $stock_akhir = $stock_sekarang + $detail_penjualan->quantity;
                    $dp = Product::findorFail($detail_product->id);
                    $dp->quantity = $stock_akhir;
                    $dp->save();
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function incrementStock($product_id, $quantity, $user_id)
    {
        try {
            $ingredients = ProductComposition::where('product_id', $product_id)->get();
            foreach ($ingredients as $key => $ingredient) {
                $stock_use = $quantity * $ingredient->quantity;

                if ($ingredient->product_type == 2) {
                    // JIKA BAHAN SETENGAH JADI
                    $inter_product_id = $ingredient->material_id;
                    $inter_product = InterProduct::find($inter_product_id);
                    $inter_product->stock = $inter_product->stock + $stock_use;
                    $inter_product->save();

                    $this->logStock('md_inter_product', $inter_product->id, $stock_use, 0, $user_id);
                } elseif ($ingredient->product_type == 1) {
                    // JIKA BAHAN BAKU
                    $material_id = $ingredient->material_id;
                    $material = Material::find($material_id);
                    $material->stock = $material->stock + $stock_use;
                    $material->save();

                    $this->logStock('md_material', $material->id, $stock_use, 0, $user_id);
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
