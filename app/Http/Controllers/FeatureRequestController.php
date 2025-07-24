<?php

namespace App\Http\Controllers;

use App\Models\FeatureRequest;
use App\Models\FeatureRequestCategories;
use App\Models\FeatureRequestImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FeatureRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['view'] = 'Feature';
        return view('main.feature_request.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = DB::table('ml_accounts')->where('id', session('id'))->first();
        if ($user->is_upgraded == 0) {
            return response()->json([
                'message' => 'Permintaan Fitur hanya tersedia untuk pengguna Randu Premium. Silakan upgrade ke Premium jika kamu memiliki permintaan fitur.',
                'redirect' => true,
            ], 403);
        }


        $expired_at = $user->upgrade_expiry; // contoh output 2026-01-01 09:16:30
        if (now()->diffInDays($expired_at) <= 90) {
            return response()->json([
                'message' => 'Permintaan Fitur hanya tersedia hanya untuk pengguna Randu Premium tahunan.',
                'redirect' => true,
            ], 403);
        }

        // Validasi input
        $request->validate([
            'judulFitur' => 'required|string|max:255',
            'detail' => 'required|string',
            'kategori' => 'required|integer',
            'foto' => 'array',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi gambar
        ]);

        // Simpan data ke tabel feature_request
        $featureRequest = FeatureRequest::create([
            'title' => $request->judulFitur,
            'detail' => $request->detail,
            'category_id' => $request->kategori,
            'user_id' => session('id'),
        ]);

        // Simpan gambar ke folder dan ke tabel feature_request_image
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $file->store('images/feature_request', 'public'); // Simpan gambar

                // Simpan path gambar ke tabel feature_request_image
                FeatureRequestImages::create([
                    'request_id' => $featureRequest->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Saran fitur telah berhasil di kirim', 'data' => $featureRequest], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $featureRequest = FeatureRequest::find($id);

        if ($featureRequest) {
            // Hapus gambar terkait jika ada
            $images = FeatureRequestImages::where('request_id', $id)->get();
            foreach ($images as $image) {
                // Hapus file gambar dari penyimpanan
                \Storage::disk('public')->delete($image->image_path);
            }
            // Hapus entri gambar dari database
            FeatureRequestImages::where('request_id', $id)->delete();
            // Hapus permintaan fitur
            $featureRequest->delete();

            return response()->json(['message' => 'Permintaan fitur berhasil dihapus.'], 200);
        }

        return response()->json(['message' => 'Permintaan fitur tidak ditemukan.'], 404);
    }

    public function getData(Request $request)
    {
        $query = $request->input('q');

        $categories = FeatureRequestCategories::when($query, function ($queryBuilder) use ($query) {
            return $queryBuilder->where('name', 'like', "%{$query}%");
        })
            ->paginate(10);

        return response()->json($categories);
    }

    public function getDataTable(Request $request)
    {
        $id = $request->session()->get('id');
        $userId = $this->get_owner_id($id);
        $query = FeatureRequest::query();

        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            $query->where(function ($query) use ($keyword) {
                $query
                    ->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('detail', 'like', '%' . $keyword . '%');
            });
        }


        $query->orderBy('created_at', 'desc');

        $query->where('user_id', $userId);
        return DataTables::of($query)
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('category', function ($row) {
                return $row->category->name;
            })
            ->addColumn('images', function ($row) {
                $images = FeatureRequestImages::where('request_id', $row->id)->get();
                $html = '<div class="flex align-items-center gap-2">';
                foreach ($images as $image) {
                    $html .= '<div><a href="' . asset('storage/' . $image->image_path) . '" target="_blank"><img src="' . asset('storage/' . $image->image_path) . '" alt="Image" style="height: 65px" /></a></div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('created_at', function ($row) { // Tambahkan kolom created_at
                return $row->created_at->format('d-m-Y H:i:s'); // Format tanggal sesuai kebutuhan
            })
            ->addColumn('detail', function ($row) {
                return \Str::limit($row->detail, 200) . '...';
            })
            ->addColumn('status', function ($row) {
                return $row->status === 0 ? 'Pending - Masih Dipertimbangkan' : ($row->status === 1 ? 'Approve - Dalam Antrian Pengerjaan' : 'Done - Fitur Sudah Tersedia');
            })
            ->addColumn('aksi', function ($row) {
                return '<button class="btn btn-danger" onclick="deleteFeatureRequest(' . $row->id . ')">Hapus</button>'; // Tombol hapus
            })
            ->escapeColumns([])
            ->make(true);
    }
}
