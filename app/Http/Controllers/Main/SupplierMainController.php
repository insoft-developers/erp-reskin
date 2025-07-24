<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SupplierMainController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function supplier_table(Request $request)
    {
        $data = Supplier::where('userid', $this->user_id_manage(session('id')))->get();
        return DataTables::of($data)
            ->addColumn('province', function ($data) {
                return '<div style="white-space:normal;">' . $data->province . '</div>';
            })
            ->addColumn('action', function ($data) {
                return '<center><a title="Edit Data" href="javascript:void(0);" onclick="editData(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-edit"></i></a><a title="Hapus Data" style="margin-top:5px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center>';
            })
            ->rawColumns(['action', 'province'])
            ->make(true);
    }

    public function index()
    {
        $view = 'main-supplier';
        $wilayah = DB::table('districts')->select('districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')->join('regencies', 'regencies.id', '=', 'districts.regency_id')->join('provinces', 'provinces.id', '=', 'regencies.province_id')->get();
        return view('main.supplier', compact('view', 'wilayah'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}'];
            $html = '';
            foreach ($pesanarr as $p) {
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= str_replace(':', '', $o) . '<br>';
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        $input['can_be_deleted'] = 1;
        Supplier::create($input);
        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Supplier::findorFail($id);
        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}'];
            $html = '';
            foreach ($pesanarr as $p) {
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= str_replace(':', '', $o) . '<br>';
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        $input['can_be_deleted'] = 1;
        $data = Supplier::findorFail($id);
        $data->update($input);
        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Supplier::destroy($id);
    }
}
