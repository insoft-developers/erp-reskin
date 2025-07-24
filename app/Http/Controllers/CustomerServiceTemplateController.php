<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerServiceTemplateController extends Controller
{
    public function index() {
        $data = DB::table('md_message_templates');

        if (isset($request->search)) {
            $data = $data->where('title', 'LIKE', '%'. $request->search .'%');
        }

        $data = $data->limit(25)->paginate();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
