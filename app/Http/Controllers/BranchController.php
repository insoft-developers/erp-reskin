<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Http\Exceptions\HttpResponseException;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accountID = session('id');
        $view = 'branch';
        $district = DB::table('districts')
			->select('districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')
			->join('regencies', 'regencies.id', '=', 'districts.regency_id')
			->join('provinces', 'provinces.id', '=', 'regencies.province_id')
			->get();
        return view('main.branch.index', compact('view','district'));
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
    public function store(StoreBranchRequest $request)
    {  
        $data = $request->only(['name','address','phone','district_id']);
        $data['account_id'] = session('id');
        $branchTotal  = Branch::where('account_id',session('id'))->count();
        if($branchTotal >= 1)
        {
            throw new HttpResponseException(response()->json(['message' => 'Anda sudah memiliki cabang sebelumnya'],500));
        }
        else {
            Branch::create($data);
        }
        return response()->json(['success' => true],200);
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
        $id = (int)$id;
        $view = 'branch';
        $data = Branch::whereId($id)->first();
        $district = DB::table('districts') 
			->select('districts.id as district_id','districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')
			->join('regencies', 'regencies.id', '=', 'districts.regency_id')
			->join('provinces', 'provinces.id', '=', 'regencies.province_id')
			->get();
        $view = 'branch-edit';
        return view('main.branch.edit',compact('data','view','district','view'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, string $id)
    {
        Branch::find($id)->update($request->only(['name','address','phone','district_id']));
        return redirect()->route('branch.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Branch::find($id)->delete();
        return response()->json(['success' => true, 'message' => 'Delete success'],200);
    }

    public function getBranchTable(Request $request)
    {
        $keyword = $request->cari;
        $query = DB::table('branches')->leftJoin('districts','districts.id','=','branches.district_id')
        ->join('regencies', 'regencies.id', '=', 'districts.regency_id')
        ->join('provinces', 'provinces.id', '=', 'regencies.province_id')
        ->where('account_id', session('id'))
        ->whereNull('branches.deleted_at');
        if(!empty($keyword)) {
            $query->where('branches.name', 'like', '%'. $keyword .'%')
            ->orWhere('branches.address', 'like', '%'. $keyword .'%')
            ->where('branches.phone', 'like', '%'. $keyword .'%');
        }
        if (!empty($request->name)) {
            $query->where('branches.id', $request->name);
        }
        
        if (!empty($request->address_detail)) {
            $query->where('branches.district_id', $request->address_detail);
        }
        $data = $query->select('branches.id as id','branches.name as name','branches.phone as phone','branches.address as address','districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')->get();
        return Datatables::of($data)
            ->addColumn('address_detail', function($data){
                return '<center>'.$data->distrik.' '.$data->kabupaten.' '.$data->provinsi. '</center>';
            })
            ->addColumn('action', function($data){
                return '<center><a href="'.route('branch.edit',['branch' => $data->id]).'"><button style="width:70px;margin-bottom:5px;" class="btn btn-warning btn-sm">Sunting</button></a><button onclick="branch_delete('.$data->id.')" style="width:70px;" class="btn btn-danger btn-sm">Hapus</button></center>';  
            })
        ->rawColumns(['action','address_detail'])
        ->make(true);
    }
}
