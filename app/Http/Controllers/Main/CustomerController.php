<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use App\Models\MdCustomer;
use App\Models\MlAccount;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $view = 'customer';

        return view('main.crm.customer.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getCustomerData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('email', function ($data) {
                return $data->email;
            })
            ->addColumn('phone', function ($data) {
                return $data->phone;
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
            ->addColumn('kelurahan', function ($data) {
                return $data->kelurahan;
            })
            ->addColumn('alamat', function ($data) {
                return $data->alamat;
            })
            ->addColumn('followup', function ($data) {
                $phone = validationPhoneNumber($data->phone ?? '0');
                $baseUrlWa = "https://wa.me/$phone?text=";

                $btn = '<div class="dropdown m-2">';
                $btn .= '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">';
                $btn .= 'Whatsapp Followup';
                $btn .= '</button>';
                $btn .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
                foreach ($data->followup() as $key => $value) {
                    $plainText = str_replace(
                        ['[name]', '[phone]', '[kecamatan]', '[kelurahan]', '[alamat]'],
                        [$data->name, $data->phone, $data->kecamatan, $data->kelurahan, $data->alamat],
                        $value->text
                    );

                    $formattedMessage = str_replace("\n", "%0A", $plainText);

                    $btn .= '<li><a class="dropdown-item" href="' . $baseUrlWa . $formattedMessage .'" target="_blank">' . $value->name . '</a></li>';
                }
                $btn .= '</ul>';
                $btn .= '</div>';

                return $btn;
            })
            ->addColumn('upselling', function ($data) {
                $phone = validationPhoneNumber($data->phone ?? '0');
                $baseUrlWa = "https://wa.me/$phone?text=";

                $btn = '<div class="dropdown m-2">';
                $btn .= '<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">';
                $btn .= 'Whatsapp Upselling';
                $btn .= '</button>';
                $btn .= '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
                foreach ($data->upselling() as $key => $value) {
                    $plainText = str_replace(
                        ['[name]', '[phone]', '[kecamatan]', '[kelurahan]', '[alamat]'],
                        [$data->name, $data->phone, $data->kecamatan, $data->kelurahan, $data->alamat],
                        $value->text
                    );

                    $formattedMessage = str_replace("\n", "%0A", $plainText);

                    $btn .= '<li><a class="dropdown-item" href="' . $baseUrlWa . $formattedMessage .'" target="_blank">' . $value->name . '</a></li>';
                }
                $btn .= '</ul>';
                $btn .= '</div>';

                return $btn;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0)" class="edit btn btn-warning btn-sm me-2" onclick="editData(' . $data->id . ')">Ubah</a>';
                $btn .= '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="deleteData(event, ' . $data->id . ')">Hapus</a>';
                $btn .= '</div>';

                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getCustomerData(Request $request)
    {
        $columns = [
            'id',
            'name',
            'email',
            'phone',
            'province_id',
            'city_id',
            'district_id',
            'kelurahan',
            'alamat',
        ];
        $keyword = $request->keyword;
        $user_id = session('id') ?? Auth::user()->id;
        $checkUser = MlAccount::find($user_id);
        $user_id = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');

        $data = MdCustomer::orderBy('id', 'desc')
                    ->select($columns)
                    ->whereIn('user_id', $user_id)
                    ->where(function($query) use ($keyword, $columns) {
                        if ($keyword != '') {
                            foreach ($columns as $column) {
                                $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                            }
                        }
                    });
        
        return $data;
    }

    public function create()
    {
        $view = 'customer-create';

        return view('main.crm.customer.create', compact('view'));
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
            $data['user_id'] = session('id');

            return $this->atomic(function () use ($data) {
                $create = MdCustomer::create($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Tambahkan',
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
        $data = MdCustomer::findOrFail($id);
        $view = 'customer-edit';

        return view('main.crm.customer.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = MdCustomer::findOrFail($id)->update($data);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Ubah',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Ubah',
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
                $penjualan = Penjualan::where('customer_id', $id)->first();

                if ($penjualan) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data Gagal Dihapus, Karena Data Ini Telah Digunakan Pada Transaksi Penjualan',
                    ]);
                }
                
                $delete = MdCustomer::find($id)->delete();

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
}
