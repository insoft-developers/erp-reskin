@extends('master')
@section('style')
    <style>
        #landing-pages-table_wrapper .dataTables_scroll {
            overflow-y: auto;
        }

        #landing-pages-table_wrapper .dataTables_scrollBody {
            max-height: 500px;
            /* Sesuaikan dengan tinggi maksimum yang Anda inginkan */
            overflow-y: scroll;
        }

        #order-detail-table {
            border: 1px solid #000;
        }

        #order-detail-table th,
        #order-detail-table td {
            border: 1px solid #000;
        }
    </style>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css"
        integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
@endsection

@section('topstyle')
    <style>
        .dashboard-card {
            align-items: center;
            justify-content: space-between;
            height: 120px;
            color: white;
            font-size: 1.5rem;
            border-radius: 10px;
            padding: 10px;
            position: relative;
        }

        .icon {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 2rem;
        }

        .bg-grid1 {
            background-color: #7787C8;
        }

        .bg-grid2 {
            background-color: #96A6DF;
        }

        .bg-grid3 {
            background-color: #CF827D;
        }

        .bg-grid4 {
            background-color: #AF96C9;
        }

        .bg-grid5 {
            background-color: #8ACFA8;
        }

        .bg-grid6 {
            background-color: #E9A679;
        }

        .text-right {
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('landing-page.index') }}">Manajemen Pesanan</a></li>
                        <li class="breadcrumb-item">List Manajemen Pesanan</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="main-content">
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Manajemen Pesanan</h5>
                                <div class="dropdown">
                                    <button id="action-button" disabled class="btn btn-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Bulk Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" id="change-transaction-status">Ubah
                                                Status Transaksi</a></li>
                                        {{-- <li><a class="dropdown-item" href="#" id="change-payment-status">Ubah
                                                Status Pembayaran</a></li> --}}
                                        {{-- <li><a class="dropdown-item" href="#" id="change-payment-status">Ubah Status
                                                Pembayaran</a></li> --}}
                                        <li><a class="dropdown-item" href="#" id="change-payment-method">Ubah Metode
                                                Pembayaran</a></li>
                                        <li><a class="dropdown-item" href="#" id="change-sync-status">Sync Jurnal</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-3">
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="dashboard-card bg-grid1">
                                            <i class="bi bi-bag icon"></i>
                                            <div class="text-right mt-4">
                                                <div id="omset_penjualan">-</div>
                                                {{-- SUM COLUMN PAID --}}
                                                <div class="fs-5">Omset Penjualan</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="dashboard-card bg-grid2">
                                            <i class="bi bi-cash-stack icon"></i>
                                            <div class="text-right mt-4">
                                                <div id="total_penjualan">-</div>
                                                {{-- SUM ORDER TOTAL --}}
                                                <div class="fs-5">Total Harga Produk Terjual</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="dashboard-card bg-grid3">
                                            <i class="bi bi-wallet2 icon"></i>
                                            <div class="text-right mt-4">
                                                <div id="total_ongkir">-</div>
                                                {{-- SUM SHIPPING --}}
                                                <div class="fs-5">Total Ongkir (+)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="dashboard-card bg-grid4">
                                            <i class="bi bi-bar-chart icon"></i>
                                            <div class="text-right mt-4">
                                                <div id="total_diskon">-</div>
                                                {{-- SUM DISKON --}}
                                                <div class="fs-5">Total Diskon (-)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-2">
                                        <select id="selectRange" class="form-select">
                                            {{-- <option value="">Pilih Range Waktu</option> --}}
                                            <option value="isToday">Hari Ini</option>
                                            <option value="isYesterday">Kemarin</option>
                                            {{-- <option value="isThisWeek">Minggu Ini</option>
                                            <option value="isLastWeek">Minggu Kemarin</option> --}}
                                            <option value="isThisMonth">Bulan Ini</option>
                                            <option value="isLastMonth">Bulan Kemarin</option>
                                            <option value="isThisYear">Tahun Ini</option>
                                            <option value="isLastYear">Tahun Kemarin</option>
                                            <option value="isRangeDate">Range Tanggal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="startDate" class="form-control" placeholder="dd/mm/yyyy"
                                            disabled />
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="endDate" class="form-control" placeholder="dd/mm/yyyy"
                                            disabled />
                                    </div>
                                    <div class="col-md-2">
                                        <select id="search-staff" class="form-select col form-select-sm"
                                            aria-controls="landing-pages-table">
                                            <option value="">Semua Staff</option>
                                            @foreach ($staff as $st)
                                                <option value="{{ $st->id }}">{{ $st->fullname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="search-price-type" class="form-select col form-select-sm"
                                            aria-controls="landing-pages-table">
                                            <option value="">Semua Jenis Harga</option>
                                            <option value="price">Default - Dine In</option>
                                            <option value="price_ta">Takeaway - Delivery</option>
                                            <option value="price_mp">Marketplace</option>
                                            <option value="price_cus">Custom</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-2 d-none">
                                        <select id="branch" class="form-select">
                                            <option value="">Semua Cabang</option>
                                            @foreach ($branch as $br)
                                                <option value="{{ $br->id }}">{{ $br->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="flag" class="form-select">
                                            <option value="">Pilih Flag</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="transaction_status" class="form-select">
                                            <option value="">Semua Status Transaksi</option>
                                            <option value="0">Pending</option>
                                            <option value="1">Process</option>
                                            <option value="2">Cooking/Packing</option>
                                            <option value="3">Shipped</option>
                                            <option value="4">Complete</option>
                                            <option value="5">Canceled</option>
                                            <option value="-2">Void</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="payment_status" class="form-select">
                                            <option value="">Semua Status Pembayaran</option>
                                            <option value="1">Paid</option>
                                            {{-- <option value="0">UnPaid</option> --}}
                                            <option value="-1">Refunded</option>
                                            <option value="-2">Void</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select id="payment_method" class="form-select">
                                            <option value="">Semua Metode Pembayaran</option>
                                            @foreach ($paymentMethods as $br)
                                                <option value="{{ $br }}">{{ $br }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button id="filterButton" type="submit" id="filterData"
                                            class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <div id="landing-pages-table_filter" class="dataTables_filter">
                                            <label>
                                                Pencarian Pesanan:
                                            </label>
                                            <div class="row gap-2">
                                                <input type="search" id="search-key"
                                                    class="form-control col form-control-sm"
                                                    placeholder="Nama / Kode Transaksi / Produk"
                                                    aria-controls="landing-pages-table">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="min-width: 300px; overflow-y: auto" class="mt-3">
                                    <table class="table table-striped border" id="landing-pages-table">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select-all"></th>
                                                {{-- <th>No</th> --}}
                                                <th style='min-width: 150px; text-align: center;'>ACTION</th>
                                                <th style='min-width: 150px; text-align: left;'>SYNC JURNAL</th>
                                                <th style='min-width: 150px; text-align: left;'>PRICE TYPE</th>
                                                <th style='min-width: 200px; text-align: left;'>TANGGAL / WAKTU</th>
                                                <th style='min-width: 200px; text-align: left;'>KODE TRANSAKSI</th>
                                                <th style='min-width: 200px; text-align: left;'>FLAG</th>
                                                <th style='min-width: 400px; text-align: left;'>LIHAT PESANAN</th>
                                                <th style='min-width: 250px; text-align: left;'>DATA KONSUMEN</th>
                                                <th style='min-width: 150px; text-align: left;'>STATUS TRANSAKSI</th>
                                                <th style='min-width: 150px; text-align: left;'>STATUS PEMBAYARAN</th>
                                                <th style='min-width: 150px; text-align: left;'>DISKON (-)</th>
                                                <th style='min-width: 150px; text-align: left;'>PAJAK (+)</th>
                                                <th style='min-width: 150px; text-align: left;'>BIAYA KIRIM (+)</th>
                                                <th style='min-width: 250px; text-align: left;'>SUB TOTAL PESANAN</th>
                                                <th style='min-width: 250px; text-align: left;'>TOTAL PESANAN</th>
                                                <th style='min-width: 150px; text-align: left;'>METODE BAYAR</th>
                                                <th style='min-width: 150px; text-align: left;'>NOMOR MEJA</th>
                                                <th style='min-width: 200px; text-align: left;'>NAMA CABANG</th>
                                                <th style='min-width: 250px; text-align: left;'>NAMA STAFF</th>
                                                <th style='min-width: 150px; text-align: left;'>PROCESSING</th>
                                                <th style='min-width: 150px; text-align: left;'>FOLLOW UP</th>
                                                <th style='min-width: 150px; text-align: left;'>UPSELLING</th>
                                                <th style='min-width: 150px; text-align: left;'>CUSTOMER SERVICE</th>

                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="change-transaction-status-modal" tabindex="-1"
        aria-labelledby="change-transaction-status-label" aria-hidden="true">
        <form class="modal-dialog" id="transaction-status-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="change-transaction-status-label">Ubah Status Transaksi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="penjualans1[]" id="penjualans1" value="" />
                    <select id="transaction_status_change" class="form-select">
                        <option value="">Status Transaksi</option>
                        <option value="0">Pending</option>
                        <option value="1">Process</option>
                        <option value="2">Cooking/Packing</option>
                        <option value="3">Shipped</option>
                        <option value="4">Complete</option>
                        <option value="5">Canceled</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keluar</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="change-payment-status-modal" tabindex="-1"
        aria-labelledby="change-payment-status-label" aria-hidden="true">
        <form class="modal-dialog" id="payment-status-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="change-payment-status-label">Ubah Status Pembayaran</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="penjualans2[]" id="penjualans2" value="" />
                    <select id="payment_status_change" class="form-select">
                        <option value="">Status Pembayaran</option>
                        <option value="1">Paid</option>
                        {{-- <option value="0">UnPaid</option> --}}
                        <option value="-1">Refunded</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keluar</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="change-payment-method-modal" tabindex="-1" aria-labelledby="payment-method-label"
        aria-hidden="true">
        <form class="modal-dialog" id="payment-method-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="payment-method-label">Ubah Metode Pembayaran</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="penjualans3[]" id="penjualans3" value="" />
                    <select id="payment_method_change" class="form-select">
                        <option value="">Metode Pembayaran</option>
                        @foreach ($paymentMethods as $br)
                            <option value="{{ $br }}">{{ $br }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keluar</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="change-sync-status-modal" tabindex="-1" aria-labelledby="change-sync-status-label"
        aria-hidden="true">
        <form class="modal-dialog" id="sync-status-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="change-sync-status-label">Ubah Status Sinkronisasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="penjualans4[]" id="penjualans4" value="" />
                    <select id="sync_status_change" class="form-select">
                        <option value="">Sinkronisasi Status</option>
                        <option value="1">Sudah</option>
                        <option value="0">Belum</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keluar</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailModalLabel">Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="order-detail-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="order-detail-body">
                            <!-- Data akan diisi dengan JavaScript -->
                        </tbody>
                        <tfoot id="order-summary">
                            <!-- Summary akan diisi dengan JavaScript -->
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="strukCheckerBtn">Struk Checker</button>
                    <button type="button" class="btn btn-danger" id="strukConsumentBtn">Struk Konsumen</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var tempSearchKey = '';
        var tempSearchStaff = '';

        $(document).ready(function() {
            $(document).on('change', '#selectRange', function() {
                if ($(this).val() == 'isRangeDate') {
                    $('#startDate').prop('disabled', false);
                    $('#endDate').prop('disabled', false);
                } else {
                    $('#startDate').prop('disabled', true);
                    $('#endDate').prop('disabled', true);
                }
            })

            $.ajax({
                url: '/v1/get-list-flag',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var flagSelect = $('#flag');
                    data.forEach(function(flag) {
                        flagSelect.append(new Option(flag.flag, flag.id));
                    });
                },
                error: function() {
                    alert('Gagal memuat data flag.');
                }
            });

            var table = $('#landing-pages-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Blfrtip',

                ajax: '{{ route('manajemen-data.data') }}',
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="row-checkbox" value="' + row
                                .DT_RowIndex + '">';
                        }
                    },
                    // {
                    //     data: null, // This will be replaced by rowCallback
                    //     orderable: false,
                    //     searchable: false,
                    //     name: 'DT_RowIndex'
                    // },
                    {
                        data: 'opsi',
                        name: 'opsi',
                        orderable: false
                    },
                    {
                        data: 'sync_status',
                        name: 'sync_status',
                        orderable: false
                    },
                    {
                        data: 'price_type',
                        name: 'price_type',
                        orderable: false
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: true
                    },
                    {
                        data: 'reference',
                        name: 'reference',
                        orderable: true
                    },
                    {
                        data: 'flag',
                        name: 'flag',
                        orderable: true
                    },
                    {
                        data: 'struk',
                        name: 'struk',
                        orderable: false
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        orderable: false
                    },
                    {
                        data: 'transaction_status',
                        name: 'transaction_status',
                        orderable: false
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        orderable: false
                    },
                    // {
                    //     data: 'detail_order',
                    //     name: 'detail_order',
                    //     orderable: false
                    // },
                    {
                        data: 'diskon',
                        name: 'diskon',
                        orderable: true
                    },
                    {
                        data: 'tax',
                        name: 'tax',
                        orderable: true
                    },
                    {
                        data: 'shipping_cost',
                        name: 'shipping_cost',
                        orderable: true
                    },
                    {
                        data: 'order_total',
                        name: 'order_total',
                        orderable: true
                    },
                    {
                        data: 'total_order',
                        name: 'total_order',
                        orderable: true
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        orderable: true
                    },
                    {
                        data: 'desk',
                        name: 'desk',
                        orderable: false
                    },
                    {
                        data: 'branch',
                        name: 'branch',
                        orderable: false
                    },
                    {
                        data: 'staff',
                        name: 'staff',
                        orderable: false
                    },

                    {
                        data: 'processing',
                        name: 'processing',
                        orderable: false
                    },
                    {
                        data: 'followup',
                        name: 'followup',
                        orderable: false
                    },
                    {
                        data: 'upselling',
                        name: 'upselling',
                        orderable: false
                    },
                    {
                        data: 'cs',
                        name: 'cs',
                        orderable: false
                    }
                ],
                order: [
                    [4, 'desc']
                ],
                // rowCallback: function(row, data, index) {
                //     var pageInfo = table.page.info();
                //     $('td:eq(1)', row).html(pageInfo.start + index +
                //         1); // Set the number in the second column
                // },
                scrollY: '500px', // Sesuaikan tinggi maksimum yang diinginkan
                scrollCollapse: true,
                paging: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                initComplete: function() {
                    $('#selectRange').val('isThisMonth'); // Set nilai dropdown ke 'isThisMonth'
                    onFilter(''); // Panggil fungsi filter untuk menerapkan filter awal
                }
            });

            $.ajax({
                    url: '/manajemen-pesanan/cart',
                    type: 'GET',
                    dataType: 'json',
                })
                .done(function(data) {
                    var result = data.data;

                    $('#omset_penjualan').text(result.omset_penjualan);
                    $('#total_penjualan').text(result.total_penjualan);
                    $('#total_ongkir').text(result.total_ongkir);
                    $('#total_diskon').text(result.total_diskon);
                })
                .fail(function() {
                    alert('Load data failed.');
                });

            function onFilter() {
                var selectedRange = $('#selectRange').val();
                var startDate = $('#startDate').val() ? $('#startDate').val() + ' 00:00:00' : null;
                var endDate = $('#endDate').val() ? $('#endDate').val() + ' 23:59:59' : null;
                var branch = $('#branch').val();
                var staff = $('#search-staff').val();
                var transactionStatus = $('#transaction_status').val();
                var paymentStatus = $('#payment_status').val();
                var paymentMethod = $('#payment_method').val();
                var flag = $('#flag').val(); // Tambahkan ini
                var price_type = $('#search-price-type').val();

                // Prepare data for AJAX request
                var requestData = {
                    selected_range: selectedRange
                };

                if (selectedRange === 'isRangeDate') {
                    requestData.startDate = startDate;
                    requestData.endDate = endDate;
                }

                if (tempSearchKey) {
                    requestData.keyword = tempSearchKey
                }

                if (staff) {
                    requestData.staff = staff
                }

                if (branch) {
                    requestData.branch = branch;
                }

                if (transactionStatus) {
                    requestData.transaction_status = transactionStatus;
                }

                if (paymentStatus) {
                    requestData.payment_status = paymentStatus;
                }

                if (paymentMethod) {
                    requestData.payment_method = paymentMethod;
                }

                if (flag) { // Tambahkan ini
                    requestData.flag = flag;
                }

                if (price_type) {
                    requestData.price_type = price_type;
                }

                const queryString = toQueryString(requestData);
                // Perform AJAX request
                table.ajax.url('/manajemen-pesanan/data?' + queryString).load();

                $.ajax({
                        url: '/manajemen-pesanan/cart',
                        type: 'GET',
                        data: queryString,
                        dataType: 'json',
                    })
                    .done(function(data) {
                        var result = data.data;

                        $('#omset_penjualan').text(result.omset_penjualan);
                        $('#total_penjualan').text(result.total_penjualan);
                        $('#total_ongkir').text(result.total_ongkir);
                        $('#total_diskon').text(result.total_diskon);
                    })
                    .fail(function() {
                        alert('Load data failed.');
                    });
            }

            // Function to delay the search
            var delayTimer;
            $('#search-key').on('keyup', function() {
                clearTimeout(delayTimer);
                tempSearchKey = $(this).val();
                delayTimer = setTimeout(function() {
                    onFilter();
                }, 1000); // 1 second delay
            });

            table.on('change', 'input.row-checkbox', function() {
                var selectedRows = table.$('input.row-checkbox:checked').length;
                $('#action-button').prop('disabled', selectedRows === 0);
            });

            function toggleActionButton() {
                var selectedRows = table.$('input.row-checkbox:checked').length;
                $('#action-button').prop('disabled', selectedRows === 0);
            }

            $('#select-all').on('click', function() {
                var rows = table.rows({
                    'search': 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
                toggleActionButton();
            });

            // action button listener
            $('#change-transaction-status').on('click', function() {
                var selectedRows = [];
                table.$('input.row-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                $('#penjualans1').val(selectedRows)
                $('#change-transaction-status-modal').modal('show');
            });

            // action button listener
            function formatCurrency(value) {
                return 'Rp ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').replace('.', ',');
            }

            $(document).on('click', '.view-struk-button', function() {
                var penjualanId = $(this).attr('penjualan-id');
                axios({
                    method: 'GET',
                    url: '/v1/show-order-detail?reference=' + penjualanId
                }).then((res) => {
                    if (res.data.status) {
                        var data = res.data.data;
                        var products = data.products;
                        var tbody = '';
                        var totalSubtotal = 0;

                        products.forEach(product => {
                            var productName = product.product.name;
                            var productNote = product.note;
                            var productQuantity = product.quantity;
                            var productPrice = product.price;
                            var productSubtotal = productPrice * productQuantity;

                            totalSubtotal += productSubtotal;
                            product.variant.forEach(variant => {
                                totalSubtotal += variant.price * variant.quantity
                            })

                            tbody +=
                                `<tr>
                <td><div>${productName} (${productQuantity})</div><div style="color: #888; font-size: 12px">Note: ${productNote}</div>`;

                            if (product.variant.length > 0) {
                                tbody += `<ul>`;
                                product.variant.forEach(variant => {

                                    if (variant.quantity > 0) {
                                        tbody +=
                                            `<li><div>${variant.variant.varian_name ?? '-'} (${variant.quantity})</div>`;
                                        if (variant.note) {
                                            tbody +=
                                                `<div style="color: #888; font-size: 12px">Note: ${variant.note}</div>`
                                        }

                                        tbody += `</li>`
                                    }
                                });
                                tbody += `</ul>`;
                            }

                            tbody += `</td>
                <td>
                    <div>${formatCurrency(productPrice)}</div>`;
                            if (productNote) {
                                tbody += `<div style="opacity: 0">-</div>`
                            }

                            product.variant.forEach(variant => {
                                if (variant.quantity > 0) {
                                    tbody +=
                                        `<div>${formatCurrency(parseFloat(variant.price))}</div>`;
                                    if (variant.note) {
                                        tbody +=
                                            `<div style="color: #888; font-size: 12px; opacity: 0">-</div>`
                                    }
                                }
                            });

                            tbody += `</td>
                <td>
                    <div>${formatCurrency(productSubtotal)}</div>`;
                            if (productNote) {
                                tbody += `<div style="opacity: 0">-</div>`
                            }

                            product.variant.forEach(variant => {
                                if (variant.quantity > 0) {
                                    tbody +=
                                        `<div>${formatCurrency(parseFloat(variant.price) * parseFloat(variant.quantity))}</div>`;
                                    if (variant.note) {
                                        tbody +=
                                            `<div style="color: #888; font-size: 12px; opacity: 0">-</div>`
                                    }
                                }
                            });
                            tbody += `</td>
              </tr>`;
                        });

                        var diskon = data.diskon || 0;
                        var shipping = data.shipping || 0;
                        var tax = data.tax || 0;
                        var total = data.paid;

                        var summary = `
                        <tr>
                            <td colspan="2">Subtotal: ${products.length} PRODUK</td>
                            <td>${formatCurrency(totalSubtotal)}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Diskon:</td>
                            <td>${formatCurrency(diskon)}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Shipping:</td>
                            <td>${formatCurrency(shipping)}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Pajak:</td>
                            <td>${formatCurrency(tax)}</td>
                        </tr>
                        <tr>
                            <td colspan="2">Flag:</td>
                            <td>${data.flag ? data.flag.flag : '-'}</td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Total:</strong></td>
                            <td><strong>${formatCurrency(total)}</strong></td>
                        </tr>`;

                        $('#order-detail-body').html(tbody);
                        $('#order-summary').html(summary);
                        $('#orderDetailModal').modal('show');

                        // Tambahkan event listener pada tombol "Struk Checker"
                        $('#strukCheckerBtn').off('click').on('click', function() {
                            window.open('/pos/print-receipt?reference=' + penjualanId +
                                '&with_checker=true',
                                '_blank');
                        });
                        $('#strukConsumentBtn').off('click').on('click', function() {
                            window.open('/pos/print-receipt?reference=' + penjualanId,
                                '_blank');
                        });
                    } else {
                        alert('Data tidak ditemukan');
                    }
                }).catch((error) => {
                    console.error(error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
            });





            $('#change-payment-status').on('click', function() {
                var selectedRows = [];
                table.$('input.row-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                $('#penjualans2').val(selectedRows)
                $('#change-payment-status-modal').modal('show');
            });
            $('#change-payment-method').on('click', function() {
                var selectedRows = [];
                table.$('input.row-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                $('#penjualans3').val(selectedRows)
                $('#change-payment-method-modal').modal('show');
            });
            $('#change-sync-status').on('click', function() {
                // var selectedRows = [];
                // table.$('input.row-checkbox:checked').each(function() {
                //     selectedRows.push($(this).val());
                // });
                // $('#penjualans4').val(selectedRows)
                // $('#change-sync-status-modal').modal('show');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will syncronize this transaction into journal account ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Synchronize it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        var elem = table.$('input.row-checkbox:checked');
                        var ids = [];
                        elem.map(function() {
                            ids.push($(this).val());
                        });

                        sync(ids);
                    }
                });
            });


            function sync(ids) {
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ route('manajemen-pesanan.bulk.sync-status') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        "ids": ids,
                        "_token": csrf_token
                    },
                    success: function(data) {
                        table.ajax.reload(null, false);
                    }
                });
            }




            // forn listener
            $('#transaction-status-form').on('submit', function(event) {
                event.preventDefault();

                var penjualans = $('#penjualans1').val();
                var status = $('#transaction_status_change').val();

                if (!status) {
                    alert('Silakan Pilih Status Transaksi.');
                    return;
                }

                $.ajax({
                    url: '{{ route('manajemen-pesanan.bulk.update-status') }}', // Ganti dengan route Anda
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        penjualans: penjualans.split(','),
                        status,
                    },
                    success: function(response) {
                        $('#change-transaction-status-modal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: 'Status Transaksi Berhasil Diubah.'
                        })
                    },
                    error: function(xhr) {
                        alert(
                            'Terjadi kesalahan. Silakan coba lagi atau hubungi Trainer lewat Live Chat'
                        );
                    }
                });
            });

            $('#payment-status-form').on('submit', function(event) {
                event.preventDefault();

                var penjualans = $('#penjualans2').val();
                var status = $('#payment_status_change').val();

                if (!status) {
                    alert('Silakan Pilih Status Pembayaran.');
                    return;
                }

                $.ajax({
                    url: '{{ route('manajemen-pesanan.bulk.payment-status') }}', // Ganti dengan route Anda
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        penjualans: penjualans.split(','),
                        status
                    },
                    success: function(response) {
                        $('#change-payment-status-modal').modal('hide');
                        table.ajax.reload();

                        if (response.status == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: 'Status Pembayaran Berhasi Diubah'
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            })
                        }
                    },
                    error: function(xhr) {
                        alert(
                            'Terjadi kesalahan. Silakan coba lagi atau hubungi Trainer lewat Live Chat.'
                        );
                    }
                });
            });

            $('#payment-method-form').on('submit', function(event) {
                event.preventDefault();

                var penjualans = $('#penjualans3').val();
                var status = $('#payment_method_change').val();

                if (!status) {
                    alert('Silakan Pilih Metode Pembayaran.');
                    return;
                }

                $.ajax({
                    url: '{{ route('manajemen-pesanan.bulk.payment-method') }}', // Ganti dengan route Anda
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        penjualans: penjualans.split(','),
                        status: status
                    },
                    success: function(response) {
                        $('#change-payment-method-modal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message
                        })
                    },
                    error: function(xhr) {
                        alert(
                            'Terjadi kesalahan. Silakan coba lagi atau hubungi Trainer lewat Live Chat'
                        );
                    }
                });
            });

            $('#sync-status-form').on('submit', function(event) {
                event.preventDefault();

                var penjualans = $('#penjualans4').val();
                var status = $('#sync_status_change').val();

                if (!status) {
                    alert('Silakan pilih salah satu status sinkronisasi.');
                    return;
                }

                $.ajax({
                    url: '{{ route('manajemen-pesanan.bulk.sync-status') }}', // Ganti dengan route Anda
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        penjualans: penjualans.split(','),
                        status: status
                    },
                    success: function(response) {
                        $('#change-sync-status-modal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message
                        })
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });

            function toQueryString(obj) {
                const params = new URLSearchParams(obj);
                return params.toString();
            }

            $('#filterButton').click(function() {
                onFilter()
            });
        });


        function syncData(id) {
            Swal.fire({
                title: 'Ubah UnSync Menjadi Sync?',
                text: 'Aksi ini akan membuat singkronisasi jurnal. ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lakukan Singkronisasi!',
                cancelButtonText: 'Tidak, Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    sync_process(id);
                }
            });
        }


        function sync_process(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('manajemen-pesanan.single.sync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {

                    reloadTable();

                }
            });
        }


        function refund(id) {
            Swal.fire({
                title: 'Ubah Paid Menjadi Refunded?',
                text: 'Refund Transaksi akan menghapus jurnal transaksi dan pendapatan tapi tidak akan mengembalikan stok. Jika ingin stock di kembalikan gunakan fitur penyesuaian. ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah Ke Refund!',
                cancelButtonText: 'Tidak, Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    refund_process(id);
                }
            });
        }


        function refund_process(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('manajemen-pesanan.single.refund') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.status) {
                        reloadTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: data.message
                        })
                    } else {
                        Swal.fire({
                            icon: 'danger',
                            title: 'Failed',
                            text: data.message
                        })
                    }


                }
            });
        }


        function unsync(id) {
            Swal.fire({
                title: 'Ubah Sync Menjadi UnSync?',
                text: 'Unsync Akan Menghapus Singkronisasi Jurnal. ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Singkronisasi!',
                cancelButtonText: 'Tidak, Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    unsync_process(id);
                }
            });
        }


        function unsync_process(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('manajemen-pesanan.single.unsync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.status) {
                        reloadTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: data.message
                        })
                    } else {
                        Swal.fire({
                            icon: 'danger',
                            title: 'Failed',
                            text: data.message
                        })
                    }


                }
            });
        }


        function voidd(id) {
            Swal.fire({
                title: 'Ubah Paid Menjadi VOID?',
                text: 'VOID Transaksi akan mengembalikan stok dan menghapus jurnal transaksi. Aksi ini tidak bisa dibatalkan ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah Ke VOID!',
                cancelButtonText: 'Tidak, Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    void_process(id);
                }
            });
        }

        function void_process(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('manajemen-pesanan.single.void') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.status) {
                        reloadTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: data.message
                        })
                    } else {
                        Swal.fire({
                            icon: 'danger',
                            title: 'Failed',
                            text: data.message
                        })
                    }


                }
            });
        }



        function payData(id) {
            Swal.fire({
                title: 'Ubah UnPaid Menjadi Paid?',
                text: 'Status Pembayaran Akan Berubah Menjadi "PAID"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah ke PAID!',
                cancelButtonText: 'Tidak, Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    paid_process(id);
                }
            });
        }


        function paid_process(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('manajemen-pesanan.single.paid') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.status) {
                        Swal.fire({
                            icon: "success",
                            title: "Notice!...",
                            html: data.message,
                            footer: ''
                        })

                        reloadTable();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Notice!...",
                            html: data.message,
                            footer: ''
                        })
                    }

                }
            });
        }

        function reloadTable() {
            var tb = $('#landing-pages-table').DataTable();
            tb.ajax.reload(null, false);
        }
    </script>
@endsection
