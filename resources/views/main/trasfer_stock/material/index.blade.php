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
                        <li class="breadcrumb-item"><a href="{{ route('transfer-stock.material.index') }}">Transfer Stok Bahan Baku</a></li>
                        <li class="breadcrumb-item">Transfer Stok Bahan Baku Builder</li>
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
                                <h5 class="card-title">Transfer Stok Bahan Baku</h5>

                                <button onclick="createData()" class="btn btn-primary">
                                    <i class="feather-plus"></i> Buat Baru
                                </button>
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
                                    <div class="col">
                                        <select class="form-control cust-control" id="type_category" name="type">
                                        </select>
                                    </div>

                                    <div class="col">
                                        <select class="form-control cust-control" id="bulan" name="bulan">
                                            <option value="">Pilih Bulan</option>
                                            @foreach (bulan() as $id => $item)
                                                <option value="{{ $id }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col">
                                        <select class="form-control cust-control" id="tahun" name="tahun">
                                            <option value="">Pilih Tahun</option>
                                            @foreach (tahun() as $item)
                                                <option value="{{ $item }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mtop20"></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="searchData" placeholder="Cari disini.."
                                                class="form-control cust-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-insoft" id="delete-btn" hidden>
                                            <i class="feather-trash-2"></i> Bulk Delete
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-success btn-insoft" id="sync-btn" hidden>
                                            <i class="fa fa-sync"></i>&nbsp;&nbsp;Synchronize
                                        </button>
                                    </div>
                                </div>


                                <div class="mtop30"></div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="data-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{-- CHECKBOX SELECT ALL --}}
                                                    <div class="custom-control custom-checkbox ms-1">
                                                        <input class="custom-control-input" type="checkbox"
                                                            id="checkAll" />
                                                        <label class="custom-control-label" for="checkAll"></label>
                                                    </div>
                                                </th>
                                                <th>Action</th>
                                                <th>Material Yang Ditransfer</th>
                                                <th>Jumlah Stok Yang Di Kurangi</th>
                                                <th>Material Yang Dituju</th>
                                                <th>Jumlah Stock Ditambah</th>
                                                <th>Tanggal</th>
                                                <th>Dibuat Pada</th>
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

            $('#checkAll').on('click', function() {
                $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));

                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#delete-btn').removeAttr('hidden');
                    $("#sync-btn").removeAttr('hidden');
                } else {
                    $('#delete-btn').attr('hidden', 'hidden');
                    $("#sync-btn").attr('hidden', 'hidden');
                }
            });

            $('tbody').on('click', 'input[type="checkbox"]', function() {
                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#delete-btn').removeAttr('hidden');
                    $('#sync-btn').removeAttr('hidden');
                } else {
                    $('#delete-btn').attr('hidden', 'hidden');
                    $('#sync-btn').attr('hidden', 'hidden');
                }
            });

            $('#delete-btn').on('click', function() {
                var elem = $('tbody input[type="checkbox"]:checked');
                var ids = [];
                elem.map(function() {
                    ids.push($(this).val());
                });

                bulkDelete(ids);
            });
        });

        $(document).on('input', '#searchData', function() {
            filter();
        })

        $(document).on('change', '#type_category', function() {
            filter();
        })

        $(document).on('change', '#bulan', function() {
            filter();
        })

        $(document).on('change', '#tahun', function() {
            filter();
        })

        function filter() {
            var keyword = $('#searchData').val();
            var type_category = $('#type_category').val();
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();

            init_table(keyword, type_category, bulan, tahun);
        }

        function init_table(keyword = '', type_category = '', bulan = '', tahun = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#data-table');
            table.destroy();

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
                    url: "{{ route('transfer-stock.material_data') }}",
                    data: {
                        'keyword': keyword,
                        'bulan': bulan,
                        'tahun': tahun
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'material_from_id',
                        name: 'material_from_id'
                    },
                    {
                        data: 'stock_from',
                        name: 'stock_from'
                    },
                    {
                        data: 'material_to_id',
                        name: 'material_to_id'
                    },
                    {
                        data: 'stock_to',
                        name: 'stock_toom'
                    },
                    {
                        data: 'date',
                        name: 'datet_from'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ]
            });
        }

        function deleteData(event, id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'DELETE',
                    };
                    $.ajax({
                            url: "{{ route('transfer-stock.material.destroy', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Deleted!', data['message'], 'success');
                            init_table();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                        });
                }
            });
        }

        function createData() {
            $.ajax({
                    url: "{{ route('transfer-stock.material.create') }}",
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function detail(id) {
            $.ajax({
                    url: "{{ route('transfer-stock.material.show', ':id') }}".replace(':id', id),
                    type: 'GET',
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
                });
        }
    </script>
@endsection
