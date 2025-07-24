@extends(isset($userKey) ? 'master-preview' : 'master')

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
    @if (!$userKey)
        <main class="nxl-container">
            <div class="nxl-content">
                <!-- [ page-header ] start -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('report') }}">Laporan</a></li>
                            <li class="breadcrumb-item">Penjualan Per Product</li>
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
                            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">


                            </div>
                        </div>
                        <div class="d-md-none d-flex align-items-center">
                            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                <i class="feather-align-right fs-20"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- [ page-header ] end -->
                <!-- [ Main Content ] start -->
                <div class="main-content">
                    <div class="row">
                        <!-- [Leads] start -->
                        <div class="col-xxl-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <h5 class="card-title">Penjualan Per Product</h5>
                                </div>
                                <div class="card-body custom-card-action p-3">
                                    <div class="row mb-4">
                                        <div class="col">
                                            <div class="input-group">
                                                <select class="form-select" id="select_date" name="date">
                                                    <option value="isToday">Hari ini</option>
                                                    <option value="isYesterday">Kemarin</option>
                                                    <option value="isThisMonth" selected>Bulan Ini</option>
                                                    <option value="isLastMonth">Bulan Kemarin</option>
                                                    <option value="isThisYear">Tahun Ini</option>
                                                    <option value="isLastYear">Tahun Kemarin</option>
                                                    <option value="isRangeDate">Custom Range</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col" id="start_date_div" hidden>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                placeholder="dd/mm/yyyy">
                                        </div>
                                        <div class="col" id="end_date_div" hidden>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                placeholder="dd/mm/yyyy">
                                        </div>
                                        {{-- <div class="col">
                                            <select class="form-select" id="expense_category_id" name="expense_category_id">
                                            </select>
                                        </div> --}}
                                        <div class="col">
                                            <div class="input-group">
                                                <button type='submit' class='btn btn-primary me-2' onclick="filter()"><i
                                                        class="feather-filter"></i> Filter</button>
                                                <button type="button" class="btn btn-success me-2" id="export-btn"
                                                    onclick="exportExcelData()"><i class="feather-download"></i> Export
                                                    Xls</button>
                                                <button type="button" class="btn btn-warning" id="export-btn"
                                                    onclick="exportPdfData()"><i class="feather-download"></i> Export
                                                    Pdf</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid1"
                                                style="background-color: #4CAF50 !important;">
                                                <i class="bi bi-bag icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="omset_penjualan">-</div>
                                                    {{-- SUM COLUMN PAID --}}
                                                    <div class="fs-5">Omset Penjualan</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid1"
                                                style="background-color: #64B5F6 !important;">
                                                <i class="bi bi-bag icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="jumlah_terjual">-</div>
                                                    <div class="fs-5">Jumlah Terjual</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid3"
                                                style="background-color: #F44336 !important;">
                                                <i class="bi bi-wallet2 icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="biaya">-</div>
                                                    <div class="fs-5">Biaya Biaya</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid3"
                                                style="background-color: #E57373 !important;">
                                                <i class="bi bi-wallet2 icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="total_ongkir">-</div>
                                                    {{-- SUM SHIPPING --}}
                                                    <div class="fs-5">Total Ongkir (+)</div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid2"
                                                style="background-color: #42A5F5 !important;">
                                                <i class="bi bi-cash-stack icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="total_harga_produk_terjual">-</div>
                                                    <div class="fs-5">Total Harga Produk Terjual</div>
                                                </div>
                                            </div>
                                        </div> --}}
                                         <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid2"
                                                style="background-color: orange  !important;">
                                                <i class="bi bi-cash-stack icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="total_harga_produk_terjual">-</div>
                                                    <div class="fs-5">Total Pajak (+)</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid4"
                                                style="background-color: #B39DDB !important;">
                                                <i class="bi bi-bar-chart icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="total_diskon">-</div>
                                                    {{-- SUM DISKON --}}
                                                    <div class="fs-5">Total Diskon (-)</div>
                                                </div>
                                            </div>
                                        </div>

                                       
                                       
                                       
                                        <div class="col-md-4 d-none mb-3">
                                            <div class="dashboard-card bg-grid4"
                                                style="background-color: #7E57C2 !important;">
                                                <i class="bi bi-bar-chart icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="hpp">-</div>
                                                    <div class="fs-5">Harga Pokok Penjualan</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-none mb-3">
                                            <div class="dashboard-card bg-grid5"
                                                style="background-color: #81C784 !important;">
                                                <i class="bi bi-graph-up-arrow icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="laba_rugi_bersih">-</div>
                                                    <div class="fs-5">Laba Rugi</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-none mb-3">
                                            <div class="dashboard-card bg-grid6"
                                                style="background-color: #FFB74D !important;">
                                                {{-- ICON X --}}
                                                <i class="bi bi-x icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="roas">-</div>
                                                    <div class="fs-5">ROAS</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="mtop30"></div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama</th>
                                                    <th>Jumlah Terjual</th>
                                                    <th>Harga Jual Produk</th>
                                                    <th>Omset Penjualan</th>
                                                    <th>HPP Produk</th>
                                                    <th>HPP Total</th>
                                                    <th>Margin Kotor</th>
                                                    <th>Persentase Margin</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- [Recent Orders] end -->
                        <!-- [Table] start -->
                        <!-- [Table] end -->
                    </div>

                </div>
                <!-- [ Main Content ] end -->

            </div>
        </main>
    @else
        <div class="row">
            <!-- [Leads] start -->
            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    @if ($from === 'desktop')
                        <div class="card-header">
                            <h5 class="card-title">Laporan Penjualan</h5>
                        </div>
                    @endif
                    <div class="card-body custom-card-action p-3">
                        <div class="row mb-4">
                            <div class="col">
                                <div class="input-group">
                                    <select class="form-select" id="select_date" name="date">
                                        <option value="isToday">Hari ini</option>
                                        <option value="isYesterday">Kemarin</option>
                                        <option value="isThisMonth" selected>Bulan Ini</option>
                                        <option value="isLastMonth">Bulan Kemarin</option>
                                        <option value="isThisYear">Tahun Ini</option>
                                        <option value="isLastYear">Tahun Kemarin</option>
                                        <option value="isRangeDate">Custom Range</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col" id="start_date_div" hidden>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    placeholder="dd/mm/yyyy">
                            </div>
                            <div class="col" id="end_date_div" hidden>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    placeholder="dd/mm/yyyy">
                            </div>
                            {{-- <div class="col">
                                <select class="form-select" id="expense_category_id" name="expense_category_id">
                                </select>
                            </div> --}}
                            <div class="col-12 col-md mt-2 mt-md-0">
                                <div class="input-group">
                                    <button type='submit' class='btn btn-primary me-2' onclick="filter()"><i
                                            class="feather-filter"></i> Filter</button>
                                    <button type="button" class="btn btn-success me-2" id="export-btn"
                                        onclick="exportExcelData()"><i class="feather-download"></i> Export
                                        Xls</button>
                                    <button type="button" class="btn btn-warning" id="export-btn"
                                        onclick="exportPdfData()"><i class="feather-download"></i> Export
                                        Pdf</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid1" style="background-color: #4CAF50 !important;">
                                    <i class="bi bi-bag icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="omset_penjualan">-</div>
                                        {{-- SUM COLUMN PAID --}}
                                        <div class="fs-5">Omset Penjualan</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid3" style="background-color: #E57373 !important;">
                                    <i class="bi bi-wallet2 icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="total_ongkir">-</div>
                                        {{-- SUM SHIPPING --}}
                                        <div class="fs-5">Total Ongkir (+)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid4" style="background-color: #B39DDB !important;">
                                    <i class="bi bi-bar-chart icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="total_diskon">-</div>
                                        {{-- SUM DISKON --}}
                                        <div class="fs-5">Total Diskon (-)</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid1" style="background-color: #64B5F6 !important;">
                                    <i class="bi bi-bag icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="jumlah_terjual">-</div>
                                        <div class="fs-5">Jumlah Terjual</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid2" style="background-color: #42A5F5 !important;">
                                    <i class="bi bi-cash-stack icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="total_harga_produk_terjual">-</div>
                                        <div class="fs-5">Total Harga Produk Terjual</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid3" style="background-color: #F44336 !important;">
                                    <i class="bi bi-wallet2 icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="biaya">-</div>
                                        <div class="fs-5">Biaya Biaya</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid4" style="background-color: #7E57C2 !important;">
                                    <i class="bi bi-bar-chart icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="hpp">-</div>
                                        <div class="fs-5">Harga Pokok Penjualan</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid5" style="background-color: #81C784 !important;">
                                    <i class="bi bi-graph-up-arrow icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="laba_rugi_bersih">-</div>
                                        <div class="fs-5">Laba Rugi Bersih</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="dashboard-card bg-grid6" style="background-color: #FFB74D !important;">
                                    {{-- ICON X --}}
                                    <i class="bi bi-x icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="roas">-</div>
                                        <div class="fs-5">ROAS</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col text-center">
                                <h5>TOTAL PAJAK: <span id="total_pajak"></span></h5>
                            </div>
                        </div>


                        <div class="mtop30"></div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Jumlah Terjual</th>
                                        <th>Harga Jual Produk</th>
                                        <th>Omset Penjualan</th>
                                        <th>HPP Produk</th>
                                        <th>HPP Total</th>
                                        <th>Margin Kotor</th>
                                        <th>Persentase Margin</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <!-- [Recent Orders] end -->
            <!-- [Table] start -->
            <!-- [Table] end -->
        </div>
    @endif

    {{-- MODALS --}}
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-ce">
        <div class="modal-dialog modal-lg" id="content-modal-ce">

        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            init_table();
            getCategoryExpense('', 'isThisMonth');
            initTotalPenjualan('isThisMonth');
        });


        $(document).on('input', '#searchData', function() {
            // filter();
        })

        $(document).on('change', '#select_date', function() {
            var value = $(this).val();
            if (value == 'isRangeDate') {
                $('#start_date_div').removeAttr('hidden');
                $('#end_date_div').removeAttr('hidden');
            } else {
                $('#start_date_div').attr('hidden', true);
                $('#end_date_div').attr('hidden', true);

                // filter();
                getCategoryExpense();
            }
        });

        $(document).on('change', '#start_date', function() {
            var end_date = $('#end_date').val();
            if (end_date != '') {
                getCategoryExpense();
            }
        })

        $(document).on('change', '#end_date', function() {
            getCategoryExpense();
        })

        function initTotalPenjualan(date = '', start_date = '', end_date = '') {
            var filterData = {
                'selected_range': date,
                'startDate': start_date,
                'endDate': end_date,
            };

            $.ajax({
                    url: "{{ !$userKey ? url('manajemen-pesanan/cart') : url('/api/manajemen-pesanan/cart') . '?user_key=' . $userKey }}",
                    type: 'GET',
                    dataType: 'json',
                    data: filterData
                })
                .done(function(data) {
                    var result = data.data;

                    $('#omset_penjualan').text(result.omset_penjualan);
                    $('#total_ongkir').text(result.total_ongkir);
                    $('#total_diskon').text(result.total_diskon);
                })
                .fail(function() {
                    alert('Load data failed.');
                });
        }

        function exportExcelData() {
            var keyword = $('#searchData').val();
            var date = $('#select_date').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var expense_category_id = $('#expense_category_id').val();

            var url =
                "{{ !$userKey ? route('laporan.penjualan.exportExcel') : url('/api/laporan/penjualan/export-excel') }}?date=" +
                date + "&start_date=" + start_date +
                "&end_date=" + end_date + "&expense_category_id=" + expense_category_id + "?user_key=" +
                "{{ $userKey ?? 'null' }}";
            // window.location.href = url;

            window.open(url);
        }

        function exportPdfData() {
            var keyword = $('#searchData').val();
            var date = $('#select_date').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var expense_category_id = $('#expense_category_id').val();

            var url =
                "{{ !$userKey ? route('laporan.penjualan.exportPdf') : url('/api/laporan/penjualan/export-pdf') }}?date=" +
                date + "&start_date=" + start_date +
                "&end_date=" + end_date + "&expense_category_id=" + expense_category_id + "&user_key=" +
                "{{ $userKey ?? 'null' }}";
            window.open(url);
        }

        function filter() {
            var keyword = $('#searchData').val();
            var date = $('#select_date').val();
            console.log(date)
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var expense_category_id = $('#expense_category_id').val();

            init_table(keyword, date, start_date, end_date, expense_category_id);
            initTotalPenjualan(date, start_date, end_date);
        }

        function init_table(keyword = '', date = '', start_date = '', end_date = '', expense_category_id = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#data-table');
            table.destroy();

            var filterData = {
                'keyword': keyword,
                'date': date,
                'start_date': start_date,
                'end_date': end_date,
                'expense_category_id': expense_category_id
            };

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Blfrtip',
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }, ],

                ajax: {
                    type: "GET",
                    url: "{{ !$userKey ? route('laporan.penjualan.data') : url('/api/laporan/penjualan/data') . '?user_key=' . $userKey }}",
                    data: filterData
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'jumlah_terjual',
                        name: 'jumlah_terjual'
                    },
                    {
                        data: 'harga_jual',
                        name: 'harga_jual'
                    },
                    {
                        data: 'total_harga_produk_terjual',
                        name: 'total_harga_produk_terjual'
                    },
                    {
                        data: 'hpp_produk',
                        name: 'hpp_produk'
                    },
                    {
                        data: 'hpp',
                        name: 'hpp'
                    },
                    {
                        data: 'margin_kotor',
                        name: 'margin_kotor'
                    },
                    {
                        data: 'persentase_margin',
                        name: 'persentase_margin'
                    },
                ],
            });

            $.ajax({
                    url: "{{ !$userKey ? route('laporan.penjualan.chart-regular') : url('/api/laporan/penjualan/chart-regular') . '?user_key=' . $userKey }}",
                    type: 'GET',
                    data: filterData,
                    dataType: 'json',
                })
                .done(function(data) {
                    console.log(data);
                    var jumlah_terjual = data.jumlah_terjual;
                    var total_harga_produk_terjual = data.total_harga_produk_terjual;
                    var biaya = data.biaya;
                    var laba_rugi_bersih = data.laba_rugi_bersih;
                    var hpp = data.hpp;
                    var roas = data.roas;

                    $('#jumlah_terjual').text(jumlah_terjual);
                    // $('#total_harga_produk_terjual').text(total_harga_produk_terjual);
                    $('#total_harga_produk_terjual').text(data.total_pajak);
                    $('#biaya').text(biaya);
                    // $('#hpp').text(hpp);
                    // $('#laba_rugi_bersih').text(laba_rugi_bersih);
                    // $('#roas').text(roas);
                    $('#total_pajak').text(data.total_pajak);
                })
                .fail(function() {
                    alert('Load data failed.');
                });
        }

        function getCategoryExpense() {
            var date = $('#select_date').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            var filterData = {
                'date': date,
                'start_date': start_date,
                'end_date': end_date,
                'user_key': "{{ $userKey ?? 'null' }}"
            };

            $.ajax({
                    url: "{{ !$userKey ? route('laporan.penjualan.categoryExpense') : route('preview.laporan.penjualan.categoryExpense') }}",
                    type: 'GET',
                    data: filterData,
                    dataType: 'json',
                })
                .done(function(data) {
                    var option = '<option value="" selected>Default Semua Kategori Pengeluaran</option>';
                    $.each(data, function(key, value) {
                        option += '<option value="' + value.id + '">' + value.name + '</option>';
                    });

                    // $('#expense_category_id').html(option);
                })
                .fail(function() {
                    alert('Load data failed.');
                });
        }
    </script>
@endsection
