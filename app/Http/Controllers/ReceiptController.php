<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class ReceiptController extends Controller
{
    public function printReceipt(Request $request)
    {
        $reference = $request->reference;

        $data = Penjualan::whereReference($reference)->first();
        $data = [
            'no_nota' => '019992999',
            'waktu_pesanan' => '12 July 2024, 20:20:56',
            'cabang' => 'Cabang Surabaya',
            'kasir' => 'Tarno Tambal Ban',
            'meja' => 'Meja 1 Lantai Atas',
            'nama_konsumen' => 'Sukini',
            'pembayaran' => 'Kas / Tunai Kas',
            'items' => [
                [
                    'name' => 'Cappuccino Italia',
                    'details' => 'Hot (1)',
                    'price' => 20000,
                ],
                [
                    'name' => 'Cappuccino Italia',
                    'details' => 'Ice (1)',
                    'price' => 25000,
                ],
                [
                    'name' => 'Nasi Goreng Ikan Asin',
                    'details' => '',
                    'price' => 30000,
                ],
                [
                    'name' => 'Thai Thea',
                    'details' => 'Boba (2) Less Sugar',
                    'price' => 20000,
                ],
                [
                    'name' => 'Rice Bowl',
                    'details' => 'Dori (1) Barbeque',
                    'price' => 10000,
                ],
            ],
            'subtotal' => 130000,
            'diskon' => 10000,
            'total' => 140000,
        ];

        // return view('receipt', $data);
        $pdf = PDF::loadView('receipt', $data)->setPaper([0, 0, 165, 340], 'portrait'); // 58mm x 120mm
        return $pdf->stream('receipt.pdf');
    }
}
