<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SeederController extends Controller
{
    public function runSeeder($seeder)
    {
        try {
            if (!class_exists("Database\\Seeders\\$seeder")) {
                return response()->json(['status' => 'error', 'message' => 'Seeder class does not exist'], 404);
            }

            Artisan::call('db:seed', ['--class' => "Database\\Seeders\\$seeder", '--force' => true]);
            return response()->json(['status' => 'success', 'message' => 'Seeder ran successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function runViewClear()
    {
        try {
            Artisan::call('view:clear');
            return response()->json(['status' => 'success', 'message' => 'View:clear ran successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

