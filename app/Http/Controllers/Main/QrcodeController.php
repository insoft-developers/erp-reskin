<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Milon\Barcode\DNS2D;
use App\Models\QrCode;
use App\Models\BusinessGroup;
use App\Models\Account;
use DB;


class QrcodeController extends Controller
{
    public function index()
    {

        $view = 'qr-code';
        $owner_id = Controller::get_owner_id(session('id'));
        $branch = DB::table('branches')->where('account_id', $owner_id)->get();
        $user = Account::where('id', $owner_id)->first();
        return view('main.qrcode.qrcode', compact('view', 'branch', 'user'));
    }
    public function ajax_get_data(Request $request)
    {
        $user = Account::where('id', session('id'))->first();
        $user_id = Controller::get_owner_id(session('id'));
        $barcode = new DNS2D();

        if ($request->ajax()) {
            $data = QrCode::where('user_id', $user_id);
            if (session('role') != 'general_member') {
                $data = $data->where('branch_id', $user->branch_id);
            }
            $data = $data->leftJoin('branches', 'branches.id', '=', 'qr_codes.branch_id')
                ->select('qr_codes.*', 'branches.name')->whereNull('deleted_at')->get();
            $data = $data->map(function ($item) use ($barcode) {
                $item->qr_code = $barcode->getBarcodeHTML($item->qr_link, 'QRCODE', 5, 5);
                return $item;
            });
            $data = $data->map(function ($item) use ($barcode) {
                $item->qr_image = $barcode->getBarcodePNG($item->qr_link, 'QRCODE', 5, 5);
                return $item;
            });
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function add(Request $request)
    {
        if (!$request->isMethod('post')) {
            return redirect()->back();
        }

        $jumlah = (int)$request->jumlah;
        $branch = $request->branch;

        if (!$request->branch) {
            return [
                "success" => false,
                "title" => 'Gagal',
                "message" => 'Silahkan Pilih Cabang Terlebih Dahulu',
                "icon"  => 'error',
            ];
        }

        $owner_id = Controller::get_owner_id(session('id'));
        $url = url('');
        $user_id = $owner_id;
        $username = $request->username;
        $get_data = QrCode::where('user_id', $user_id)->get();
        $existing = count($get_data);

        // Cek jika jumlah existing + jumlah yang ingin ditambahkan lebih dari 50
        if ($existing + $jumlah > 50) {
            return [
                "success" => false,
                "title" => 'Gagal',
                "message" => 'Jumlah QR Meja tidak boleh lebih dari 50',
                "icon"  => 'error',
            ];
        }

        // Hitung sisa yang bisa ditambahkan
        $sisa = 50 - $existing;
        $jumlah = min($jumlah, $sisa); // Ambil jumlah yang bisa ditambahkan

        for ($n = 0; $n < $jumlah; $n++) {
            $meja = $existing + $n + 1;
            $no = $user_id . date('ymd') . ($existing + $n + 1);
            $link = $url . '/' . $username . '/' . $no;
            $data = [
                'user_id' => $user_id,
                'no_meja' => $meja,
                'qr_id'   => $no,
                'qr_link' => $link,
                'availability' => 'Available',
                'branch_id' => $branch
            ];
            $save = QrCode::create($data);

            if ($save) {
                $success = true;
                $title = "Berhasil";
                $message = "Data QR Meja Berhasil Dibuat";
                $icon = "success";
            } else {
                $success = false;
                $title = "Gagal!";
                $message = "Data QR Meja Gagal Dibuat";
                $icon = "error";
            }
        }

        return [
            "success" => $success,
            "title" => $title,
            "message" => $message,
            "icon"  => $icon
        ];
    }

    public function delete(Request $request)
    {
        $ids = $request->key;

        try {
            $query = QrCode::query();
            if (is_array($ids)) {
                $query->whereIn('id', $ids);
            } else {
                $query->where('id', $ids);
            }
            $delete = $query->delete();
            if ($delete) {
                $response = ['title' => 'Sukses', 'text' => 'Data Berhasil Dihapus', 'icon' => 'success'];
            }
        } catch (\Throwable $th) {
            $response = ['title' => 'Gagal', 'text' => 'Data Gagal Dihapus', 'icon' => 'error'];
        }

        return response()->json($response);
    }

    public function qrcode_view()
    {
        $user_id = session()->get('id');
        $data = DB::table('qrcode_meja')->where('user_id', $user_id)->first();
        $qrcode = null;
        if ($data) {
            $qrcode = json_decode($data->qr_meja, true);
        }
        return view('main.qrcode_view', compact('data', 'qrcode'));
    }
    public function edit(Request $request)
    {
        $id = $request->id;
        $meja = $request->nomor;
        $avail = $request->availability;
        $data = QrCode::where('id', $id)->first();
        if ($data) {
            $data->no_meja = $meja;
            $data->availability = $avail;
            $update = $data->save();
            if ($update) {
                return [
                    "success" => true,
                    "title" => "Sukses",
                    "message" => "Data berhasil diubah",
                    "icon" => "success"
                ];
            } else {
                return [
                    "success" => false,
                    "title" => "Kesalahan",
                    "message" => "Data gagal diubah",
                    "icon"  => "error"
                ];
            }
        } else {
            return 'Data Not Found';
        }
    }
    public function set_availability(Request $request)
    {
        $ids = $request->key;
        $status = $request->status;

        try {
            $query = QrCode::query();
            if (is_array($ids)) {
                $query->whereIn('id', $ids);
            } else {
                $query->where('id', $ids);
            }
            $update = $query->update(["availability" => $status]);
            if ($update) {
                $response = ['title' => 'Sukses', 'text' => 'Data Berhasil Diubah ke ' . $status, 'icon' => 'success'];
            }
        } catch (\Throwable $th) {
            $response = ['title' => 'Gagal', 'text' => 'Data Gagal Diubah', 'icon' => 'error'];
        }
        return response()->json($response);
    }
    // Print Page
    public function print_qr_code()
    {
        $view = 'print-qr-code';
        $barcode = new DNS2D();
        $company = BusinessGroup::where('user_id', session('id'))->first();
        $qrcode = QrCode::where('user_id', session('id'))->get();
        $qrcode = $qrcode->map(function ($item) use ($barcode) {
            $item->qr_image = $barcode->getBarcodePNG($item->qr_link, 'QRCODE', 3, 3);
            return $item;
        });
        return view('main.qrcode.qrcode_print', compact('view', 'company', 'qrcode'));
    }
}
