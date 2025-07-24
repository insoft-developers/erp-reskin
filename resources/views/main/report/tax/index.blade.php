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
                            <li class="breadcrumb-item">Laporan Pajak</li>
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
                                <div class="card-body custom-card-action p-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="mb-0">Total Pajak Yang Diterima: </p>
                                            <h3 class="display-6" id="total_pajak"></h3>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4 me-3">
                                                    <select id="yearFilter" class="form-control" aria-label="Pilih Tahun">
                                                        <option value="">Tampilkan Semua Tahun</option>
                                                        @foreach ($tahun as $thn)
                                                            <option value="{{ $thn }}"
                                                                {{ now()->year == $thn ? 'selected' : '' }}>
                                                                {{ $thn }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <select id="monthFilter" class="form-control" aria-label="Pilih Bulan">
                                                        <option value="">Tampilkan Semua Bulan</option>
                                                        @foreach ($bulan as $key => $bln)
                                                            <option value="{{ $key + 1 }}"
                                                                {{ now()->month == $key + 1 ? 'selected' : '' }}>
                                                                {{ $bln }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <button type='submit' class='btn btn-primary me-2' onclick="filter()"><i
                                                            class="feather-filter"></i> Filter</button>
                                                        <button type="button" class="btn btn-success" id="export-btn"
                                                            onclick="exportData()"><i class="feather-download"></i> Export Xls</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [Table] end -->

                        <div class="col-xxl-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <h5 class="card-title">Laporan Pajak</h5>
                                </div>
                                <div class="card-body custom-card-action p-3">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Kode Transaksi</th>
                                                    <th>Tanggal Waktu</th>
                                                    <th>Nama Konsumen</th>
                                                    <th>Sub Total Pesanan</th>
                                                    <th>Nominal Pajak</th>
                                                    <th>Metode Pembayaran</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <div class="card-body custom-card-action p-3">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-0">Total Pajak Yang Diterima: </p>
                                <h3 class="display-6" id="total_pajak"></h3>
                            </div>

                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-6 col-md-4 mt-2 mt-md-0 me-md-3">
                                        <select id="yearFilter" class="form-control" aria-label="Pilih Tahun">
                                            <option value="">Tampilkan Semua Tahun</option>
                                            @foreach ($tahun as $thn)
                                                <option value="{{ $thn }}"
                                                    {{ now()->year == $thn ? 'selected' : '' }}>
                                                    {{ $thn }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-4 mt-2 mt-md-0">
                                        <select id="monthFilter" class="form-control" aria-label="Pilih Bulan">
                                            <option value="">Tampilkan Semua Bulan</option>
                                            @foreach ($bulan as $key => $bln)
                                                <option value="{{ $key + 1 }}"
                                                    {{ now()->month == $key + 1 ? 'selected' : '' }}>
                                                    {{ $bln }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3 mt-2 mt-md-0">
                                        <div class="input-group">
                                            <button type='submit' class='btn btn-primary me-2' onclick="filter()"><i
                                                class="feather-filter"></i> Filter</button>
                                            <button type="button" class="btn btn-success" id="export-btn"
                                                onclick="exportData()"><i class="feather-download"></i> Export Xls</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [Table] end -->

            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    @if($from === 'desktop')
                        <div class="card-header">
                            <h5 class="card-title">Laporan Pajak</h5>
                        </div>
                    @endif
                    <div class="card-body custom-card-action p-3">
                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kode Transaksi</th>
                                        <th>Tanggal Waktu</th>
                                        <th>Nama Konsumen</th>
                                        <th>Sub Total Pesanan</th>
                                        <th>Nominal Pajak</th>
                                        <th>Metode Pembayaran</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
            filter();
        });
        const userKey = '{{$userKey ?? null}}'

        function exportData() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();
            var url = "{{ !$userKey ? route('laporan.pajak.export') : route('preview.laporan.pajak.export') }}?month="+month+"&year="+year+"&user_key={{$userKey ?? ''}}";
            window.open(url)
        }

        function filter() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();

            init_table(month, year);
        }

        function init_table(month = '', year = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#data-table');
            table.destroy();

            var filterData = {
                'month': month,
                'year': year,
                'user_key': userKey
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
                    url: "{{ !$userKey ? route('laporan.pajak.data') : route('preview.laporan.pajak.data') }}",
                    data: filterData
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'created',
                        name: 'created'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'paid',
                        name: 'paid'
                    },
                    {
                        data: 'tax',
                        name: 'tax'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                ],
            });

            $.ajax({
                    url: "{{ isset($userKey) ? route('preview.laporan.pajak.chart') : route('laporan.pajak.chart') }}",
                    type: 'GET',
                    data: filterData,
                    dataType: 'json',
                })
                .done(function(data) {
                    var total_pajak = data.total_pajak;

                    $('#total_pajak').text(total_pajak);
                })
                .fail(function() {
                    alert('Load data failed.');
                });
        }
    </script>
@endsection
