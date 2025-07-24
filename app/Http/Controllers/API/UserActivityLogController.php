<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLogs;
use Illuminate\Http\Request;

class UserActivityLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'page' => 'required|string',
        ]);

        try {
            return $this->atomic(function () use ($request) {
                $user = auth()->user();
    
                UserActivityLogs::create([
                    'user_id' => $user->id,
                    'page' => $request->page,
                    'is_mobile' => 1
                ]);
    
                return response()->json([
                    'status' => true,
                    'message' => 'Log Berhasil di Tambahkan',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
