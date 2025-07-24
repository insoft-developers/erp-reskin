<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\MdProduct;
use App\Models\Product;
use Error;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function api(Request $request)
    {
        $keyword = $request->input('keyword');
        $pages = $request->input('pages');

        $query = MdProduct::query();

        if ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $data = $query
            ->where('user_id', userOwnerId());
        
        if ($pages == 'all') {
            $data = $data->get();
        } else {
            $data = $data->limit($request->limit ?? 10)->get();
        }

        return response()->json($data);
    }

    public function apiById(Request $request, $id)
    {
        $product = MdProduct::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($product);
    }

    public function search(string $username,Request $request) : JsonResponse
    {
        try {
            $query = $request->get('search');
            $userID = Account::where('username',$username)->first()->id;
            $products = Product::where('name', 'LIKE', "%{$query}%")->where('store_displayed',1)->where('user_id',$userID)->limit(10)->get();
            return response()->json(['success'=> true, 'message'=> 'Get Products','data' => $products],200);
        } catch (Error $e) {
            Log::error('Error search product', $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Something wrong with search API'],500));
        }
        
    }
}
