<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB;

class ReservasiController extends Controller
{
    public function index()
    {
        $view = 'reservasi';
        $data = DB::table('reservasi')
            ->select('reservasi.*', 'md_customers.name', 'md_customers.phone')
            ->join('md_customers', 'md_customers.id', '=', 'reservasi.pelanggan_id')
            ->where('reservasi.user_id', session('id'))
            ->get();
        return view('main.reservasi', compact('view', 'data'));
    }

    public function get_meja()
    {
        $user_id = session('id');
        $data = DB::table('qrcode_table')->where('user_id', $user_id)->first();
        return response()->json($data);
    }

    public function get_pelanggan(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $data = DB::table('md_customers')->where('id', $id)->first();
        } else {
            $user_id = session('id');
            $data = DB::table('md_customers')->where('user_id', $user_id)->get();
        }
        return response()->json($data);
    }

    public function get_lokasi()
    {
        $district = DB::table('districts')
            ->select('districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')
            ->join('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->join('provinces', 'provinces.id', '=', 'regencies.province_id')
            ->get();
        return response()->json($district);
    }

    public function get_reservasi(Request $request)
    {
        $id = $request->id;
        $data = DB::table('reservasi')
            ->select('reservasi.*', 'md_customers.name', 'md_customers.phone')
            ->join('md_customers', 'md_customers.id', '=', 'reservasi.pelanggan_id')
            ->where('reservasi.id', $id)
            ->first();;
        return response()->json($data);
    }

    public function add(Request $request)
    {
        $nama_pelanggan = $request->nama_pelanggan;
        $alamat = $request->alamat;
        $lokasi = $request->lokasi;
        $kelurahan = $request->kelurahan;
        $nohp = $request->no_hp;
        $tgl = $request->tgl;
        $jam = $request->jam;
        $jumlah = $request->jml_orang;
        $no_meja = $request->no_meja;
        $id_qr = $request->id_qr;

        $user_id = session('id');


        $pelanggan = [
            'name' => $nama_pelanggan,
            'phone' => $nohp,
            'kecamatan' => $lokasi,
            'kelurahan' => $kelurahan,
            'alamat' => $alamat,
            'created' => date('Y-m-d H:i:s'),
            'user_id' => $user_id
        ];

        $data = [
            'qrcode_id' => $id_qr,
            'nomor_meja' => $no_meja,
            'tgl_reservasi' => $tgl,
            'jam_reservasi' => $jam,
            'jumlah' => $jumlah,
            'user_id' => $user_id,
            'status' => 1
        ];

        $data_meja = DB::table('qrcode_table')->where('id', $id_qr)->first();
        $json_meja = json_decode($data_meja->qr_meja, true);
        $json_meja[$no_meja] = ['status' => 1];



        if ($id_qr == null) {
            $message = ['title' => 'Error', 'text' => 'Nomor Meja Harus Diisi', 'icon' => 'error'];
            return redirect()->back()->with($message);
        }

        try {
            if (is_numeric($nama_pelanggan)) {
                $data_pelanggan = DB::table('md_customers')->where('id', $nama_pelanggan)->first();
                if ($data_pelanggan == null) {
                    $message = ['title' => 'Error', 'text' => 'Data Pelanggan Tidak Ditemukan', 'icon' => 'error'];
                    return redirect()->back()->with($message);
                }
                $data['pelanggan_id'] = $nama_pelanggan;
            } else {
                $idPelanggan = DB::table('md_customers')->insertGetId($pelanggan);
                $data['pelanggan_id'] = $idPelanggan;
            }

            DB::table('qrcode_table')->where('id', $id_qr)->update(['qr_meja' => json_encode($json_meja)]);

            DB::table('reservasi')->insert($data);

            $message = ['title' => 'Berhasil', 'text' => 'Data Berhasil Ditambahkan', 'icon' => 'success'];
        } catch (\Throwable $th) {
            $message = ['title' => 'Error', 'text' => $th->getMessage(), 'icon' => 'error'];
        }
        return redirect()->back()->with($message);
    }

    public function edit_reservasi(Request $request)
    {
        $id_reservasi = $request->id;
        $id_qr = $request->id_qr;
        $tgl = $request->tgl;
        $jam = $request->jam;
        $jumlah = $request->jumlah;
        $no_meja = $request->no_meja;

        $get_data_qr = DB::table('qrcode_table')->where('id', $id_qr)->first();
        $datajson_meja = json_decode($get_data_qr->qr_meja, true);
        $get_data_reservasi = DB::table('reservasi')->where('id', $id_reservasi)->first();

        $datajson_meja[$get_data_reservasi->nomor_meja] = ['status' => 0];
        $datajson_meja[$no_meja] = ['status' => 1];

        $data = [
            'nomor_meja' => $no_meja,
            'tgl_reservasi' => $tgl,
            'jam_reservasi' => $jam,
            'jumlah' => $jumlah
        ];

        $data_meja = [
            'qr_meja' => json_encode($datajson_meja)
        ];

        try {
            DB::table('reservasi')->where('id', $id_reservasi)->update($data);
            DB::table('qrcode_table')->where('id', $id_qr)->update($data_meja);
            $message = ['title' => 'Berhasil', 'text' => 'Data Berhasil Diupdate', 'icon' => 'success'];
        } catch (\Throwable $th) {
            $message = ['title' => 'Error', 'text' => $th->getMessage(), 'icon' => 'error'];
        }
        return redirect()->back()->with($message);
    }

    public function updata_status_meja(Request $request)
    {
        $status = $request->status;
        $id_qr = $request->id_qr;
        $user_id = session('id');
        $data = [];

        foreach ($status as $row) {
            $explode_status = explode("_", $row);
            $no_meja = $explode_status[0];
            $status_meja = $explode_status[1];
            $data[$no_meja] = ['status' => $status_meja];

            $reservasi = DB::table('reservasi')->where('user_id', $user_id)->where('qrcode_id', $id_qr)->where('nomor_meja', $no_meja);
            if ($reservasi->first() != null) {
                $reservasi->update(['status' => $status_meja]);
            }
        }

        $update_data = json_encode($data);

        try {
            DB::table('qrcode_table')->where('id', $id_qr)->update(['qr_meja' => $update_data]);

            $message = ['title' => 'Berhasil', 'text' => 'Status meja berhasil diubah', 'icon' => 'success'];
        } catch (\Throwable $th) {
            $message = ['title' => 'Error', 'text' => 'Data gagal ditambahkan', 'icon' => 'error'];
        }

        return redirect()->back()->with($message);
    }

    public function hapus(Request $request)
    {
        $id_reservasi = $request->id;
        $idqr = $request->idqr;
        $no_meja = $request->no_meja;

        $getdata_qr = DB::table('qrcode_table')->where('id', $idqr)->first();
        $datajson_meja = json_decode($getdata_qr->qr_meja, true);

        $datajson_meja[$no_meja] = ['status' => 0];

        $data_meja = [
            'qr_meja' => json_encode($datajson_meja)
        ];

        try {
            DB::table('reservasi')->where('id', $id_reservasi)->delete();
            DB::table('qrcode_table')->where('id', $idqr)->update($data_meja);
            $message = ['title' => 'Berhasil', 'text' => 'Data Berhasil Dihapus', 'icon' => 'success'];
        } catch (\Throwable $th) {
            $message = ['title' => 'Gagal', 'text' => 'Data Gagal Dihapus', 'icon' => 'error'];
        }
        return response()->json($message);
    }
}
