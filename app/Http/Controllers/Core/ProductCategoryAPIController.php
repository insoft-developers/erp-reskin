<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ProductCategoryAPIController extends Controller
{
    use JournalTrait;
    public function productCategoryList(Request $request) {
        $input = $request->all();
        $query = ProductCategory::where('user_id', $this->user_id_staff($input['userid']));
        if(! empty($input['kata_cari'])) {
            $query->where('name', 'LIKE', "%".$input['kata_cari']."%"); 
        }

        $query->orderBy('id', 'desc');
        $data = $query->get();
        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function productCategoryStore(Request $request) {
        $input = $request->all();
        $rules = array(
            "name" => "required",
            "code" => "required"
        );

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
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

        $input['user_id'] = $this->user_id_staff($input['user_id']);
        $input['created'] = date('Y-m-d H:i:s');
        $query = ProductCategory::create($input);

        return response()->json([
            "success" => true,
            "message" => "success",
            "id" => $query->id
        ]);
    }

    public function productCategoryUpload(Request $request) {
        $dir = 'images/category/';
        $image = $request->file('image');
        $ids = $request->ids;

        $path = storage_path('app/public/images/category');

        try {
            if($request->has('image')) {
                $manager = new ImageManager(new Driver());
                $file = $request->image;
                $filename = date('YmdHis').$file->getClientOriginalName();
                $img = $manager->read($file->path());
                $img->resize(500,500, function($constraint){
                    $constraint->aspectRatio();
                })->save($path.'/'.$filename);
        
            } else {
                return response()->json(['message'=> trans('/storage/test/'.'def.png.')],200);
            }
    
        
            $data = ProductCategory::findorFail((int)$ids);
            $data->image = $filename;
            $data->save();
    
            return response()->json(['message'=> trans('/storage/test/'.$filename)],200);

        }catch(\Exception $e) {
            return response()->json(['success'=> false, 'message'=> $e->getMessage()]);
        }

    }

    public function productCategoryUpdate(Request $request) {
        $input = $request->all();
        $rules = array(
            "name" => "required",
            "code" => "required"
        );

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
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


        $input['created'] = date('Y-m-d H:i:s');
        $input['user_id'] = $this->user_id_staff($input['user_id']);
        $query = ProductCategory::findorFail($input['id']);
        $query->update($input);

        return response()->json([
            "success" => true,
            "message" => "success",
        ]);
    }


    public function productCategoryDelete(Request $request) {
        // $data = ProductCategory::findorFail($request->id);
        // $dir = storage_path('app/public/images/category/'.$data->image);
        // if(file_exists($dir) && ! empty($data->image)) {
        //    unlink($dir);
        // } 

        ProductCategory::destroy($request->id);
        return response()->json([
            "success" => true,
            "message" => 'success',
        ]);


    }
}
