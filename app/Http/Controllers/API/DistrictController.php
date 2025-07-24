<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictController extends Controller
{
    public function getDistrictLists(Request $request)
    {
        $limit = $request->query('limit');
        $keyword = $request->query('keyword');
        $data = DB::table('districts')
                ->join('regencies', 'regencies.id', '=', 'districts.regency_id')
			    ->join('provinces', 'provinces.id', '=', 'regencies.province_id')
                ->where('districts.name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('regencies.name','LIKE', '%' . $keyword . '%')
                ->orWhere('provinces.name','LIKE', '%' . $keyword . '%')
                ->select('districts.id',DB::raw("CONCAT(districts.name, ',', regencies.name, ',' , provinces.name) as name"))
                ->limit($limit)
                ->offset(0)
                ->get();
        
        return response()->json(['success' => true, 'data' => $data],200);
    }
}
