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

        .spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
    @if (!$userKey)
        <main class="nxl-container" style="padding-bottom: 100px;">
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
                                        <div class="col-xl-2">
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

                                        <div class="col-xl-2">
                                            <div class="input-group">
                                                <select class="form-select" id="select_staff" name="staff_id">
                                                    <option value="">Semua Staf</option>
                                                    @foreach ($staffs as $staff)
                                                        <option value="{{ $staff->id }}">{{ $staff->fullname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-2">
                                            <div class="input-group">
                                                <select class="form-select" id="select_flag" name="flag_id">
                                                    <option value="">Semua Flag</option>
                                                    @foreach ($flags as $flag)
                                                        <option value="{{ $flag->id }}">{{ $flag->flag }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-2">
                                            <select id="payment_method" class="form-select">
                                                <option value="">Semua Metode Pembayaran</option>
                                                @foreach ($paymentMethods as $br)
                                                    <option value="{{ $br }}">{{ $br }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-xl-2">
                                            <select id="search-price-type" class="form-select col form-select-sm"
                                                aria-controls="landing-pages-table">
                                                <option value="">Semua Jenis Harga</option>
                                                <option value="price">Default - Dine In</option>
                                                <option value="price_ta">Takeaway - Delivery</option>
                                                <option value="price_mp">Marketplace</option>
                                                <option value="price_cus">Custom</option>
                                            </select>
                                        </div>

                                        <div class="col" id="start_date_div" hidden>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                placeholder="dd/mm/yyyy">
                                        </div>
                                        <div class="col" id="end_date_div" hidden>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                placeholder="dd/mm/yyyy">
                                        </div>
                                    </div>

                                    <div class="row my-4">
                                        <div class="col-12">
                                            <div style="display: flex; gap: 10px;">
                                                <button type='submit' class='btn btn-primary' onclick="filter()"><i
                                                        class="feather-filter"></i> Filter</button>
                                                <button type="button" class="btn btn-success" id="export-btn"
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

                                        <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid4"
                                                style="background-color: #7E57C2 !important;">
                                                <i class="bi bi-bar-chart icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="hpp">-</div>
                                                    <div class="fs-5">Harga Pokok Penjualan</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="dashboard-card bg-grid5"
                                                style="background-color: #81C784 !important;">
                                                <i class="bi bi-graph-up-arrow icon"></i>
                                                <div class="text-right mt-4">
                                                    <div id="laba_rugi_bersih">-</div>
                                                    <div class="fs-5">Laba Rugi</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
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

                                    <div style="overflow-x: auto; max-width: 100%;">
                                        {!! $dataTable->table(['width' => '100%', 'class' => 'table table-auto table-border']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <div class="col-xl-2">
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
                            <div class="col-xl-2">
                                <div class="input-group">
                                    <select class="form-select" id="select_staff" name="staff_id">
                                        <option value="">Semua Staf</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->fullname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="input-group">
                                    <select class="form-select" id="select_flag" name="flag_id">
                                        <option value="">Semua Flag</option>
                                        @foreach ($flags as $flag)
                                            <option value="{{ $flag->id }}">{{ $flag->flag }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <select id="payment_method" class="form-select">
                                    <option value="">Semua Metode Pembayaran</option>
                                    @foreach ($paymentMethods as $br)
                                        <option value="{{ $br }}">{{ $br }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xl-2">
                                <select id="search-price-type" class="form-select col form-select-sm"
                                    aria-controls="landing-pages-table">
                                    <option value="">Semua Jenis Harga</option>
                                    <option value="price">Default - Dine In</option>
                                    <option value="price_ta">Takeaway - Delivery</option>
                                    <option value="price_mp">Marketplace</option>
                                    <option value="price_cus">Custom</option>
                                </select>
                            </div>

                            <div class="col" id="start_date_div" hidden>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    placeholder="dd/mm/yyyy">
                            </div>
                            <div class="col" id="end_date_div" hidden>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    placeholder="dd/mm/yyyy">
                            </div>
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
                                <div class="dashboard-card bg-grid1" style="background-color: #64B5F6 !important;">
                                    <i class="bi bi-bag icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="jumlah_terjual">-</div>
                                        <div class="fs-5">Jumlah Terjual</div>
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
                                <div class="dashboard-card bg-grid3" style="background-color: #E57373 !important;">
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
                                <div class="dashboard-card bg-grid2" style="background-color: orange  !important;">
                                    <i class="bi bi-cash-stack icon"></i>
                                    <div class="text-right mt-4">
                                        <div id="total_harga_produk_terjual">-</div>
                                        <div class="fs-5">Total Pajak (+)</div>
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
                                        <div class="fs-5">Laba Rugi</div>
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

                        <div style="overflow-x: auto; max-width: 100%;">
                            {!! $dataTable->table(['width' => '100%', 'class' => 'table table-auto table-border']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('js')
    {!! $dataTable->scripts() !!}
    <script>
        const is_free = {{ $isFree ? 'true' : 'false' }};

        function showSpinners() {
            $('#omset_penjualan').html('<div class="spinner"></div>');
            $('#jumlah_terjual').html('<div class="spinner"></div>');
            $('#biaya').html('<div class="spinner"></div>');
            $('#total_ongkir').html('<div class="spinner"></div>');
            $('#total_harga_produk_terjual').html('<div class="spinner"></div>');
            $('#total_diskon').html('<div class="spinner"></div>');
            $('#hpp').html('<div class="spinner"></div>');
            $('#laba_rugi_bersih').html('<div class="spinner"></div>');
            $('#roas').html('<div class="spinner"></div>');
        }

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

        function filter() {
            var keyword = '';
            var date = $('#select_date').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var expense_category_id = '';
            var staff_id = $('#select_staff').val();
            var payment_method = $('#payment_method').val();
            var flag_id = $('#select_flag').val();
            var price_type = $('#search-price-type').val();

            // Show spinners before making requests
            showSpinners();

            // Refresh DataTable dengan parameter filter
            var table = $('.dataTable').DataTable();
            table.ajax.url("{{ route('laporan.penjualan.advance.index') }}?date=" + date + "&start_date=" + start_date +
                "&end_date=" + end_date + "&staff_id=" + staff_id + "&payment_method=" + payment_method + "&flag_id=" +
                flag_id + "&price_type=" + price_type).load();

            if (!is_free) {
                // Load data from separate endpoints in parallel
                loadBasicData(keyword, date, start_date, end_date, expense_category_id, staff_id, payment_method, flag_id, price_type);
                loadExpensesData(date, start_date, end_date, staff_id, payment_method, flag_id, price_type);
                loadSalesData(date, start_date, end_date, staff_id, payment_method, flag_id, price_type);
                initTotalPenjualan(date, start_date, end_date, staff_id, payment_method, flag_id, price_type);
            }
        }

        function loadBasicData(keyword = '', date = '', start_date = '', end_date = '', expense_category_id = '', staff_id = '', payment_method = '', flag_id = '', price_type = '') {
            var filterData = {
                'keyword': keyword,
                'date': date,
                'start_date': start_date,
                'end_date': end_date,
                'expense_category_id': expense_category_id,
                'staff_id': staff_id,
                'payment_method': payment_method,
                'flag_id': flag_id,
                'price_type': price_type
            };

            $.ajax({
                url: "{{ !$userKey ? route('laporan.penjualan.chart.basic') : url('/api/laporan/penjualan/chart-basic') . '?user_key=' . $userKey }}",
                type: 'GET',
                data: filterData,
                dataType: 'json',
            })
            .done(function(data) {
                $('#jumlah_terjual').text(data.jumlah_terjual);
            })
            .fail(function() {
                $('#jumlah_terjual').text('-');
            });
        }

        function loadExpensesData(date = '', start_date = '', end_date = '', staff_id = '', payment_method = '', flag_id = '', price_type = '') {
            var filterData = {
                'date': date,
                'start_date': start_date,
                'end_date': end_date,
                'staff_id': staff_id,
                'payment_method': payment_method,
                'flag_id': flag_id,
                'price_type': price_type
            };

            $.ajax({
                url: "{{ !$userKey ? route('laporan.penjualan.chart.expenses') : url('/api/laporan/penjualan/chart-expenses') . '?user_key=' . $userKey }}",
                type: 'GET',
                data: filterData,
                dataType: 'json',
            })
            .done(function(data) {
                $('#biaya').text(data.biaya);
            })
            .fail(function() {
                $('#biaya').text('-');
            });
        }

        function loadSalesData(date = '', start_date = '', end_date = '', staff_id = '', payment_method = '', flag_id = '', price_type = '') {
            var filterData = {
                'date': date,
                'start_date': start_date,
                'end_date': end_date,
                'staff_id': staff_id,
                'payment_method': payment_method,
                'flag_id': flag_id,
                'price_type': price_type
            };

            $.ajax({
                url: "{{ !$userKey ? route('laporan.penjualan.chart.sales') : url('/api/laporan/penjualan/chart-sales') . '?user_key=' . $userKey }}",
                type: 'GET',
                data: filterData,
                dataType: 'json',
            })
            .done(function(data) {
                $('#hpp').text(data.hpp);
                $('#laba_rugi_bersih').text(data.laba_rugi_bersih);
                $('#roas').text(data.roas);
                $('#total_harga_produk_terjual').text(data.total_pajak);
            })
            .fail(function() {
                $('#hpp').text('-');
                $('#laba_rugi_bersih').text('-');
                $('#roas').text('-');
                $('#total_harga_produk_terjual').text('-');
            });
        }

        function initTotalPenjualan(date = '', start_date = '', end_date = '', staff_id = '', payment_method = '', flag_id = '', price_type = '') {
            var filterData = {
                'selected_range': date,
                'startDate': start_date,
                'endDate': end_date,
                'staff_id': staff_id,
                'payment_method': payment_method,
                'flag_id': flag_id,
                'price_type': price_type
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
                $('#omset_penjualan').text('-');
                $('#total_ongkir').text('-');
                $('#total_diskon').text('-');
            });
        }

        function exportExcelData() {
            var keyword = '';
            var date = $('#select_date').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var expense_category_id = '';
            var staff_id = $('#select_staff').val();
            var payment_method = $('#payment_method').val();
            var flag_id = $('#select_flag').val();
            var price_type = $('#search-price-type').val();

            // Show loading indicator
            Swal.fire({
                title: 'Memproses Export...',
                text: 'Sedang memproses laporan, mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Use POST request to queue endpoint
            $.ajax({
                url: "{{ route('laporan.penjualan.exportExcelQueue.advance') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    date: date,
                    start_date: start_date,
                    end_date: end_date,
                    expense_category_id: expense_category_id,
                    staff_id: staff_id,
                    payment_method: payment_method,
                    flag_id: flag_id,
                    price_type: price_type
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    var errorMessage = 'Terjadi kesalahan saat memproses export.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function exportPdfData() {
            var keyword = '';
            var date = $('#select_date').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var expense_category_id = '';
            var staff_id = $('#select_staff').val();
            var payment_method = $('#payment_method').val();
            var flag_id = $('#select_flag').val();
            var price_type = $('#search-price-type').val();

            // Show loading indicator
            Swal.fire({
                title: 'Memproses Export...',
                text: 'Sedang memproses laporan, mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Use POST request to queue endpoint
            $.ajax({
                url: "{{ route('laporan.penjualan.exportPdfQueue.advance') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    date: date,
                    start_date: start_date,
                    end_date: end_date,
                    expense_category_id: expense_category_id,
                    staff_id: staff_id,
                    payment_method: payment_method,
                    flag_id: flag_id,
                    price_type: price_type
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    var errorMessage = 'Terjadi kesalahan saat memproses export.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        $(document).ready(function() {
            if (!is_free) {
                showSpinners();
                loadBasicData('', 'isThisMonth');
                loadExpensesData('isThisMonth');
                loadSalesData('isThisMonth');
                initTotalPenjualan('isThisMonth');
            } else {
                Swal.fire({
                    title: 'Info',
                    text: 'Mohon maaf Laporan Penjualan (Advance) hanya untuk pengguna Randu Premium. Silakan melakukan upgrade terlebih dahulu',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/premium';
                    }
                });
            }
        });
    </script>
@endsection
