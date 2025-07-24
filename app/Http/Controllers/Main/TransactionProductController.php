<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\TransactionProduct;
use Illuminate\Http\Request;

class TransactionProductController extends Controller
{
    public function index()
    {
        $view = 'transaction';

        return view('main.katalog_transaction.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('references', function ($data) {
                return $data->references;
            })
            ->addColumn('referal', function ($data) {
                return $data->user()->first()->referal_source ?? null;
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('status_transaction', function ($data) {
                if ($data->status_transaction == 0) {
                    return '<span class="btn btn-info">Proses</span>';
                } elseif ($data->status_transaction == 1) {
                    return '<span class="btn btn-warning">Dikirim</span>';
                } else if ($data->status_transaction == 2) {
                    return '<span class="btn btn-success">Selesai</span>';
                }
            })
            ->addColumn('status_payment', function ($data) {
                if ($data->status_payment == 0) {
                    return '<span class="btn btn-danger">Unpaid</span>';
                } else if ($data->status_payment == 1) {
                    return '<span class="btn btn-info">Paid</span>';
                }
            })
            ->addColumn('province', function ($data) {
                return $data->province->province_name ?? null;
            })
            ->addColumn('city', function ($data) {
                return $data->city->city_name ?? null;
            })
            ->addColumn('district', function ($data) {
                return $data->district->subdistrict_name ?? null;
            })
            ->addColumn('address', function ($data) {
                return $data->address;
            })
            ->addColumn('shipping', function ($data) {
                return $data->shipping;
            })
            ->addColumn('ongkir', function ($data) {
                return 'Rp. ' . number_format($data->ongkir, 0, ',', '.');
            })
            ->addColumn('total_price', function ($data) {
                return 'Rp. ' . number_format($data->total_price, 0, ',', '.');
            })
            ->addColumn('detail_product', function ($data) {
                $html = '';
                foreach ($data->transactionDetail as $key => $value) {
                    $html .= '<div style="line-height: 0.5;">';
                    $html .= '<p>'.$value->product->name ?? null.'<p>';
                    if ($data->status_payment == 1) {
                        $url_download = $value->product->url_download ?? null;
                        $html .= '<a href="'.$url_download.'" target="_blank" style="background-color: green; color: white; padding: 5px; border-radius: 5px; margin-left: 10px">Download</a>';
                    }
                    $html .= '<p> @'.$value->qty.' - Rp. '.number_format($value->price, 0, ',', '.').'</p>';
                    $html .= '</div>';
                }

                return $html;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'references',
            'name',
            'status_transaction',
            'status_payment',
            'province_id',
            'city_id',
            'district_id',
            'address',
            'shipping',
            'ongkir',
            'total_price',
            'created_at',
            'user_id',
        ];
        $keyword = $request->keyword;
        $start_date = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? now()->format('Y-m-d');
        $status_transaction = $request->status_transaction;
        $status_payment = $request->status_payment;

        $data = TransactionProduct::orderBy('id', 'desc')
                    ->select($columns)
                    ->when($start_date, function ($query) use ($start_date, $end_date) {
                        return $query->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
                    })
                    ->when($status_transaction, function ($query) use ($status_transaction) {
                        return $query->where('status_transaction', $status_transaction);
                    })
                    ->when($status_payment, function ($query) use ($status_payment) {
                        return $query->where('status_payment', $status_payment);
                    })
                    ->where(function($query) use ($keyword, $columns) {
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
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 
    }
}
