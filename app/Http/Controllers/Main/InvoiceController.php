<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\API\PosController;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BusinessGroup;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceTermin;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MdClient;
use App\Models\MdCurrency;
use App\Models\MlAccount;
use App\Models\Penjualan;
use App\Models\WalletLogs;
use App\Traits\CommonTrait;
use App\Traits\DuitkuTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    use DuitkuTrait;
    use CommonTrait;

    public function index()
    {
        $view = 'invoice';
        $payment = $this->typePayment();

        return view('main.invoice.index', compact('view', 'payment'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                $checkbox =
                    '<div class="custom-control custom-checkbox">
                    <input class="custom-control-input checkbox" id="checkbox' .
                    $data->id .
                    '" type="checkbox" value="' .
                    $data->id .
                    '" />
                    <label class="custom-control-label" for="checkbox' .
                    $data->id .
                    '"></label>
                </div>';

                return $checkbox;
            })
            ->addColumn('copy_link', function ($data) {
                $html = '';
                $html .= '<a href="javascript:void(0)" style="background-color: #385a9c !important;" class="btn-primary btn-sm me-2 copyLinkButton" data-url="' . route('invoice.preview', $data->id) . '" style="padding: 8px; font-size: 9px; border-radius: 4px;" title="Copy Invoice">
                        <i class="fa fa-copy"></i> Invoice Link
                    </a>';
                if ($data->payment_url != null) {
                    $html .= '<a href="javascript:void(0)" style="background-color: #385a9c !important;" class="btn-primary btn-sm me-2 copyLinkButton" data-url="' . $data->payment_url . '" style="padding: 8px; font-size: 9px; border-radius: 4px;" title="Copy Payment Link">
                            <i class="fa fa-copy"></i> Payment Link
                        </a>';
                }

                return $html;
            })
            ->addColumn('payment_url', function ($data) {
                $html = '';
                $html .= '<a href="' . route('invoice.export', $data->id) . '" target="_blank" class="btn-success btn-sm me-2" style="padding: 8px; font-size: 9px; border-radius: 4px;" title="Download Invoice">
                <img src="' . asset('template/main/images/icon-download-pdf.png') . '" width="20px">
                </a>';
                return $html;
            })
            ->addColumn('termin_action', function ($data) {
                if ($data->status != 1) {
                    $btn = '<div class="d-flex">';
                    $btn .= '<a title="Termin" style="margin-right:2px;" href="javascript:void(0);" onclick="payment(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside">DP</a>';
                    $btn .= '<a title="History" style="margin-right:2px;" href="javascript:void(0);" onclick="detail(' . $data->id . ')" class="avatar-text avatar-md bg-primary text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-history"></i></a>';
                    $btn .= '</div>';
                } else {
                    $btn = '-';
                }

                return $btn;
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('invoice_number', function ($data) {
                return $data->invoice_number;
            })
            ->addColumn('total_rupiah', function ($data) {
                $total_rupiah = $data->grand_total * $data->kurs;
                return 'Rp. ' . customNumberFormat($total_rupiah);
            })
            ->addColumn('grand_total', function ($data) {
                return ($data->currency->symbol ?? null) . customNumberFormat($data->grand_total);
            })
            ->addColumn('currency', function ($data) {
                return $data->currency->code ?? null;
            })
            ->addColumn('kurs', function ($data) {
                return 'Rp. ' . customNumberFormat($data->kurs);
            })
            ->addColumn('client', function ($data) {
                return $data->client->name ?? null;
            })
            ->addColumn('created', function ($data) {
                return Carbon::parse($data->created)->format('d-m-Y');
            })
            ->addColumn('due_date', function ($data) {
                return Carbon::parse($data->due_date)->format('d-m-Y');
            })
            ->addColumn('status', function ($data) {
                $status_elem = '';
                if ($data->status == 1) {
                    $status_elem = '<span class="badge bg-success">Paid</span>';
                } else if ($data->status == 2) {
                    $status_elem = '<span class="badge bg-danger">Canceled</span>';
                } else {
                    $status_elem = '<span class="badge bg-warning">Unpaid</span>';
                }

                return $status_elem;
            })
            ->addColumn('payment_method', function ($data) {
                return $data->payment_method;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';

                if ($data->sync_status == 1) {
                    $btn .= '<a title="Sync Jurnal" style="margin-right:2px;" href="javascript:void(0);" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a>';
                } else {
                    $btn .= '<a title="Sync Jurnal" style="margin-right:2px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a>';
                }

                $btn .= '<a href="' . route('invoice.invoice.edit', $data->id) . '"  title="Ubah" style="margin-right:2px;" class="avatar-text avatar-md bg-warning text-white" data-bs-auto-close="outside"><i class="fa fa-edit"></i></a>';
                $btn .= '<a title="Hapus" style="margin-right:2px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a>';
                $btn .= '</div>';
                return $btn;
            })
            ->addcolumn('sync_jurnal', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'name',
            'sync_status',
            'invoice_from',
            'client_id',
            'invoice_number',
            'payment_method',
            'created',
            'currency_id',
            'due_date',
            'kurs',
            'discount_type',
            'sub_total',
            'discount_value',
            'tax',
            'status',
            'grand_total',
            'flip_ref',
            'payment_url',
        ];
        $keyword = $request->keyword;
        $month = $request->month ?? '';
        $year = $request->year ?? '';
        $status = $request->status ?? '';
        $payment_method = $request->payment_method ?? '';

        $user_id = session('id') ?? Auth::user()->id;
        $checkUser = MlAccount::find($user_id);
        $userIdAll = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');

        $data = Invoice::orderBy('id', 'desc')
            ->when($month, function ($query) use ($month) {
                if ($month != '') {
                    return $query->whereMonth('created', $month);
                }
            })
            ->when($year, function ($query) use ($year) {
                if ($year != '') {
                    return $query->whereYear('created', $year);
                }
            })
            ->when($status, function ($query) use ($status) {
                if ($status != '') {
                    return $query->where('status', (int)$status);
                }
            })
            ->when($payment_method, function ($query) use ($payment_method) {
                if ($payment_method != '') {
                    return $query->where('payment_method', $payment_method);
                }
            })
            ->whereIn('user_id', $userIdAll)
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return $data;
    }

    public function chart(Request $request)
    {
        $month = $request->month ?? '';
        $year = $request->year ?? '';
        $payment_method = $request->payment_method ?? '';

        $data = Invoice::where('user_id', session('id'))
            ->when($month, function ($query) use ($month) {
                if ($month != '') {
                    return $query->whereMonth('created', $month);
                }
            })
            ->when($year, function ($query) use ($year) {
                if ($year != '') {
                    return $query->whereYear('created', $year);
                }
            })
            ->when($payment_method, function ($query) use ($payment_method) {
                if ($payment_method != '') {
                    return $query->where('payment_method', $payment_method);
                }
            })
            ->where('status', 1)->get();

        $total_paid = 0;
        foreach ($data as $key => $value) {
            $total_paid += $value->grand_total * $value->kurs;
        }

        $data['total_paid'] = 'Rp. ' . number_format($total_paid, 0, ',', '.');

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = 'invoice-create';
        $typePayment = $this->typePayment();

        if (count($typePayment) == 0) {
            return redirect('payment-method-setting')->with('warning', 'Anda belum melengkapi data rekening bank anda, Silahkan anda lengkapi terlebih dahulu');
        }

        $user = Account::where('id', session('id'))->first();
        $data['tax'] = $user->tax;

        return view('main.invoice.create', compact('view', 'typePayment', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data) {
                $kurs = str_replace('.', '', $data['kurs']);
                $discount_value = str_replace('.', '', $data['discount_value']);

                $dataInvoice['user_id'] = session('id');
                $dataInvoice['name'] = $data['name'];
                $refid = $data['invoice_number'];
                $dataInvoice['invoice_from'] = $data['invoice_from'];
                $dataInvoice['client_id'] = $data['client_id'];
                $dataInvoice['invoice_number'] = $data['invoice_number'];
                $dataInvoice['payment_method'] = $data['payment_method'];
                $dataInvoice['created'] = $data['created'];
                $dataInvoice['due_date'] = $data['due_date'];
                $dataInvoice['signature_name'] = $data['signature_name'];
                $dataInvoice['signature_position'] = $data['signature_position'];
                $dataInvoice['notes'] = $data['notes'];
                $dataInvoice['additional_intruction'] = $data['additional_intruction'];
                $dataInvoice['currency_id'] = $data['currency'];
                $dataInvoice['kurs'] = $kurs;
                $dataInvoice['discount_type'] = $data['discount_type'];
                $dataInvoice['discount_value'] = $discount_value;
                $dataInvoice['discount_amount'] = $data['discount_amount'];
                $dataInvoice['tax'] = $data['tax'];
                $dataInvoice['tax_amount'] = $data['tax_amount'];
                $dataInvoice['sub_total'] = $data['sub_total'];
                $dataInvoice['grand_total'] = $data['sub_total'] + $data['tax_amount'] - (int)$data['discount_amount'];
                $dataInvoice['total_rupiah'] = (int)$dataInvoice['grand_total'] * (int)$kurs;
                $dataInvoice['is_quotation'] = $data['is_quotation'];

                $invoice = Invoice::create($dataInvoice);

                foreach ($data['price'] as $key => $value) {
                    $dataInvoiceDetail['invoice_id'] = $invoice->id;
                    $dataInvoiceDetail['description'] = $data['description'][$key];
                    $dataInvoiceDetail['short_description'] = $data['short_description'][$key];

                    $price = str_replace('.', '', $data['price'][$key]);

                    $dataInvoiceDetail['price'] = $price;
                    $dataInvoiceDetail['qty'] = $data['qty'][$key];
                    $dataInvoiceDetail['sub_total'] = $price * $data['qty'][$key];

                    $invoiceDetail = InvoiceDetail::create($dataInvoiceDetail);
                }

                if ($data['payment_method'] == 'randu-wallet') {
                    $itemsDetails = [
                        [
                            'name' => 'Randu - ' . $refid,
                            'price' => (int)$dataInvoice['total_rupiah'],
                            'quantity' => 1
                        ],
                    ];

                    $client = MdClient::where('id', $data['client_id'])->first();

                    if ($client->name && $client->phone) {
                        $customerDetail = [
                            'email' => $client->email ?? '',
                            'phone' => $client->phone ?? '',
                            'username' => '', // client tidak ada username
                            'fullname' => $client->name,
                        ];
                    } else {
                        // return gagal data customer tidak ada
                        return response()->json([
                            'status' => false,
                            'message' => 'Data customer seperti nama dan phone wajib ada',
                        ]);
                    }

                    $invoiceDuitku = $this->createInvoice('invoice-' . $refid, $itemsDetails, ($data['return_url'] ?? ''), 'invoice-generator', $customerDetail);
                    $result = $invoiceDuitku['result'];
                    if ($invoiceDuitku['httpCode'] === 200) {
                        $config = DB::table('ml_site_config')->first();
                        WalletLogs::create([
                            'user_id' => session('id'),
                            'amount' => ($invoice->total_rupiah * $config->fee_payment_gateway) / 100,
                            'type' => '-',
                            'from' => 'Invoice',
                            'group' => 'transaction-fee',
                            'note' => 'Transaksi Fee Randu Wallet - Invoice',
                            'reference' => $result->reference,
                            'status' => '0',
                        ]);

                        WalletLogs::create([
                            'user_id' => session('id'),
                            'amount' => $invoice->total_rupiah,
                            'type' => '+',
                            'from' => 'Invoice',
                            'group' => 'income-invoice',
                            'note' => 'Pemasukan Dari Invoice Sebesar Rp. ' . number_format($invoice->total_rupiah, 0, ',', '.'),
                            'reference' => $result->reference,
                            'status' => '0',
                            'payment_return_url' => $result->paymentUrl,
                        ]);

                        Invoice::whereId($invoice->id)->update([
                            'payment_url' => $result->paymentUrl,
                            'payment_start_at' => now()
                        ]);
                        // UPDATE REFERENCE FROM DUITKU
                        $invoice->flip_ref = $result->reference;
                        $invoice->save();
                        $result->payment_method = $data['payment_method'];
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Terdapat kesalahan saat mencoba melakukan pembayaran, coba lagi nanti.',
                        ]);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $data = Invoice::findOrFail($id);
        $view = 'invoice-edit';
        $typePayment = $this->typePayment();

        $user = Account::where('id', session('id'))->first();
        $data['tax'] = $data['tax'] ?? $user->tax;

        if ($data->invoice_from == 'personal') {
            $account = MlAccount::where('id', session('id'))->first();
            $data['from_name'] = $account->fullname;
            $data['from_email'] = $account->email;
            $data['from_address'] = $account->mlAccountInfo->store_address ?? '';
            $data['from_phone'] = $account->phone;
        } else if ($data->invoice_from == 'perusahaan') {
            $userId = $this->get_owner_id(session('id'));
            $business = BusinessGroup::where('user_id', $userId)->first();
            $data['from_name'] = $business->branch_name;
            $data['from_email'] = $business->company_email;
            $data['from_address'] = $business->business_address;
            $data['from_phone'] = $business->business_phone;
        }

        return view('main.invoice.edit', compact('view', 'data', 'typePayment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data, $id) {
                $kurs = str_replace('.', '', $data['kurs']);
                $discount_value = str_replace('.', '', $data['discount_value']);

                $dataInvoice['user_id'] = session('id');
                $dataInvoice['name'] = $data['name'];
                $refid = $data['invoice_number'];
                $dataInvoice['invoice_from'] = $data['invoice_from'];
                $dataInvoice['client_id'] = $data['client_id'];
                $dataInvoice['invoice_number'] = $data['invoice_number'];
                $dataInvoice['payment_method'] = $data['payment_method'];
                $dataInvoice['created'] = $data['created'];
                $dataInvoice['due_date'] = $data['due_date'];
                $dataInvoice['signature_name'] = $data['signature_name'];
                $dataInvoice['signature_position'] = $data['signature_position'];
                $dataInvoice['notes'] = $data['notes'];
                $dataInvoice['additional_intruction'] = $data['additional_intruction'];
                $dataInvoice['currency_id'] = $data['currency'];

                $dataInvoice['kurs'] = $kurs;
                $dataInvoice['discount_type'] = $data['discount_type'];
                $dataInvoice['discount_value'] = $discount_value;
                if (isset($data['discount_amount'])) {
                    $dataInvoice['discount_amount'] = $data['discount_amount'];
                }
                $dataInvoice['tax'] = $data['tax'];
                if (isset($data['tax_amount'])) {
                    $dataInvoice['tax_amount'] = $data['tax_amount'];
                }
                if (isset($data['sub_total'])) {
                    $dataInvoice['sub_total'] = $data['sub_total'];
                }
                $dataInvoice['grand_total'] = $data['sub_total'] + $data['tax_amount'] - (int)$data['discount_amount'];
                $dataInvoice['total_rupiah'] = $dataInvoice['grand_total'] * $kurs;
                $dataInvoice['is_quotation'] = $data['is_quotation'];

                $invoice = Invoice::findOrFail($id);
                $invoice->update($dataInvoice);

                if ($data['payment_method'] == 'randu-wallet') {
                    $itemsDetails = [
                        [
                            'name' => 'Randu - ' . $refid,
                            'price' => (int)$dataInvoice['total_rupiah'],
                            'quantity' => 1
                        ],
                    ];

                    $client = MdClient::where('id', $data['client_id'])->first();

                    if ($client->name && $client->phone) {
                        $customerDetail = [
                            'email' => $client->email ?? '',
                            'phone' => $client->phone ?? '',
                            'username' => '', // client tidak ada username
                            'fullname' => $client->name,
                        ];
                    } else {
                        // return gagal data customer tidak ada
                        return response()->json([
                            'status' => false,
                            'message' => 'Data customer seperti nama dan phone wajib ada',
                        ]);
                    }

                    $invoiceDuitku = $this->createInvoice('invoice-' . $refid, $itemsDetails, ($data['return_url'] ?? ''), 'invoice-generator', $customerDetail);
                    $result = $invoiceDuitku['result'];
                    if ($invoiceDuitku['httpCode'] === 200) {
                        Invoice::whereId($invoice->id)->update([
                            'payment_url' => $result->paymentUrl,
                            'payment_start_at' => now()
                        ]);
                        // UPDATE REFERENCE FROM DUITKU
                        $invoice->flip_ref = $result->reference;
                        $invoice->save();
                        $result->payment_method = $data['payment_method'];
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Terdapat kesalahan saat mencoba melakukan pembayaran, coba lagi nanti.',
                        ]);
                    }
                }

                InvoiceDetail::whereNotIn('id', $data['id'])->where('invoice_id', $id)->delete();
                foreach ($data['price'] as $key => $value) {

                    $price = str_replace('.', '', $data['price'][$key]);

                    $dataInvoiceDetail['price'] = $price;
                    $dataInvoiceDetail['invoice_id'] = $id;
                    $dataInvoiceDetail['description'] = $data['description'][$key];
                    $dataInvoiceDetail['short_description'] = $data['short_description'][$key];
                    $dataInvoiceDetail['qty'] = $data['qty'][$key];
                    $dataInvoiceDetail['sub_total'] = $price * $data['qty'][$key];

                    if (isset($data['id'][$key])) {
                        $invoiceDetail = InvoiceDetail::findOrFail($data['id'][$key])->update($dataInvoiceDetail);
                    } else {
                        $invoiceDetail = InvoiceDetail::create($dataInvoiceDetail);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Ubah!',
                ]);
            });
        } catch (\Throwable $th) {
            dd($th);
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Ubah!',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $dt = Invoice::findorFail($id);
                if ($dt->sync_status == 1) {
                    $journals = Journal::where('relasi_trx', 'invoice_' . $id)->get();
                    foreach ($journals as $journal) {
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                }
                $delete = Invoice::find($id)->delete();
                $invoiceDetail = InvoiceDetail::where('invoice_id', $id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }

    public function typePayment()
    {
        $userId = $this->get_owner_id(session('id'));

        $info = DB::table('ml_account_info')->where('user_id', $userId)->first();

        $payment_method = isset($info->payment_method) ? json_decode($info->payment_method) : [];
        $payment = [];
        $kasbon = [
            'id' => 4,
            'method' => 'Kasbon / Piutang',
            'description' => 'Untuk Kasbon Pembayaran Tempo (kasbon/piutang)',
            'selected' => false,
            'items' => []
        ];

        $piutangCounter = 0;
        foreach ($payment_method as $key => $value) {
            if ($value->method == 'COD' && $value->selected === 'true') {
                $piutangCounter++;
            }

            if ($value->method == 'Marketplace' && $value->selected === 'true') {
                $piutangCounter++;
            }

            if ($value->method == 'Piutang' && $value->selected === 'true') {
                $piutangCounter++;
            }
        }

        if ($piutangCounter > 0) {
            $kasbon['selected'] = true;
        }

        foreach ($payment_method as $key => $value) {
            if ($value->method == 'Cash') {
                $payment_detail['id'] = $value->id * 1;
                $payment_detail['method'] = 'Bayar Tunai';
                $payment_detail['selected'] = $value->selected === 'false' ? false : true;
                $payment_detail['code'] = 'kas';
                $payment_detail['description'] = 'Pembayaran Dengan Uang Tunai di Kasir';
                $payment[] = $payment_detail;
            }
            if ($value->method == 'Online-Payment') {
                $payment_detail['id'] = $value->id * 1;
                $payment_detail['method'] = 'Payment Gateway';
                $payment_detail['selected'] = $value->selected === 'false' ? false : true;
                $payment_detail['code'] = 'randu-wallet';
                $payment_detail['description'] = 'Gunakan fasilitas Randu Wallet';
                $payment[] = $payment_detail;
            }

            if ($value->method == 'Transfer') {
                foreach ($value->banks as $ckey => $cvalue) {
                    $payment_detail['id'] = $ckey + 1;
                    if ($cvalue->bank == 'Bank BCA') {
                        $payment_detail['code'] = 'bank-bca';
                    } else if ($cvalue->bank == 'Bank BNI') {
                        $payment_detail['code'] = 'bank-bni';
                    } else if ($cvalue->bank == 'Bank Mandiri') {
                        $payment_detail['code'] = 'bank-mandiri';
                    } else if ($cvalue->bank == 'Bank BRI') {
                        $payment_detail['code'] = 'bank-bri';
                    } else {
                        $payment_detail['code'] = 'bank-lain';
                    }
                    $payment_detail['method'] = $cvalue->bank;
                    $payment_detail['selected'] = ($value->selected == "false") ? false : json_decode($cvalue->selected);
                    $payment_detail['bankOwner'] = $cvalue->bankOwner;
                    $payment_detail['bankAccountNumber'] = $cvalue->bankAccountNumber;
                    $payment[] = $payment_detail;
                }
            }

            if ($value->method == 'COD') {
                $payment_detail['id'] = 1;
                $payment_detail['method'] = $value->method;
                $payment_detail['code'] = 'piutang-cod';
                $payment_detail['selected'] = json_decode($value->selected);
                $payment[] = $payment_detail;
            }

            if ($value->method == 'Marketplace') {
                $payment_detail['id'] = 2;
                $payment_detail['method'] = $value->method;
                $payment_detail['code'] = 'piutang-marketplace';
                $payment_detail['selected'] = json_decode($value->selected);
                $payment[] = $payment_detail;
            }

            if ($value->method == 'Piutang') {
                $payment_detail['id'] = 3;
                $payment_detail['method'] = $value->method;
                $payment_detail['code'] = 'piutang-usaha';
                $payment_detail['selected'] = json_decode($value->selected);
                $payment[] = $payment_detail;
            }
        }

        $payment = collect($payment)->where('selected', true)->toArray();

        return $payment;
    }

    public function invoiceFrom(Request $request)
    {
        if ($request->invoice_from == 'personal') {
            $account = MlAccount::where('id', session('id'))->first();
            $data['name'] = $account->fullname;
            $data['email'] = $account->email;
            $data['address'] = $account->mlAccountInfo->store_address ?? '';
            $data['phone'] = $account->phone;
        } else if ($request->invoice_from == 'perusahaan') {
            // $userId = $this->get_owner_id(session('id'));
            $user_id = session('id') ?? Auth::user()->id;
            $checkUser = MlAccount::find($user_id);
            $userIdAll = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');

            $business = BusinessGroup::whereIn('user_id', $userIdAll)->first();
            $data['name'] = $business->branch_name;
            $data['email'] = $business->company_email;
            $data['address'] = $business->business_address;
            $data['phone'] = $business->business_phone;
        }

        return response()->json([
            'status' => true,
            'message' => 'Data Berhasil di dapatkankan!',
            'data' => $data,
        ]);
    }

    public function currency(Request $request)
    {
        $columns = [
            'id',
            'name',
            'code',
            'symbol',
        ];
        $keyword = $request->search;

        $data = MdCurrency::orderBy('name', 'asc')
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data Berhasil di dapatkankan!',
            'data' => $data
        ]);
    }

    public function export($id, $invoice_termin_id = null)
    {
        $owner_id = $this->get_owner_id(session('id') ?? Auth::user()->id);
        $user = Account::where('id', $owner_id)->first();
        // $checkUser = MlAccount::find($user_id);
        // $userIdAll = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');

        $data = Invoice::where('id', $id)->first();
        $data->logo = $data->invoice_from == 'personal' ? $data->user->profile_picture : $user->business_group->logo;
        $business = BusinessGroup::where('user_id', $owner_id)->first();
        $termin = InvoiceTermin::find($invoice_termin_id) ?? [];

        if ($data->invoice_from == 'personal') {
            $account = MlAccount::where('id', session('id'))->first();
            $data['from_name'] = $account->fullname;
            $data['from_email'] = $account->email;
            $data['from_address'] = $account->mlAccountInfo->store_address ?? '';
            $data['from_phone'] = $account->phone;
        } else if ($data->invoice_from == 'perusahaan') {
            $userId = $this->get_owner_id(session('id'));
            $data['from_name'] = $business->branch_name;
            $data['from_email'] = $business->company_email;
            $data['from_phone'] = $business->business_phone;
        }
        $data['from_address'] = $business->business_address ?? null;

        $bank = $this->getRekening($data->payment_method, session('id'));
        $data['bank_name'] = $bank['method'] ?? null;
        $data['bank_owner'] = $bank['bankOwner'] ?? null;
        $data['bank_number'] = $bank['bankAccountNumber'] ?? null;

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);

        if ($termin != []) {
            if ($termin->nominal_type == 'percent') {
                $termin['nominal'] = $data->grand_total * $termin->nominal / 100;
            } else {
                $termin['nominal'] = $termin->nominal;
            }
        }

        $pdf = Pdf::loadView('main.invoice.export', compact('data', 'user', 'termin'));

        // Download the generated PDF
        // return $pdf->download('filename.pdf');

        // Stream the generated PDF
        return $pdf->stream($data->invoice_number ?? $data->name ?? 'invoice.pdf');

        return view('main.invoice.export', compact('data', 'user', 'termin'));
    }

    public function preview($id, $invoice_termin_id = null)
    {
        $data = Invoice::where('id', $id)->first();
        $user = Account::where('id', $data->user_id)->first();
        $userOwner = Account::where('id', $this->get_owner_id($data->user_id))->first();
        $data->logo = $data->invoice_from == 'personal' ? $data->user->profile_picture : $userOwner->business_group->logo;
        $business = BusinessGroup::where('user_id', $userOwner->id)->first();
        $termin = InvoiceTermin::find($invoice_termin_id) ?? [];

        if ($data->invoice_from == 'personal') {
            $account = MlAccount::where('id', $data->user_id)->first();
            $data['from_name'] = $account->fullname;
            $data['from_email'] = $account->email;
            $data['from_address'] = $account->mlAccountInfo->store_address ?? '';
            $data['from_phone'] = $account->phone;
        } else if ($data->invoice_from == 'perusahaan') {
            $userId = $this->get_owner_id($data->user_id);
            $data['from_name'] = $business->branch_name;
            $data['from_email'] = $business->company_email;
            $data['from_phone'] = $business->business_phone;
        }
        $data['from_address'] = $business->business_address ?? null;

        $bank = $this->getRekening($data->payment_method, $data->user_id);
        $data['bank_name'] = $bank['method'] ?? null;
        $data['bank_owner'] = $bank['bankOwner'] ?? null;
        $data['bank_number'] = $bank['bankAccountNumber'] ?? null;

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);

        if ($termin != []) {
            if ($termin->nominal_type == 'percent') {
                $termin['nominal'] = $data->grand_total * $termin->nominal / 100;
            } else {
                $termin['nominal'] = $termin->nominal;
            }
        }

        $pdf = Pdf::loadView('main.invoice.export', compact('data', 'user', 'termin'));

        // Download the generated PDF
        // return $pdf->download('filename.pdf');

        // Stream the generated PDF
        // return $pdf->stream($data->invoice_number ?? $data->name ?? 'invoice.pdf');
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data->invoice_number ?? $data->name ?? 'invoice') . '.pdf';
        return $pdf->stream($filename);

        return view('main.invoice.preview', compact('data', 'user', 'termin'));
    }

    public function changeBulkPaid(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                foreach ($ids as $key => $id) {
                    $delete = Invoice::find($id)->update(['status' => 1]);
                    if ($delete) {
                        $this->send_to_journal_invoice($id);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Diubah!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Data Gagal Diubah!',
            ]);
        }
    }

    public function getRekening($code, $userId)
    {
        $userId = $this->get_owner_id($userId);

        $info = DB::table('ml_account_info')->where('user_id', $userId)->first();

        if (!$info) {
            return response()->json([
                'status' => false,
                'message' => 'Silahkan setting metode pembayaran terlebih dahulu.',
            ]);
        }

        $payment_method = json_decode($info->payment_method);
        $payment = [];

        foreach ($payment_method as $key => $value) {
            if ($value->method == 'Transfer') {
                foreach ($value->banks as $ckey => $cvalue) {
                    $payment_detail['id'] = $ckey + 1;
                    if ($cvalue->bank == 'Bank BCA') {
                        $payment_detail['code'] = 'bank-bca';
                    } else if ($cvalue->bank == 'Bank BNI') {
                        $payment_detail['code'] = 'bank-bni';
                    } else if ($cvalue->bank == 'Bank Mandiri') {
                        $payment_detail['code'] = 'bank-mandiri';
                    } else if ($cvalue->bank == 'Bank BRI') {
                        $payment_detail['code'] = 'bank-bri';
                    } else {
                        $payment_detail['code'] = 'bank-lain';
                    }
                    $payment_detail['method'] = $cvalue->bank;
                    $payment_detail['selected'] = json_decode($cvalue->selected);
                    $payment_detail['bankOwner'] = $cvalue->bankOwner;
                    $payment_detail['bankAccountNumber'] = $cvalue->bankAccountNumber;
                    $payment[] = $payment_detail;
                }
            }
        }

        $payment = collect($payment)->where('code', $code)->first();

        return $payment;
    }

    public function checkExchange($id)
    {
        $data = MdCurrency::find($id);

        return response()->json([
            'status' => true,
            'message' => 'Data Berhasil didapat!',
            'data' => $data
        ]);
    }


    public function single_sync(Request $request)
    {
        $input = $request->all();

        $this->send_to_journal_invoice($input['id']);

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function show($id)
    {
        $data = Invoice::find($id);
        foreach ($data->termin as $key => $value) {
            if ($value->nominal_type == 'percent') {
                $value['nominal'] = $data->grand_total * $value->nominal / 100;
            } else {
                $value['nominal'] = $value->nominal;
            }
        }

        return view('main.invoice.detail', compact('data'));
    }

    public function todoTermin($id)
    {
        $data = Invoice::find($id);
        $typePayment = $this->typePayment();

        return view('main.invoice.termin', compact('data', 'typePayment'));
    }

    public function terminStore(Request $request, $id)
    {
        $data = $request->all();
        try {
            return $this->atomic(function () use ($data, $id) {
                $invoice = Invoice::find($id);
                $refid = $invoice->invoice_number;
                $data['number'] = InvoiceTermin::where('invoice_id', $id)->count() + 1;
                $data['invoice_id'] = $id;
                $data['nominal'] = str_replace('.', '', $data['nominal']);
                $termin = InvoiceTermin::create($data);

                if ($data['payment_method'] == 'randu-wallet') {
                    $grand_total = 0;
                    if ($termin->nominal_type == 'percent') {
                        $grand_total = $invoice->grand_total * $termin->nominal / 100;
                    } else {
                        $grand_total = $termin->nominal;
                    }

                    $itemsDetails = [
                        [
                            'name' => 'Randu - ' . $refid,
                            'price' => (int)$grand_total,
                            'quantity' => 1
                        ],
                    ];

                    $client = MdClient::where('id', $invoice['client_id'])->first();

                    if ($client->name && $client->phone) {
                        $customerDetail = [
                            'email' => $client->email ?? '',
                            'phone' => $client->phone ?? '',
                            'username' => '',
                            'fullname' => $client->name,
                        ];
                    } else {
                        // return gagal data customer tidak ada
                        return response()->json([
                            'status' => false,
                            'message' => 'Data customer seperti nama dan phone wajib ada',
                        ]);
                    }

                    $invoiceDuitku = $this->createInvoice('invoice-' . $refid, $itemsDetails, ($data['return_url'] ?? ''), 'invoice-generator-termin', $customerDetail);
                    $result = $invoiceDuitku['result'];
                    if ($invoiceDuitku['httpCode'] === 200) {
                        $config = DB::table('ml_site_config')->first();
                        WalletLogs::create([
                            'user_id' => session('id'),
                            'amount' => ($grand_total * $config->fee_payment_gateway) / 100,
                            'type' => '-',
                            'from' => 'Invoice',
                            'group' => 'transaction-fee',
                            'note' => 'Transaksi Fee Randu Wallet - Invoice',
                            'reference' => $result->reference,
                            'status' => '0',
                        ]);

                        WalletLogs::create([
                            'user_id' => session('id'),
                            'amount' => $grand_total,
                            'type' => '+',
                            'from' => 'Invoice',
                            'group' => 'income-invoice',
                            'note' => 'Pemasukan Dari Invoice Sebesar Rp. ' . number_format($grand_total, 0, ',', '.'),
                            'reference' => $result->reference,
                            'status' => '0',
                            'payment_return_url' => $result->paymentUrl,
                        ]);

                        InvoiceTermin::whereId($termin->id)->update([
                            'payment_url' => $result->paymentUrl,
                            'payment_start_at' => now()
                        ]);
                        // UPDATE REFERENCE FROM DUITKU
                        $termin->flip_ref = $result->reference;
                        $termin->save();
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Terdapat kesalahan saat mencoba melakukan pembayaran, coba lagi nanti.',
                        ]);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Termin Berhasil Dibuat!',
                ]);
            });
        } catch (\Throwable $th) {
            dd($th);
            return response()->json([
                'status' => true,
                'message' => 'Data Gagal Diubah!',
            ]);
        }
    }

    public function changePaidTermin($id)
    {
        try {
            $data = InvoiceTermin::find($id);
            $data->status = 1;
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Data Berhasil Diubah!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Data Gagal Diubah!',
            ]);
        }
    }

    public function quickInvoice(Request $request)
    {
        $reference = $request->get('ref');
        if (!$reference) {
            abort(404, 'Reference not found');
        }

        $data = Penjualan::with(['flag', 'products.variant.variant', 'products.product'])->where('reference', $reference)->first();

        if (!$data) {
            abort(404, 'Data penjualan tidak ditemukan');
        }

        // Mendapatkan informasi toko dari user
        $user = DB::table('ml_accounts')->where('id', $data->user_id)->first();
        $company = DB::table('business_groups')->where('user_id', $data->user_id)->first();
        $setting = DB::table('ml_setting_users')->where('user_id', $data->user_id)->first();

        // Format tanggal
        $tanggal = Carbon::parse($data->created_at)->format('d F Y');

        // Menentukan metode pembayaran seperti di PosController
        $metodePembayaran = match ($data->payment_method) {
            'randu-wallet' => 'Randu Wallet',
            'kas' => 'Kas / Tunai Kas',
            'bank-mandiri' => 'Transfer Bank Mandiri',
            'bank-bri' => 'Transfer Bank BRI',
            'bank-bni' => 'Transfer Bank BNI',
            'bank-bca' => 'Transfer Bank BCA',
            'piutang-cod' => 'Piutang COD',
            'piutang-marketplace' => 'Piutang Marketplace',
            'piutang-usaha' => 'Piutang Usaha',
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'bank-transfer' => 'Transfer Bank',
            default => 'Metode Lainnya'
        };

        // Menentukan status pesanan
        $statusPesanan = match ($data->payment_status) {
            1 => 'Pesanan Selesai',
            0 => 'Pesanan Pending',
            default => 'Status Tidak Diketahui'
        };

        // Menyiapkan item pesanan dengan detail varian seperti di PosController
        $orderItems = [];
        $totalProduct = 0;
        foreach ($data->products as $product) {
            // Hitung total produk
            $totalProduct += $product->quantity;

            // Menyiapkan varian untuk produk ini
            $variants = [];
            if ($product->variant && count($product->variant) > 0) {
                foreach ($product->variant as $variant) {
                    if ($variant->quantity > 0) {
                        $variants[] = [
                            'id' => $variant->id,
                            'name' => $variant->variant->varian_name ?? 'Varian',
                            'quantity' => $variant->quantity,
                            'price' => $variant->price,
                            'note' => $variant->note ?? null
                        ];
                    }
                }
            }

            $orderItems[] = [
                'id' => $product->id,
                'name' => $product->product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'note' => $product->note ?? null,
                'variants' => $variants
            ];
        }

        // Menggunakan data langsung dari model seperti di PosController
        $subtotal = $data->order_total; // Subtotal sebelum diskon, shipping, dan pajak
        $diskon = $data->diskon ?? 0;
        $shipping = $data->shipping ?? 0;
        $pajak = $data->tax ?? 0;
        $totalBayar = $data->paid; // Total yang harus dibayar
        $uangDiterima = $data->payment_amount ?? 0;
        $kembalian = $data->payment_amount - $data->paid;

        // Hitung pembulatan: total - (subtotal - diskon + order fee + pajak)
        $pembulatan = $totalBayar - ($subtotal - $diskon + $shipping + $pajak);

        $receiptData = [
            'reference' => $data->reference,
            'tanggal' => $tanggal,
            'namaPembeli' => $data->cust_name ?? $data->customer->name ?? '-',
            'noTelepon' => $data->cust_phone ?? $data->customer->phone ?? '-',
            'metodePembayaran' => $metodePembayaran,
            'noMeja' => $data->qr_codes_id ? ($data->desk ? $data->desk->no_meja : "Meja_{$data->qr_codes_id}") : '-',
            'grupMeja' => 'Area Utama',
            'jumlahOrang' => '1',
            'storeName' => $company->branch_name ?? $user->fullname ?? 'Toko',
            'status' => $statusPesanan,
            'idOrder' => $data->reference,
            'noOrder' => $data->id,
            'tipePesanan' => $data->qr_codes_id ? 'Makan Di Tempat' : 'Takeaway',
            'orderItems' => $orderItems,
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'shipping' => $shipping,
            'pajak' => $pajak,
            'pembulatan' => $pembulatan,
            'totalBayar' => $totalBayar,
            'uangDiterima' => $uangDiterima,
            'kembalian' => $kembalian,
            'alamatToko' => $company->business_address ?? 'Alamat tidak tersedia',
            'noTeleponToko' => $user->phone ?? '-',
            'waktuPesan' => Carbon::parse($data->created_at)->format('H:i') . ' WIB',
            'kasir' => $data->staff_id ? ($data->staff->fullname ?? 'Kasir') : ($data->user->fullname ?? 'Kasir'),
            'catatan' => $data->note ?? null,
            'paymentStatus' => $data->payment_status,
            'totalProduct' => $totalProduct,
            'flag' => $data->flag ? $data->flag->flag : '',
            'footer' => $setting->printer_custom_footer ?? ''
        ];

        // dd($receiptData);

        return view('receipt', compact('receiptData'));
    }
}
