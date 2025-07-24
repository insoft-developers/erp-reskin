<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetStaffRequest;
use App\Http\Requests\StoreStaffRequest;
use App\Models\Account;
use App\Models\MlAccountInfo;
use App\Models\Penjualan;
use App\Models\Role;
use App\Models\Staff;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
class StaffController extends Controller
{
    public function getStaffData(Request $request)
    {
        $keyword = $request->cari;
        $query = DB::table('ml_accounts')->leftJoin('staff_positions as position','position.id','=','ml_accounts.position_id')
        ->leftJoin('branches', 'branches.id', '=', 'ml_accounts.branch_id')
        ->where('ml_accounts.is_soft_delete', '!=' , 1)
        ->where('ml_accounts.role_code','=','staff');
        if(!empty($request->user_id))
        {
            $query->where('branches.account_id',[$request->user_id]);
        }
        if(!empty($keyword)) {
            $query->where('ml_accounts.name', 'like', '%'. $keyword .'%')
            ->orWhere('ml_accounts.username', 'like', '%'. $keyword .'%')
            ->orWhere('ml_accounts.phone', 'like', '%'. $keyword .'%')
            ->orWhere('branches.name', 'like', '%'. $keyword .'%')
            ->orWhere('position.position', 'like', '%'. $keyword .'%');
        }
        if (!empty($request->branch_id)) {
            $query->where('branches.id', $request->branch_id);
        }
        
        if (!empty($request->position_id)) {
            $query->where('ml_accounts.position_id', $request->position_id);
        }
        $data = $query->select(
            'ml_accounts.id as id',
            'ml_accounts.fullname as name',
            'ml_accounts.email as email',
            'position.position as position',
            'branches.name as branch_name',
            'ml_accounts.phone AS phone',
            'ml_accounts.pin AS pin',
            'ml_accounts.username AS username',
            'ml_accounts.start_date AS start_date',
            DB::raw('CASE 
                WHEN ml_accounts.is_active = 1 THEN "Aktif" 
                ELSE "Non Aktif" 
            END as status')
            
        )->get();
        return DataTables::of($data)
            ->addColumn('start_date', function($data){
                return '<center>'.Carbon::parse($data->start_date)->format('d-m-Y').'</center>';
            })
            ->addColumn('action', function($data){
                return '<center><a href="'.route('staff.edit',['staff' => $data->id]).'"><button style="width:70px;margin-bottom:5px;" class="btn btn-warning btn-sm">Sunting</button></a><button onclick="staff_delete('.$data->id.')" style="width:70px;" class="btn btn-danger btn-sm">Hapus</button></center>';  
            })
        ->rawColumns(['start_date','action'])
        ->make(true);
    }

    public function store(StoreStaffRequest $request) : JsonResponse
    {
        try {
            $data = $request->only(['fullname','address','branch_id','position_id','phone','username','start_date','pin','is_active','email', 'clock_in', 'clock_out', 'holiday']);
            $data['roles'] = $this->role('staff')->id;
            $data['role_code'] = $this->role('staff')->code_name;
            $data['password'] = bcrypt($request->password);
            $data['token'] = Str::random(36);
            $data['created'] = time();
            $data['uuid'] = Str::uuid();
            $data['is_upgraded'] = 1;
            $data['clock_in'] = $request->clock_in;
            $data['clock_out'] = $request->clock_out;
            $data['holiday'] = isset($data['holiday']) ? json_encode($data['holiday']) : '';
            $staffCreated = Account::create($data);
            
            return response()->json(['success' => true,'message' => 'Berhasil menambahkan staff','staff'=> $staffCreated],200);
        } catch (Exception $e) {
            Log::error('create staff error', [$e->getMessage()]);
            throw new HttpResponseException(
                response()->json(['success' => false, 'message' => $e->getMessage()],500)
            );
        }
    }
    public function destroy(string $id) : JsonResponse
    {
        $penjualan = Penjualan::where('staff_id', $id)->count();
        if($penjualan > 0) {
            return response()->json(['success' => false, 'message' => 'Hapus Gagal, Staff ini sudah melakukan transaksi...!'],200);
        }

        Account::find($id)->delete();
        return response()->json(['success' => true, 'message' => 'Delete success'],200);
    }

    private function role(string $role) : Role
    {
        return Role::where('name',$role)->first();
    }
}

