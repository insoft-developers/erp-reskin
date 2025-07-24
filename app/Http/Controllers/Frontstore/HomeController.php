<?php

namespace App\Http\Controllers\Frontstore;

use App\Http\Controllers\Controller;
use App\Models\Category;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {

        return view('front_store.home.index');
        // echo "tes" . $user;
    }

    public function kategori()
    {
        $title = "Kategori";
        $kategori = Category::where('user_id', 54)->get();
        return view('front_store.pages.kategori', compact('title', 'kategori'));
    }
}
