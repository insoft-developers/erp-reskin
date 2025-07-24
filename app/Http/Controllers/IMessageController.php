<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IMessageController extends Controller
{
    private $host = 'https://imessage.id/api/randu';
    public function __construct()
    {
        //
    }

    public function status(Request $request)
    {
        $params = $request->all();
        $cek = Http::get($this->host . '/status', $params);
        if ($cek->successful()) {
            return response()->json($cek->json());
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Please check later'
            ], 500);
        }
    }

    public function getQr(Request $request, $id)
    {
        $cek = Http::post($this->host . '/create-session/' . $id);
        if ($cek->successful()) {
            return response()->json($cek->json());
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Please check later'
            ], 500);
        }
    }

    public function checkSession(Request $request, $id)
    {
        $cek = Http::post($this->host . '/check-session/' . $id);
        if ($cek->successful()) {
            return response()->json($cek->json());
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Please check later',
                'detail' => $cek->json(),
            ], 500);
        }
    }

    public function logoutSession(Request $request, $id)
    {
        $cek = Http::post($this->host . '/logout-session/' . $id);
        if ($cek->successful()) {
            return response()->json($cek->json());
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Please check later',
                'detail' => $cek->json(),
            ], 500);
        }
    }

    public function addCustomerService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'user_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $cek = Http::post($this->host . '/device', [
                'name' => $request->name,
                'user_id' => $request->user_id
            ]);
            if ($cek->successful()) {
                $response = $cek->json();

                DB::table('md_customer_services')->insert([
                    'user_id' => $this->get_owner_id(session('id')),
                    'name' => $request->name,
                    'uuid' => $response['uuid'],
                    'scan_url' => $response['redirect'],
                    'is_active' => -1,
                    'created_at' => now()
                ]);

                DB::commit();
                return response()->json($cek->json());
            } else {
                return response()->json($cek->json(), 500);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function deleteCustomerService(Request $request)
    {
        DB::beginTransaction();
        try {
            $counterSuccessDelete = 0;
            foreach ($request->cs_ids as $cs_id) {
                $cs = DB::table('md_customer_services')->whereId($cs_id)->first();
                if ($cs) {
                    $cek = Http::delete($this->host . '/device/' . $cs->uuid);
                    if ($cek->successful()) {
                        $counterSuccessDelete++;
                        DB::table('md_customer_services')->whereId($cs_id)->delete();
                    }
                }
            }

            if ($counterSuccessDelete) {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Customer service successfully to delete'
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Nothing to delete'
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateCustomerService(Request $request)
    {
        DB::beginTransaction();
        try {
            $counterSuccess = 0;
            foreach ($request->cs_ids as $cs_id) {
                $cs = DB::table('md_customer_services')->whereId($cs_id)->first();
                if ($cs) {
                    $counterSuccess++;
                    DB::table('md_customer_services')->whereId($cs_id)->update([
                        'is_active' => $request->is_active
                    ]);
                }
            }

            if ($counterSuccess) {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Customer service successfully to updated'
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Nothing to update'
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function deviceConnectedInfo(Request $request)
    {
        DB::table('md_customer_services')->whereUuid($request->device_info['uuid'])->update([
            'phone' => $request->device_info['phone'],
            'is_active' => 1,
            'updated_at' => now()
        ]);

        $cek = Http::post($this->host . '/app', [
            'user_id' => $request->user_info['id'],
            'device_id' => $request->device_info['device_id'],
            'name' => $request->device_info['name'],
        ]);
        if ($cek->successful()) {
            $response = $cek->json();
            DB::table('md_customer_services')->whereUuid($request->device_info['uuid'])->update([
                'appkey' => $response['appkey'],
                'updated_at' => now()
            ]);
        }
    }

    public function deviceStore(Request $request)
    {
        DB::beginTransaction(); // Memulai transaksi
        try {
            $user = DB::table('ml_accounts')->where('email', $request->email)->where('phone', $request->phone)->first();
            if ($user) {
                DB::table('md_customer_services')->insert([
                    'user_id' => $this->get_owner_id($user->id),
                    'name' => $request->name,
                    'uuid' => $request->uuid,
                    'scan_url' => $request->redirect,
                    'is_active' => -1,
                    'created_at' => now()
                ]);
            }
            DB::commit(); // Menyimpan perubahan jika berhasil
            return response()->json([
                'status' => true,
                'message' => 'Customer service successfully added',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack(); // Mengembalikan perubahan jika terjadi kesalahan
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function deviceUpdate(Request $request)
    {
        DB::table('md_customer_services')->whereUuid($request->uuid)->update([
            'name' => $request->name,
        ]);
    }

    public function deviceRemove(Request $request)
    {
        DB::table('md_customer_services')->whereUuid($request->uuid)->delete();
    }

    public function deviceLogout(Request $request, $id)
    {
        DB::table('md_customer_services')->whereUuid($id)->update([
            'is_active' => -1,
            'phone' => null,
        ]);
    }

    public function accountStatus(Request $request)
    {
        $user = DB::table('ml_accounts')->whereEmail($request->email)->wherePhone($request->phone)->first();

        if ($user) {
            return response()->json([
                'status' => true,
                'data' => (object) $user
            ]);
        } else {
            return response()->json([
                'status' => false,
            ]);
        }
    }
}
