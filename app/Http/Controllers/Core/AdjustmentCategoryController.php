<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\AdjustmentCategory;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdjustmentCategoryController extends Controller
{
    use JournalTrait;
    use CommonApiTrait;


    public function list(Request $request) {
        $query = AdjustmentCategory::where('account_id', $this->user_id_staff($request->userid));
        if( ! empty($request->kata_cari)) {
            $query->where('name', 'LIKE', "%".$request->kata_cari."%"); 
        }

        $query->orderBy('id','desc');
        $data = $query->get();

        return response()->json([
            "success" => true,
            "data" => $data
        ]);

    }


    public function store(Request $request) {
        $input = $request->all();

        $rules = array(
            "code" => "required",
            "name" => "required"
        );

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}', '"'];
            $html = '';
            $nomor = 0;
            foreach ($pesanarr as $p) {
                $nomor++;
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .=$nomor.'. '.str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        AdjustmentCategory::create($input);
        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }


    public function update(Request $request) {
        $input = $request->all();

        $rules = array(
            "code" => "required",
            "name" => "required"
        );

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}', '"'];
            $html = '';
            $nomor = 0;
            foreach ($pesanarr as $p) {
                $nomor++;
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .=$nomor.'. '.str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $query = AdjustmentCategory::findorFail($input['id']);
        $query->update($input);
        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }


    public function destroy(Request $request) {
        AdjustmentCategory::destroy($request->id);
        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }
}
