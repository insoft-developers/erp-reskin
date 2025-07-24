<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionStaffController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->position_id;
        $data = DB::table('staff_positions')
                ->where('staff_positions.position','like','%'. $keyword. '%')
                ->limit($request->limit)
                ->offset(0)
                ->get();
        return response()->json(['data' => $data],200);
    }
}
