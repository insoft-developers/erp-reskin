<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStaffRequest;
use App\Models\Account;
use App\Models\MlAccountInfo;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accountID = session('id');
        $view = 'staff';
        $district = DB::table('districts')
			->select('districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')
			->join('regencies', 'regencies.id', '=', 'districts.regency_id')
			->join('provinces', 'provinces.id', '=', 'regencies.province_id')
			->get();
        $branches = DB::table('branches')->where('account_id',$accountID)->limit(1)->get();
        return view('main.staff.index', compact('view','district','branches'));
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
        //
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
        $view = 'staff';
        $data = Account::find($id);
        
        $branches = DB::table('branches')->whereNull('deleted_at')->where('account_id',session('id'))->get();
        $positions = DB::table('staff_positions')->whereNull('deleted_at')->get();
        return view('main.staff.edit', compact('data','view','branches','positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffRequest $request, string $id)
    {
        $data = $request->only(['fullname','username','branch_id','position_id','phone','start_date','is_active', 'clock_in', 'clock_out', 'holiday']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->filled('pin')) {
            $data['pin'] = $request->pin;
        }
        
        $data['clock_in'] = $request->clock_in;
        $data['clock_out'] = $request->clock_out;
        $data['holiday'] = isset($data['holiday']) ? json_encode($data['holiday']) : null;
        Account::find($id)->update($data);

        return redirect()->route('staff.index');
    }


    
}
