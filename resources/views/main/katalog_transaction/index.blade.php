@extends('master')

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('katalog-randu.index') }}">Transaksi</a></li>
                        <li class="breadcrumb-item">Transaksi Builder</li>
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
                                <h5 class="card-title">Transaksi</h5>
                            </div>
                            <div class="card-body custom-card-action p-3">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" name="start_date" id="start_date" onchange="filter()" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="form-control cust-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="end_date" class="form-label">End Date</label>
                                            <input type="date" name="end_date" id="end_date" onchange="filter()" value="{{ date('Y-m-d') }}" class="form-control cust-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="status_transaction" class="form-label">Status Transaksi</label>
                                            <select name="status_transaction" id="status_transaction" onchange="filter()" class="form-select cust-control">
                                                <option value="" selected>Semua</option>
                                                <option value="0">Proses</option>
                                                <option value="1">Dikirim</option>
                                                <option value="2">Selesai</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="status_payment" class="form-label">Status Pembayaran</label>
                                            <select name="status_payment" id="status_payment" onchange="filter()" class="form-select cust-control">
                                                <option value="" selected>Semua</option>
                                                <option value="0">Unpaid</option>
                                                <option value="1">Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-group">
                                            <label for="searchData" class="form-label">Cari</label>
                                            <input type="text" id="searchData" placeholder="Cari disini.."
                                                class="form-control cust-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Reference</th>
                                                <th>Referal</th>
                                                <th>Name</th>
                                                <th>Status Transaksi</th>
                                                <th>Status Pembayaran</th>
                                                <th>Detail Product</th>
                                                <th>Provinsi</th>
                                                <th>Kota</th>
                                                <th>Kecamatan</th>
                                                <th>Alamat</th>
                                                <th>Jasa Kirim</th>
                                                <th>Ongkos Kirim</th>
                                                <th>Total</th>
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
        });

        $(document).on('input', '#searchData', function() {
            filter();
        })

        function filter() {
            var keyword = $('#searchData').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var status_transaction = $('#status_transaction').val();
            var status_payment = $('#status_payment').val();

            init_table(keyword, start_date, end_date, status_transaction, status_payment);
        }

        function init_table(keyword = '', start_date = '', end_date = '', status_transaction = '', status_payment = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#data-table');
            table.destroy();

            var table = $('#data-table').DataTable({
                processing:true,
                serverSide:true,
                dom: 'Blfrtip',
                columnDefs: [
                    {
                        target: 0,
                        visible: false,
                        searchable: false
                    },
                ],
                
                ajax: {type: "GET", url: "{{ route('katalog-randu.transaction-data') }}", data:{'keyword':keyword, 'start_date':start_date, 'end_date':end_date, 'status_transaction':status_transaction, 'status_payment':status_payment}},
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'references',name: 'references'},
                    {data: 'referal',name: 'referal'},
                    {data: 'name',name: 'name'},
                    {data: 'status_transaction',name: 'status_transaction'},
                    {data: 'status_payment',name: 'status_payment'},
                    {data: 'detail_product',name: 'detail_product'},
                    {data: 'province',name: 'province'},
                    {data: 'city',name: 'city'},
                    {data: 'district',name: 'district'},
                    {data: 'address',name: 'address'},
                    {data: 'shipping',name: 'shipping'},
                    {data: 'ongkir',name: 'ongkir'},
                    {data: 'total_price',name: 'total_price'},
                ]
            });
        }
    </script>
@endsection
