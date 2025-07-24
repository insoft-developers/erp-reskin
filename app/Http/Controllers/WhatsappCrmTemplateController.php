<?php

namespace App\Http\Controllers;

use App\Models\WhatsappCrmTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class WhatsappCrmTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $owner_id = $this->get_owner_id(session('id'));
            $template = WhatsappCrmTemplate::where('owner_id', $owner_id)->first();

            return response()->json([
                'success' => true,
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data template: ' . $e->getMessage()
            ], 500);
        }
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
        try {
            $request->validate([
                'invoice_pending' => 'required|string',
                'invoice_payment_complete' => 'required|string'
            ], [
                'invoice_pending.required' => 'Template invoice pending payment harus diisi',
                'invoice_payment_complete.required' => 'Template invoice payment complete harus diisi'
            ]);

            $owner_id = $this->get_owner_id(session('id'));

            $template_data = [
                'invoice_pending' => $request->invoice_pending,
                'invoice_payment_complete' => $request->invoice_payment_complete
            ];

            // Cek apakah sudah ada template untuk owner_id ini
            $existing_template = WhatsappCrmTemplate::where('owner_id', $owner_id)->first();

            if ($existing_template) {
                // Update existing template
                $existing_template->update([
                    'template_data' => $template_data
                ]);
                $message = 'Template berhasil diperbarui';
            } else {
                // Create new template
                WhatsappCrmTemplate::create([
                    'owner_id' => $owner_id,
                    'template_data' => $template_data
                ]);
                $message = 'Template berhasil disimpan';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WhatsappCrmTemplate $whatsappCrmTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WhatsappCrmTemplate $whatsappCrmTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WhatsappCrmTemplate $whatsappCrmTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhatsappCrmTemplate $whatsappCrmTemplate)
    {
        //
    }
}
