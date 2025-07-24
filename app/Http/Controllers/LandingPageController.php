<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageAsset;
use App\Models\LandingPageDetailBumpProduct;
use App\Models\MdProduct;
use App\Models\MlAccount;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Traits\CustomerServiceTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    use CustomerServiceTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request = app()->request;
        $id = $request->session()->get('id');
        $landing_pages = LandingPage::where("user_id", $id);

        if ($request->has('search')) {
            $search = $request->input('search');
            $landing_pages->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });;
        }

        $landing_pages = $landing_pages->paginate(10);

        $view = 'landing_page';
        return view('main.landing_page.index', compact('view', "landing_pages"));
    }

    public function getData(Request $request)
    {
        $userId = $request->session()->get('id');
        $landingPages = LandingPage::where('user_id', $userId)->get();

        return DataTables::of($landingPages)
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('product', function ($row) {
                return $row->product->name;
            })
            ->addColumn('title', function ($row) {
                return $row->title;
            })
            ->addColumn('slug', function ($row) {
                return $row->slug;
            })
            ->addColumn('bump_product', function ($row) {
                return count($row->bump_products) ? 'yes' : 'no';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="d-flex">';
                $btn = $btn . '<a href="' . route('landing-page.custom-show', ['id' => $row->id, 'slug' => $row->slug]) . '" target="_blank" class="edit btn btn-success btn-sm me-2">Lihat</a>';
                $btn = $btn . '<a href="' . route('landing-page.edit', ['landing_page' => $row->id]) . '" class="edit btn btn-warning btn-sm me-2">Ubah</a>';
                $btn = $btn . '<a href="' . route('landing-page.custom-destroy', ['id' => $row->id]) . '" class="delete btn btn-danger btn-sm">Hapus</a>';
                $btn = $btn . '</div>';
                return $btn;
            })
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $view = 'landing_page_create';
        $id = $request->session()->get('id');
        $user = MlAccount::with('information')->where('id', $id)->first();
        return view('main.landing_page.create', compact('view', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $all = $request->all();

        $this->validate($request, [
            "product_id" => "required",
            "title" => "required",
            "script_header" => "nullable",
            "script_header_payment_page" => "nullable",
            "script_header_wa_page" => "nullable",
            "with_customer_name" => "nullable",
            "with_customer_wa_number" => "nullable",
            "with_customer_email" => "nullable",
            "with_customer_full_address" => "nullable",
            "with_customer_proty" => "nullable",
            "bump_product_id" => "nullable",
            "bump_product_discount" => "nullable",
            "bump_product_custom_name" => "nullable|string|max:255",
            "bump_product_custom_photo" => "nullable|image|mimes:webp,jpg,jpeg,png",
            "bump_product_title" => "nullable|string|max:255",
            "bump_product_description" => "nullable|string",
            "text_submit_button" => "required|string",
        ]);

        $headerData = Arr::except($all, [
            'bump_product_id',
            'bump_product_discount',
            'bump_product_custom_name',
            'bump_product_custom_photo',
            'bump_product_title',
            'bump_product_description',
        ]);

        $headerData['user_id'] = $request->session()->get('id');

        DB::beginTransaction();
        try {

            if (!isset($request->with_customer_name)) {
                $headerData['with_customer_name'] = 0;
            }
            if (!isset($request->with_customer_wa_number)) {
                $headerData['with_customer_wa_number'] = 0;
            }
            if (!isset($request->with_customer_email)) {
                $headerData['with_customer_email'] = 0;
            }
            if (!isset($request->with_customer_full_address)) {
                $headerData['with_customer_full_address'] = 0;
            }
            if (!isset($request->with_customer_proty)) {
                $headerData['with_customer_proty'] = 0;
            }

            $header = LandingPage::create($headerData);

            $photo = $request->bump_product_custom_photo ? $this->savingImageToStorage($request->bump_product_custom_photo, 'bump_product') : null;

            if ($request->bump_product_id) {
                LandingPageDetailBumpProduct::create([
                    'landing_page_id' => $header->id,
                    'product_id' => isset($request->bump_product_id) ? $request->bump_product_id * 1 : null,
                    'custom_name' => $request->bump_product_custom_name,
                    'custom_photo' => $photo,
                    'discount' => $request->bump_product_discount,
                    'title' => $request->bump_product_title,
                    'description' => $request->bump_product_description,
                ]);
            }

            DB::commit();
            return redirect()->route('landing-page.edit', array('landing_page' => $header->id))->with('success', 'Data berhasil disimpan');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $slug)
    {
        function replaceBodyString($inputString, $replacement)
        {
            // Menggunakan preg_replace untuk menggantikan semua kejadian kata 'body' dengan kata pengganti, case-insensitive
            $pattern = '/body/i'; // 'i' flag untuk case-insensitive
            $outputString = preg_replace($pattern, $replacement, $inputString);
            return $outputString;
        }

        $data['district'] = DB::table('districts')
            ->select('districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')
            ->join('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->join('provinces', 'provinces.id', '=', 'regencies.province_id')
            ->get();

        $data['id'] = $id;
        $data['head'] = LandingPage::with('user', 'product', 'bump_products')->where('id', $id)->first();

        $data['head']->html_code = replaceBodyString($data['head']->html_code, 'div');

        return view('main.landing_page.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $view = 'landing_page_edit';
        $data = LandingPage::where('id', $id)->first();
        $bump = null;
        if (count($data->bump_products)) {
            $bump = $data->bump_products[0];
        }
        return view('main.landing_page.edit', compact('data', 'bump', 'view', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $all = $request->all();
        $this->validate($request, [
            "product_id" => "required",
            "title" => "required",
            "slug" => "required",
            "script_header" => "nullable",
            "script_header_payment_page" => "nullable",
            "script_header_wa_page" => "nullable",
            "with_customer_name" => "nullable",
            "with_customer_wa_number" => "nullable",
            "with_customer_email" => "nullable",
            "with_customer_full_address" => "nullable",
            "with_customer_proty" => "nullable",
            "bump_product_id" => "nullable",
            "bump_product_discount" => "nullable",
            "bump_product_custom_name" => "nullable|string|max:255",
            "bump_product_custom_photo" => "nullable|image|mimes:webp,jpg,jpeg,png",
            "bump_product_title" => "nullable|string|max:255",
            "bump_product_description" => "nullable|string",
            "text_submit_button" => "required|string",
        ]);

        $headerData = Arr::except($all, [
            'id',
            '_method',
            '_token',
            'bump_id',
            'bump_product_id',
            'bump_product_discount',
            'bump_product_custom_name',
            'bump_product_custom_photo',
            'bump_product_title',
            'bump_product_description',
        ]);

        $headerData['user_id'] = $request->session()->get('id');

        DB::beginTransaction();
        try {
            if (!isset($request->with_customer_name)) {
                $headerData['with_customer_name'] = 0;
            }
            if (!isset($request->with_customer_wa_number)) {
                $headerData['with_customer_wa_number'] = 0;
            }
            if (!isset($request->with_customer_email)) {
                $headerData['with_customer_email'] = 0;
            }
            if (!isset($request->with_customer_full_address)) {
                $headerData['with_customer_full_address'] = 0;
            }
            if (!isset($request->with_customer_proty)) {
                $headerData['with_customer_proty'] = 0;
            }

            LandingPage::where('id', $id)->update($headerData);

            $bump = LandingPageDetailBumpProduct::where('id', $request->bump_id)->first();
            $photo = null;
            if ($bump) {
                $photo = $bump->custom_photo;
                if ($request->bump_product_custom_photo) {
                    if ($bump->custom_photo) {
                        $this->removeImageFromStorage($bump->custom_photo);
                    }
                    $photo = $this->savingImageToStorage($request->bump_product_custom_photo, 'bump_product');
                }

                $willSaved = [
                    'landing_page_id' => $id,
                    'product_id' => isset($request->bump_product_id) ? $request->bump_product_id * 1 : null,
                    'custom_name' => $request->bump_product_custom_name,
                    'discount' => $request->bump_product_discount,
                    'title' => $request->bump_product_title,
                    'description' => $request->bump_product_description,
                    'custom_photo' => $photo
                ];

                if (isset($request->bump_product_id)) {
                    LandingPageDetailBumpProduct::where('id', $request->bump_id)->update($willSaved);
                }
            } else {
                $photo = $request->bump_product_custom_photo ? $this->savingImageToStorage($request->bump_product_custom_photo, 'bump_product') : null;

                if (isset($request->bump_product_id)) {
                    LandingPageDetailBumpProduct::create([
                        'landing_page_id' => $id,
                        'product_id' => isset($request->bump_product_id) ? $request->bump_product_id * 1 : null,
                        'custom_name' => $request->bump_product_custom_name,
                        'custom_photo' => $photo,
                        'discount' => $request->bump_product_discount,
                        'title' => $request->bump_product_title,
                        'description' => $request->bump_product_description,
                    ]);
                }
            }


            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $details = LandingPageDetailBumpProduct::where('landing_page_id', $id)->get();
        foreach ($details as $detail) {
            if ($detail->custom_photo) {
                $this->removeImageFromStorage($detail->custom_photo);
            }
        }
        LandingPageDetailBumpProduct::where('landing_page_id', $id)->delete();
        LandingPage::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Data has been deleted');
    }

    public function contentBuilder(Request $request, string $id)
    {
        $data['data'] = LandingPage::where('id', $id)->first();
        $data['id'] = $id;
        $data['assets'] = LandingPageAsset::where('user_id', $request->session()->get('id'))->pluck('path');
        return view('main.landing_page.content_builder', $data);
    }

    public function storeContent(Request $request, string $id)
    {
        LandingPage::where('id', $id)->update([
            'html_code' => $request->html,
            'css_code' => $request->css,
            'last_update_content_at' => now()
        ]);

        return response()->json(['message' => 'Data has been saved successfully', 'updated_at' => now()]);
    }

    public function uploadFile(Request $request)
    {
        // Validasi file yang diupload
        $request->validate([
            'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $uploadedFiles = $request->file('files');
        $fileUrls = [];

        foreach ($uploadedFiles as $file) {
            // Buat nama file yang unik
            $path = $this->savingImageToStorage($file, 'landing_page');
            LandingPageAsset::create([
                'user_id' => $request->session()->get('id'),
                'path' => $path,
                'size' => $file->getSize(),
            ]);
            $fileUrls[] = $path;
        }

        return response()->json([
            'data' => $fileUrls,
            'message' => 'Files uploaded successfully'
        ]);
    }

    public function removeFile(Request $request)
    {
        $path = $request->path;
        LandingPageAsset::where('user_id', $request->session()->get('id'))->where('path', $path)->delete();
        $this->removeImageFromStorage($path);

        return response()->json(['message' => 'File deleted successfully']);
    }

    public function checkout(Request $request, string $id)
    {
        $product = MdProduct::where('id', $request->product_id)->first();
        $landing = LandingPage::find($request->landing_id);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }
        $userId = $this->get_owner_id($request->user_id);
        DB::beginTransaction();
        try {
            $last = Penjualan::limit(1)->orderBy('id', 'DESC')->first();
            $lsatid = $last->id + 1;
            $currentDateTime = date('dm');

            $price = $landing->product->price;
            $detail_order = $landing->title ? "- {$landing->title} @1 {$this->formatRupiah($landing->product->price)}" : "- {$landing->product->name} @1 {$this->formatRupiah($landing->product->price)}";

            if (count($landing->bump_products)) {
                foreach ($landing->bump_products as $bump) {
                    $discount = $bump->discount ?? 0; // asumsikan discount dalam persentase, misalnya 10 untuk 10%
                    $bumpPrice = $bump->product->price;
                    $discountAmount = ($bumpPrice * $discount) / 100;
                    $finalBumpPrice = $bumpPrice - $discountAmount;

                    $price = $price + $finalBumpPrice;
                    $detail_order .= $bump->title ? "<br/> - {$bump->title} @1 {$this->formatRupiah($finalBumpPrice)}" : "<br/> - {$bump->product->name} @1 {$this->formatRupiah($finalBumpPrice)}";
                }
            }

            $str_random = Str::upper(Str::random(2));
            $refid = "LP" . $currentDateTime . '-' . $userId . '-' . $str_random . rand(10, 99);
            $need_save = [
                'id' => $lsatid,
                'reference' => $refid,
                'date' => now(),
                'cs_id' => $this->getCustomerServiceId($userId),
                'cust_name' => $request->cust_name ?? null,
                'cust_email' => $request->cust_email ?? null,
                'cust_phone' => $request->cust_phone ?? null,
                'cust_kecamatan' => $request->cust_kecamatan ?? null,
                'cust_kelurahan' => '',
                'cust_alamat' => $request->cust_alamat ?? null,
                'detail' => $detail_order,
                'paid' => $price,
                'status' => '0',
                'branch_id' => $userId,
                'payment_method' => 'kas',
                'user_id' => $userId,
                'diskon' => 0,
                'shipping' => 0,
                'tax' => 0
            ];
            $data = Penjualan::create($need_save);
            PenjualanProduct::create([
                'penjualan_id' => $data->id,
                'product_id' => $product->id,
                'price' => $price,
                'quantity' => 1,
                'total' => $price
            ]);
            $this->customerServiceSendMessage($data->id);

            DB::commit();
            return redirect()->route('landing-page.order', ['reference_id' => $refid, 'landing_id' => $id]);
        } catch (Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    public function checkoutByWa(Request $request, string $id)
    {
        $bump = isset($request->bump_product_id) ? 'yes' : 'no';
        return redirect()->route('landing-page.order-wa', ['landing_id' => $id, 'bump' => $bump]);
    }

    public function order(Request $request, string $landing_id, string $reference_id)
    {
        $landing = LandingPage::find($landing_id);
        if (!$landing) {
            return redirect()->back();
        }
        $scriptheader = $landing->script_header_payment_page;
        return view('main.landing_page.order', compact('reference_id', 'scriptheader'));
    }

    public function orderByWa(Request $request, string $landing_id, string $bump)
    {
        $landing = LandingPage::find($landing_id);

        if ($landing) {
            $phoneNumber = $landing->contact_seller;

            // Cek apakah angka pertama adalah 0
            if (substr($phoneNumber, 0, 1) === '0') {
                // Ganti angka 0 dengan kode negara 62
                $phoneNumber = '62' . substr($phoneNumber, 1);
            }

            $host = env('APP_URL');
            $product_url = $host . '/checkout/' . $landing->id . '/' . $landing->slug;
            // Pesan yang akan dikirim
            $message = "Halo, saya tertarik dengan. $product_url, Bisa kita diskusikan lebih lanjut?";
            if ($bump === 'yes') {
                $message .= ' beserta bump produk nya, Bisa kita diskusikan lebih lanjut?';
            } else {
                $message .= 'Bisa kita diskusikan lebih lanjut?';
            }

            // Format pesan ke dalam URL encoded
            $message = urlencode($message);
            $scriptheader = $landing->script_header_wa_page;

            return view('main.landing_page.order_wa', compact('phoneNumber', 'message', 'scriptheader'));
        }
    }
}
