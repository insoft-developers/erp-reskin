<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileSettingController extends Controller
{
    public function branchName(Request $request) {
    	$input = $request->all();
    	$company = DB::table('business_groups')->where('user_id', $input['userid']);
	    if ($company->count() > 0) {
	        $cq = $company->first();
	        $cname = $cq->branch_name;
	    } else {
	        $cname = 'Randu Apps';
	    }

	    return response()->json([
	    	"success" => true,
	    	"data" => $cname
	    ]);
    }
}
