<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BusinessGroup;
use App\Models\MdClient;
use App\Models\MlAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        $view = 'client';

        return view('main.invoice.client.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('email', function ($data) {
                return $data->email;
            })
            ->addColumn('address', function ($data) {
                return $data->address;
            })
            ->addColumn('phone', function ($data) {
                return $data->phone;
            })
            ->addColumn('mobile', function ($data) {
                return $data->mobile;
            })
            ->addColumn('fax', function ($data) {
                return $data->fax;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0)" class="edit btn btn-warning btn-sm me-2"  onclick="editData(' . $data->id . ')">Ubah</a>';
                $btn .= '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="deleteData(event, ' . $data->id . ')">Hapus</a>';
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'user_id',
            'name',
            'email',
            'address',
            'phone',
            'mobile',
            'fax',
        ];
        $keyword = $request->keyword;
        $user_id = session('id') ?? Auth::user()->id;
        $checkUser = MlAccount::find($user_id);
        $userIdAll = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');
        
        $data = MdClient::orderBy('id', 'desc')
                    ->whereIn('user_id', $userIdAll)
                    ->select($columns)
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
        $view = 'client-create';

        return view('main.invoice.client.create', compact('view'));
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
                $create = MdClient::create($data);

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
        $data = MdClient::findOrFail($id);
        $view = 'client-edit';

        return view('main.invoice.client.edit', compact('view', 'data'));
    }

    public function show(string $id)
    {
        $data = MdClient::findOrFail($id);
        $view = 'client-edit';

        return response()->json([
            'status' => true,
            'message' => 'Data Berhasil di dapatkankan!',
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = MdClient::findOrFail($id)->update($data);
                
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
                $delete = MdClient::find($id)->delete();

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
