<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Mail\ReceiptMail;
use App\Models\BusinessGroup;
use App\Models\MlAccount;
use App\Models\MlSettingUser;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDF;

class PosController extends Controller
{
    public function index()
    {
        $view = 'pos-index';
        $category_products = DB::table('md_product_category')->where('user_id', session('id'))->get();
        $products = DB::table('md_products')->where('user_id', session('id'))->get();
        $varian_product = DB::table('md_products')->where('user_id', session('id'))
            ->join('md_product_varians', 'md_products.id', '=', 'md_product_varians.product_id')
            ->select('md_products.*', 'md_product_varians.varian_group', 'md_product_varians.varian_name', 'md_product_varians.varian_price')
            ->get();
        $tables = DB::table('qr_codes')->where('user_id', session('id'))->get();
        $user = MlAccount::whereId(session('id'))->first();
        $branch_id = $user->branch_id ?? null;
        $staff_id = $user->branch_id ? $user->id ?? null : null;

        $tax = 0;
        if ($user->role_code === 'staff') {
            $tax = DB::table('ml_accounts')->whereId($user->branch_id)->first()->tax;
        }

        return view('main.pos_index', compact('view', 'tax', 'category_products', 'products', 'tables', 'varian_product', 'user', 'branch_id', 'staff_id'));
    }

    public function metode_pembayaran()
    {
        $view = 'metode-pembayaran';
        return view('main.metode_pembayaran', compact('view'));
    }

    public function selesai_pembayaran(Request $request)
    {
        $reference = $request->reference;

        $data = Penjualan::whereReference($reference)->first();
        // dd($data);

        if (!$data) {
            return redirect('/pos/index');
        }

        $view = 'terima-kasih';
        return view('main.selesai_pembayaran', compact('view', 'reference', 'data'));
    }

    public function printReceipt(Request $request)
    {
        $with_check = $request->with_check ?? 'false';
        $pen = Penjualan::with(['flag'])->where('reference', $request->reference)->first();
        $bussines = BusinessGroup::where('user_id', $pen->user_id)->first();
        $setting = MlSettingUser::where('user_id', $pen->user_id)->first();

        $payment_method = '';
        switch ($pen->payment_method) {
            case 'randu-wallet':
                $payment_method = 'Randu Wallet';
                break;
            case 'kas':
                $payment_method = 'Kas / Tunai Kas';
                break;
            case 'bank-mandiri':
                $payment_method = 'Transfer Bank Mandiri';
                break;
            case 'bank-bri':
                $payment_method = 'Transfer Bank BRI';
                break;
            case 'bank-bni':
                $payment_method = 'Transfer Bank BNI';
                break;
            case 'bank-bca':
                $payment_method = 'Transfer Bank BCA';
                break;
            case 'piutang-cod':
                $payment_method = 'Piutang COD';
                break;
            case 'piutang-marketplace':
                $payment_method = 'Piutang Marketplace';
                break;
            case 'piutang-usaha':
                $payment_method = 'Piutang Usaha';
                break;
            default:
                $payment_method = 'Metode Lainnya';
                break;
        }

        $product_counter = 0;
        foreach ($pen->products as $pro) {
            $product_counter += $pro->quantity;
        }

        // Hitung pembulatan: total - (subtotal - diskon + shipping + tax)
        $pembulatan = $pen->paid - ($pen->order_total - $pen->diskon + $pen->shipping + $pen->tax);

        $data = [
            'with_check' => json_decode($with_check),
            'bussines_name' => $bussines->branch_name ?? 'Randu',
            'bussines_address' => $bussines->business_address ?? '',
            'no_nota' => $request->reference,
            'flag' => $pen->flag ? $pen->flag->flag : '',
            'waktu_pesanan' => Carbon::parse($pen->created)->format('d F Y, H:i:s'),
            'branch' => $pen->branch ? $pen->branch->name : '',
            'kasir' => $pen->staff_id ? $pen->staff->fullname : $pen->user->fullname,
            'meja' => $pen->qr_codes_id ? $pen->desk->no_meja : '',
            'nama_konsumen' => $pen->customer_id ? $pen->customer->name : $pen->cust_name,
            'pembayaran' => $payment_method,
            'subtotal' => $pen->order_total,
            'diskon' => $pen->diskon,
            'shipping' => $pen->shipping,
            'tax' => $pen->tax,
            'pembulatan' => $pembulatan,
            'total' => $pen->paid,
            'total_bayar' => $pen->payment_amount ?? 0,
            'kembalian' => ($pen->payment_amount ?? 0) - $pen->paid,
            'total_product' => $product_counter,
            'footer' => $setting->printer_custom_footer ?? '',
            'products' => $pen->products,
        ];

        // Menghitung tinggi kertas dinamis berdasarkan jumlah produk
        $baseHeight = 330; // Tinggi dasar
        $additionalHeightPerItem = 20; // Tambahkan tinggi per item
        $totalHeight = $baseHeight + (count($data['products']) * $additionalHeightPerItem);

        // Membuat PDF dengan tinggi dinamis
        $pdf = PDF::loadView($with_check ? 'pdf.receipt' : 'pdf.receipt', $data)->setPaper([0, 0, 165, $totalHeight], 'portrait');

        return $pdf->stream('receipt.pdf');
    }

    public function sendReceipt(Request $request)
    {
        $request->validate([
            'inputText' => 'required|email',
            'reference' => 'required'
        ]);

        $email = $request->input('inputText');
        $reference = $request->input('reference');

        $data = Penjualan::where('reference', $reference)->first();

        if (!$data) {
            return redirect('/pos/index');
        }

        $bussines = BusinessGroup::where('user_id', $data->user_id)->first();
        $setting = MlSettingUser::where('user_id', $data->user_id)->first();

        $payment_method = ''; // logic for payment method...

        $product_counter = 0;
        foreach ($data->products as $pro) {
            $product_counter += $pro->quantity;
        }

        $pdf_data = [
            'bussines_name' => $bussines->branch_name ?? 'Randu',
            'bussines_address' => $bussines->business_address ?? '',
            'no_nota' => $reference,
            'waktu_pesanan' => Carbon::parse($data->created)->format('d F Y, H:i:s'),
            'branch' => $data->branch ? $data->branch->name : '',
            'kasir' => $data->staff_id ? $data->staff->fullname : $data->user->fullname,
            'meja' => $data->qr_codes_id ? $data->desk->no_meja : '',
            'nama_konsumen' => $data->customer_id ? $data->customer->name : $data->cust_name,
            'pembayaran' => $payment_method,
            'subtotal' => $data->order_total,
            'diskon' => $data->diskon,
            'shipping' => $data->shipping,
            'tax' => $data->tax,
            'total' => $data->paid,
            'total_product' => $product_counter,
            'footer' => $setting->printer_custom_footer ?? '',
            'products' => $data->products,
        ];

        Mail::to($email)->send(new ReceiptMail($pdf_data, $reference));

        return redirect()->back()->with('success', 'Email dengan struk telah dikirim.');
    }
}
