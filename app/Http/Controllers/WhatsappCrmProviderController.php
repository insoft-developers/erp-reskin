<?php

namespace App\Http\Controllers;

use App\Models\WhatsappCrmProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class WhatsappCrmProviderController extends Controller
{
    public function index()
    {
        return view('main.whatsapp-crm-provider.index');
    }

    public function getData(Request $request)
    {
        $query = WhatsappCrmProvider::where('owner_id', $this->get_owner_id(session('id')));
        return DataTables::of($query)
            ->addColumn('api_key', function ($row) {
                return data_get($row->credentials, 'api_key', '');
            })
            ->addColumn('device_id', function ($row) {
                return data_get($row->credentials, 'device_id', '');
            })
            ->addColumn('active', function ($row) {
                return $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="d-flex gap-2">'
                    . '<button data-id="' . $row->id . '" class="btn btn-sm btn-primary editBtn">Edit</button>'
                    . '<button data-id="' . $row->id . '" class="btn btn-sm btn-danger deleteBtn">Delete</button>'
                    . '</div>';
            })
            ->rawColumns(['action', 'active'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'api_key' => 'required|string',
                'device_id' => 'required|string',
                'is_active' => 'nullable',
            ]);
            $credentials = ['api_key' => $validated['api_key'], 'device_id' => $validated['device_id']];
            $provider = WhatsappCrmProvider::create([
                'owner_id' => $this->get_owner_id(session('id')),
                'credentials' => $credentials,
                'is_active' => $request->has('is_active') && $request->input('is_active') === 'on',
            ]);

            return response()->json(['success' => true, 'data' => $provider]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $provider = WhatsappCrmProvider::findOrFail($id);
        return response()->json($provider);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'api_key' => 'required|string',
            'device_id' => 'required|string',
            'is_active' => 'nullable',
        ]);
        $provider = WhatsappCrmProvider::findOrFail($id);
        $credentials = ['api_key' => $validated['api_key'], 'device_id' => $validated['device_id']];
        $provider->update([
            'credentials' => $credentials,
            'is_active' => $request->has('is_active') && $request->input('is_active') === 'on',
        ]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $provider = WhatsappCrmProvider::findOrFail($id);
        $provider->delete();
        return response()->json(['success' => true]);
    }
}
