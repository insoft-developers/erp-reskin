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
                            <li class="breadcrumb-item">Laporan Stock</li>
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
                                            <p class="mb-0">Total Nilai Stock Akhir: </p>
                                            <h3 class="display-6" id="total_akhir"></h3>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <select id="yearFilter" class="form-control" aria-label="Pilih Tahun">
                                                        <option value="">Tampilkan Semua Tahun</option>
                                                        @foreach (tahun() as $thn)
                                                            <option value="{{ $thn }}"
                                                                {{ now()->year == $thn ? 'selected' : '' }}>
                                                                {{ $thn }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select id="monthFilter" class="form-control" aria-label="Pilih Bulan">
                                                        <option value="">Tampilkan Semua Bulan</option>
                                                        @foreach (bulan() as $key => $bln)
                                                            <option value="{{ $key }}"
                                                                {{ now()->month == $key ? 'selected' : '' }}>
                                                                {{ $bln }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <button type='submit' class='btn btn-primary me-2' onclick="filter()"><i
                                                            class="feather-filter"></i> Filter</button>
                                                        <button type="button" class="btn btn-success me-2" id="export-btn"
                                                            onclick="exportData()"><i class="feather-download"></i> Export Xls</button>
                                                        {{-- <button type="button" class="btn btn-warning" id="sync-btn"
                                                            onclick="syncStock()"><i class="feather-download"></i> Sync Stok</button> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 text-center mt-3" id="loading-sync" style="display: none">
                                            <p>
                                                Singkronisasi Stock Sedang Berjalan Mohon Tunggu Sebentar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [Table] end -->

                        <div class="col-xxl-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <div class="float-left">
                                        <h5 class="card-title">Laporan Stok Barang Jadi</h5>
                                    </div>
                                    <div class="float-right">
                                        <h5 class="card-title" id="total-stock-barang-jadi"></h5>
                                    </div>
                                </div>
                                <div class="card-body custom-card-action p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" oninput="filterDataBarangJadi()" id="keyword-barang-jadi" placeholder="Cari disini.."
                                                    class="form-control cust-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table-barang-jadi">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah Awal Bulan (Unit)</th>
                                                    <th>Pemasukan (Unit)</th>
                                                    <th>Pengeluaran (Unit)</th>
                                                    <th>Jumlah Akhir Bulan (Unit)</th>
                                                    <th>COGS/HPP (Rp)</th>
                                                    <th>Nilai Stok Akhir (Rp)</th>
                                                    <th>Stock di Daftar</th>
                                                    <th>Selisih</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-xxl-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <div class="float-left">
                                        <h5 class="card-title">Laporan Stok Produk Manufaktur</h5>
                                    </div>
                                    <div class="float-right">
                                        <h5 class="card-title" id="total-stock-manufaktur"></h5>
                                    </div>
                                </div>
                                <div class="card-body custom-card-action p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" oninput="filterDataManufaktur()" id="keyword-manufaktur" placeholder="Cari disini.."
                                                    class="form-control cust-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table-manufaktur">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah Awal Bulan (Unit)</th>
                                                    <th>Pemasukan (Unit)</th>
                                                    <th>Pengeluaran (Unit)</th>
                                                    <th>Jumlah Akhir Bulan (Unit)</th>
                                                    <th>COGS/HPP (Rp)</th>
                                                    <th>Nilai Stok Akhir (Rp)</th>
                                                    <th>Stock di Daftar</th>
                                                    <th>Selisih</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        
            
                        <div class="col-xxl-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <div class="float-left">
                                        <h5 class="card-title">Laporan Stok Setengah Jadi</h5>
                                    </div>
                                    <div class="float-right">
                                        <h5 class="card-title" id="total-stock-setengah-jadi"></h5>
                                    </div>
                                </div>
                                <div class="card-body custom-card-action p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" oninput="filterDataSetengahJadi()" id="keyword-setengah-jadi" placeholder="Cari disini.."
                                                    class="form-control cust-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table-setengah-jadi">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah Awal Bulan (Unit)</th>
                                                    <th>Pemasukan (Unit)</th>
                                                    <th>Pengeluaran (Unit)</th>
                                                    <th>Jumlah Akhir Bulan (Unit)</th>
                                                    <th>COGS/HPP (Rp)</th>
                                                    <th>Nilai Stok Akhir (Rp)</th>
                                                    <th>Stock di Daftar</th>
                                                    <th>Selisih</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <div class="col-xxl-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <div class="float-left">
                                        <h5 class="card-title">Laporan Stok Bahan Baku</h5>
                                    </div>
                                    <div class="float-right">
                                        <h5 class="card-title" id="total-stock-bahan-baku"></h5>
                                    </div>
                                </div>
                                <div class="card-body custom-card-action p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" oninput="filterDataBahanBaku()" id="keyword-bahan-baku" placeholder="Cari disini.."
                                                    class="form-control cust-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table-bahan-baku">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah Awal Bulan (Unit)</th>
                                                    <th>Pemasukan (Unit)</th>
                                                    <th>Pengeluaran (Unit)</th>
                                                    <th>Jumlah Akhir Bulan (Unit)</th>
                                                    <th>COGS/HPP (Rp)</th>
                                                    <th>Nilai Stok Akhir (Rp)</th>
                                                    <th>Stock di Daftar</th>
                                                    <th>Selisih</th>
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
                                <p class="mb-0">Total Nilai Stock Akhir: </p>
                                <h3 class="display-6" id="total_akhir"></h3>
                            </div>

                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-6 col-md-4 mt-3 me-md-3">
                                        <select id="yearFilter" class="form-control" aria-label="Pilih Tahun">
                                            <option value="">Tampilkan Semua Tahun</option>
                                            @foreach (tahun() as $thn)
                                                <option value="{{ $thn }}"
                                                    {{ now()->year == $thn ? 'selected' : '' }}>
                                                    {{ $thn }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-4 mt-3">
                                        <select id="monthFilter" class="form-control" aria-label="Pilih Bulan">
                                            <option value="">Tampilkan Semua Bulan</option>
                                            @foreach (bulan() as $key => $bln)
                                                <option value="{{ $key }}"
                                                    {{ now()->month == $key ? 'selected' : '' }}>
                                                    {{ $bln }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-12 col-md-3 mt-2">
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
                        <h5 class="card-title">Laporan Stok Barang Jadi</h5>
                    </div>
                    <div class="card-body custom-card-action p-3">
                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table-barang-jadi">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah Awal Bulan (Unit)</th>
                                        <th>Pemasukan (Unit)</th>
                                        <th>Pengeluaran (Unit)</th>
                                        <th>Jumlah Akhir Bulan (Unit)</th>
                                        <th>COGS/HPP (Rp)</th>
                                        <th>Nilai Stok Akhir (Rp)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Laporan Stok Produk Manufaktur</h5>
                    </div>
                    <div class="card-body custom-card-action p-3">
                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table-manufaktur">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah Awal Bulan (Unit)</th>
                                        <th>Pemasukan (Unit)</th>
                                        <th>Pengeluaran (Unit)</th>
                                        <th>Jumlah Akhir Bulan (Unit)</th>
                                        <th>COGS/HPP (Rp)</th>
                                        <th>Nilai Stok Akhir (Rp)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Laporan Stok Setengah Jadi</h5>
                    </div>
                    <div class="card-body custom-card-action p-3">
                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table-setengah-jadi">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah Awal Bulan (Unit)</th>
                                        <th>Pemasukan (Unit)</th>
                                        <th>Pengeluaran (Unit)</th>
                                        <th>Jumlah Akhir Bulan (Unit)</th>
                                        <th>COGS/HPP (Rp)</th>
                                        <th>Nilai Stok Akhir (Rp)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Laporan Stok Bahan Baku</h5>
                    </div>
                    <div class="card-body custom-card-action p-3">
                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table-bahan-baku">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah Awal Bulan (Unit)</th>
                                        <th>Pemasukan (Unit)</th>
                                        <th>Pengeluaran (Unit)</th>
                                        <th>Jumlah Akhir Bulan (Unit)</th>
                                        <th>COGS/HPP (Rp)</th>
                                        <th>Nilai Stok Akhir (Rp)</th>
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

        var total_akhir = 0;
        const userKey = '{{$userKey ?? null}}'

        function exportData() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();
            var url = "{{ !$userKey ? route('laporan.stock.export') : route('preview.laporan.stock.export') }}?month="+month+"&year="+year+"&user_key={{ $userKey ?? '' }}";
            window.open(url)
        }

        function syncStock() {
            $('#loading-sync').show();

            var postForm = {
                '_token': '{{ csrf_token() }}',
            };
            $.ajax({
                url: "{{ route('laporan.stock.syncStock') }}", 
                type: 'POST', 
                data : postForm,
                dataType  : 'json',
            })
            .done(function(data) {
                if (data.errors) {
                    let errorMessages = '';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorMessages += messages.join('<br>') + '<br>';
                    }
        
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: errorMessages
                    });
                }else if(!data.status){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .fail(function() {
                alert('Load data failed.');
            });
        }
        
        function filter() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();

            tableDataBarangJadi(month, year);
            tableDataManufaktur(month, year);
            tableDataSetengahJadi(month, year);
            tableDataBahanBaku(month, year);

            sumDataBarangJadi(month, year);
            sumDataManufaktur(month, year);
            sumDataSetengahJadi(month, year);
            sumDataBahanBaku(month, year);

            total_akhir = 0;
        }

        function filterDataBarangJadi() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();
            var keyword = $('#keyword-barang-jadi').val();

            tableDataBarangJadi(month, year, keyword);
        }

        function filterDataManufaktur() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();
            var keyword = $('#keyword-manufaktur').val();

            tableDataManufaktur(month, year, keyword);
        }

        function filterDataSetengahJadi() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();
            var keyword = $('#keyword-setengah-jadi').val();

            tableDataSetengahJadi(month, year, keyword);
        }

        function filterDataBahanBaku() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();
            var keyword = $('#keyword-bahan-baku').val();

            tableDataBahanBaku(month, year, keyword);
        }


        // TABLE
        function tableDataBarangJadi(month = '', year = '', keyword = '') {
            var table = new DataTable('#data-table-barang-jadi');
            table.destroy();

            var filterData = {
                'category' : 'barang-jadi',
                'month' : month,
                'year' : year,
                'keyword' : keyword,
                'user_key': userKey
            }

            var table = $('#data-table-barang-jadi').DataTable({
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
                    url: "{{ !$userKey ? route('laporan.stock.data') : route('preview.laporan.stock.data') }}",
                    data: filterData
                },
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'name',name: 'name'},
                    {data: 'initial_stock',name: 'initial_stock'},
                    {data: 'total_in',name: 'total_in'},
                    {data: 'total_out',name: 'total_out'},
                    {data: 'final_stock',name: 'final_stock'},
                    {data: 'unit_price',name: 'unit_price'},
                    {data: 'stock_value',name: 'stock_value'},
                    {data: 'stock_list',name: 'stock_list'},
                    {data:'selisih', name:'selisih'}
                    
                ],
            });
        }

        function tableDataManufaktur(month = '', year = '', keyword = '') {
            var table = new DataTable('#data-table-manufaktur');
            table.destroy();

            var filterData = {
                'category' : 'manufaktur',
                'month' : month,
                'year' : year,
                'keyword' : keyword,
                'user_key': userKey
            }

            var table = $('#data-table-manufaktur').DataTable({
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
                    url: "{{ !$userKey ? route('laporan.stock.data') : route('preview.laporan.stock.data') }}",
                    data: filterData
                },
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'name',name: 'name'},
                    {data: 'initial_stock',name: 'initial_stock'},
                    {data: 'total_in',name: 'total_in'},
                    {data: 'total_out',name: 'total_out'},
                    {data: 'final_stock',name: 'final_stock'},
                    {data: 'unit_price',name: 'unit_price'},
                    {data: 'stock_value',name: 'stock_value'},
                    {data: 'stock_list',name: 'stock_list'},
                    {data:'selisih', name:'selisih'}
                ],
            });
        }

        function tableDataSetengahJadi(month = '', year = '', keyword = '') {
            var table = new DataTable('#data-table-setengah-jadi');
            table.destroy();

            var filterData = {
                'category' : 'setengah-jadi',
                'month' : month,
                'year' : year,
                'keyword' : keyword,
                'user_key': userKey
            }

            var table = $('#data-table-setengah-jadi').DataTable({
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
                    url: "{{ !$userKey ? route('laporan.stock.data') : route('preview.laporan.stock.data') }}",
                    data: filterData
                },
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'name',name: 'name'},
                    {data: 'initial_stock',name: 'initial_stock'},
                    {data: 'total_in',name: 'total_in'},
                    {data: 'total_out',name: 'total_out'},
                    {data: 'final_stock',name: 'final_stock'},
                    {data: 'unit_price',name: 'unit_price'},
                    {data: 'stock_value',name: 'stock_value'},
                    {data: 'stock_list',name: 'stock_list'},
                    {data:'selisih', name:'selisih'}
                ],
            });
        }

        function tableDataBahanBaku(month = '', year = '', keyword = '') {
            var table = new DataTable('#data-table-bahan-baku');
            table.destroy();

            var filterData = {
                'category' : 'bahan-baku',
                'month' : month,
                'year' : year,
                'keyword' : keyword,
                'user_key': userKey
            }

            var table = $('#data-table-bahan-baku').DataTable({
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
                    url: "{{ !$userKey ? route('laporan.stock.data') : route('preview.laporan.stock.data') }}",
                    data: filterData
                },
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'name',name: 'name'},
                    {data: 'initial_stock',name: 'initial_stock'},
                    {data: 'total_in',name: 'total_in'},
                    {data: 'total_out',name: 'total_out'},
                    {data: 'final_stock',name: 'final_stock'},
                    {data: 'unit_price',name: 'unit_price'},
                    {data: 'stock_value',name: 'stock_value'},
                    {data: 'stock_list',name: 'stock_list'},
                    {data:'selisih', name:'selisih'}
                ],
            });
        }

        // SUMMARY
        function sumDataBarangJadi(month, year) {
            var postForm = {
                'month': month,
                'year': year
            };
            $.ajax({
                url: "{{ !$userKey ? route('laporan.stock.sumDataBarangJadi') : route('preview.laporan.stock.sumDataBarangJadi') }}",
                type: 'GET', 
                data : postForm,
                dataType  : 'json',
            })
            .done(function(data) {
                var totalStockValue = data.reduce(function(acc, item) {
                    return acc + (item.stock_value || 0);
                }, 0);

                var resultTotal = totalStockValue.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                $('#total-stock-barang-jadi').html('Total Nilai Stok Akhir: ' + resultTotal);

                total_akhir += totalStockValue

                var resultGrandTotal = total_akhir.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });

                $('#total_akhir').text(resultGrandTotal);
            })
            .fail(function() {
                alert('Load data failed.');
            });
        }

        function sumDataManufaktur(month, year) {
            var postForm = {
                'month': month,
                'year': year
            };
            $.ajax({
                url: "{{ !$userKey ? route('laporan.stock.sumDataManufaktur') : route('preview.laporan.stock.sumDataManufaktur') }}",
                type: 'GET', 
                data : postForm,
                dataType  : 'json',
            })
            .done(function(data) {
                var totalStockValue = data.reduce(function(acc, item) {
                    return acc + (item.stock_value || 0);
                }, 0);

                var resultTotal = totalStockValue.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                $('#total-stock-manufaktur').html('Total Nilai Stok Akhir: ' + resultTotal);

                total_akhir += totalStockValue

                var resultGrandTotal = total_akhir.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });

                $('#total_akhir').text(resultGrandTotal);
            })
            .fail(function() {
                alert('Load data failed.');
            });
        }

        function sumDataSetengahJadi(month, year) {
            var postForm = {
                'month': month,
                'year': year
            };
            $.ajax({
                url: "{{ !$userKey ? route('laporan.stock.sumDataSetBarangJadi') : route('preview.laporan.stock.sumDataSetBarangJadi') }}",
                type: 'GET', 
                data : postForm,
                dataType  : 'json',
            })
            .done(function(data) {
                var totalStockValue = data.reduce(function(acc, item) {
                    return acc + (item.stock_value || 0);
                }, 0);

                var resultTotal = totalStockValue.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                $('#total-stock-setengah-jadi').html('Total Nilai Stok Akhir: ' + resultTotal);

                total_akhir += totalStockValue

                var resultGrandTotal = total_akhir.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });

                $('#total_akhir').text(resultGrandTotal);
            })
            .fail(function() {
                alert('Load data failed.');
            });
        }

        function sumDataBahanBaku(month, year) {
            var postForm = {
                'month': month,
                'year': year
            };
            $.ajax({
                url: "{{ !$userKey ? route('laporan.stock.sumDataMaterial') : route('preview.laporan.stock.sumDataMaterial') }}",
                type: 'GET', 
                data : postForm,
                dataType  : 'json',
            })
            .done(function(data) {
                var totalStockValue = data.reduce(function(acc, item) {
                    return acc + (item.stock_value || 0);
                }, 0);

                var resultTotal = totalStockValue.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                $('#total-stock-bahan-baku').html('Total Nilai Stok Akhir: ' + resultTotal);

                total_akhir += totalStockValue

                var resultGrandTotal = total_akhir.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });

                $('#total_akhir').text(resultGrandTotal);
            })
            .fail(function() {
                alert('Load data failed.');
            });
        }
    </script>
@endsection
