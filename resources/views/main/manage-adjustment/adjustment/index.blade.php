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
                        <li class="breadcrumb-item"><a href="{{ route('adjustment.index') }}">Daftar Penyesuaian</a></li>
                        <li class="breadcrumb-item">Penyesuaian Barang Dagang Builder</li>
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
                                <h5 class="card-title">Penyesuaian</h5>
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
                                    <div class="col-md-3">
                                        <select class="form-control cust-control" id="categoryAdjustment"
                                            name="category_adjustment_id">
                                            <option value="">Pilih Kategori</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select class="form-control cust-control" id="bulan" name="bulan">
                                            <option value="">Pilih Bulan</option>
                                            @foreach (bulan() as $id => $item)
                                                <option value="{{ $id }}" {{ $id == date('m') ? 'selected' : '' }}>{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select class="form-control cust-control" id="tahun" name="tahun">
                                            <option value="">Pilih Tahun</option>
                                            @foreach (tahun() as $item)
                                                <option value="{{ $item }}" {{ $item == date('Y') ? 'selected' : '' }}>{{ $item }}</option>
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
                                                <th>Sync Jurnal</th>

                                                <th>Tanggal</th>
                                                <th>Detail Order</th>
                                                <th>Total Product</th>

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
                    $('#sync-btn').removeAttr('hidden');
                } else {
                    $('#delete-btn').attr('hidden', 'hidden');
                    $('#sync-btn').attr('hidden', 'hidden');
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


            $('#sync-btn').on('click', function() {
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
                        var elem = $('tbody input[type="checkbox"]:checked');
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
                    url: "{{ route('adjustment.sync') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        "ids": ids,
                        "_token": csrf_token
                    },
                    success: function(data) {
                        if (data.success) {
                            reloadTable();
                        }
                    }
                });
            }


            $('#categoryAdjustment').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Kategori Penyesuaian',
                ajax: {
                    url: '/adjustment/category-data',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term,
                            limit: 25
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.data, function(item) {
                                return {
                                    id: item.ids,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });

        $(document).on('input', '#searchData', function() {
            filter();
        })

        $(document).on('change', '#categoryAdjustment', function() {
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
            var category = $('#categoryAdjustment').select2('data')[0].id;
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();

            init_table(keyword, category, bulan, tahun);
        }

        function init_table(keyword = '', category = '', bulan = '', tahun = '') {
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
                    url: "{{ route('adjustment.data') }}",
                    data: {
                        'keyword': keyword,
                        'category': category,
                        'bulan': bulan,
                        'tahun': tahun
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
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
                        data: 'sync_status',
                        name: 'sync_status'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'detail',
                        name: 'detail'
                    },
                    {
                        data: 'total_quantity',
                        name: 'total_quantity'
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
                            url: "{{ route('adjustment.destroy', ':id') }}".replace(':id', id),
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

        function bulkDelete(ids) {
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
                        'ids': ids,
                    };
                    $.ajax({
                            url: "{{ route('adjustment.destroyAll') }}",
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
                    url: "{{ route('adjustment.create') }}",
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

        function editData(id) {
            $.ajax({
                    url: "{{ route('adjustment.edit', ':id') }}".replace(':id', id),
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

        function reloadTable() {
            var table = $("#data-table").DataTable();
            table.ajax.reload(null, false);
        }

        function syncData(id) {
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
                    sync_process(id);
                }
            });
        }


        function sync_process(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('adjustment.single.sync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
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
                url: "{{ route('adjustment.single.unsync') }}",
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
    </script>
@endsection
