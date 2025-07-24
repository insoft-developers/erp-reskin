<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministrativeController extends Controller
{
    public function getProvince(Request $request)
    {
        $data = DB::table('ro_provinces');

        if (isset($request->search)) {
            $data = $data->where('province_name', 'LIKE', '%'. $request->search .'%');
        }

        $data = $data->limit(25)->paginate();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function getCity(Request $request)
    {
        $data = DB::table('ro_cities');

        if (isset($request->province_id)) {
            $data = $data->whereProvince_id($request->province_id);
        }

        if (isset($request->search)) {
            $data = $data->where('city_name', 'LIKE', '%'. $request->search .'%');
        }

        $data = $data->limit(25)->paginate();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function getDistrict(Request $request)
    {
        $data = DB::table('ro_subdistricts');

        if (isset($request->city_id)) {
            $data = $data->whereCity_id($request->city_id);
        }

        if (isset($request->search)) {
            $data = $data->where('subdistrict_name', 'LIKE', '%'. $request->search .'%');
        }

        $data = $data->limit(25)->paginate();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
