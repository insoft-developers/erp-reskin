<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function getBranchLists(string $id,Request $request) : JsonResponse
    {
        $id = (int)$id;
        $limit = $request->query('limit');
        $keyword = $request->query('keyword');
        $data = DB::table('branches')->where('account_id',$id)
                ->whereNull('branches.deleted_at')
                ->where('branches.name', 'LIKE', '%' . $keyword . '%')
                ->select('branches.id','branches.name')
                ->limit($limit)
                ->offset(0)
                ->get();
        
        return response()->json(['success' => true, 'data' => $data],200);
    }

    public function getDistrictLists(string $id,Request $request) : JsonResponse
    {
        $id = (int)$id;
        $limit = $request->query('limit');
        $keyword = $request->query('keyword');
        $data = DB::table('branches')
                ->join('districts','districts.id','=','branches.district_id')
                ->join('regencies', 'regencies.id', '=', 'districts.regency_id')
			    ->join('provinces', 'provinces.id', '=', 'regencies.province_id')
                ->where('account_id',$id)
                ->whereNull('branches.deleted_at')
                ->where('districts.name', 'LIKE', '%' . $keyword . '%')
                ->select('districts.id','districts.name')
                ->limit($limit)
                ->offset(0)
                ->get();
        
        return response()->json(['success' => true, 'data' => $data],200);
    }
}
