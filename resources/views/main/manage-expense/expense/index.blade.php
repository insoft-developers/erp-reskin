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
                        <li class="breadcrumb-item"><a href="{{ route('expense.index') }}">List Pengeluaran</a></li>
                        <li class="breadcrumb-item">List Pengeluaran Builder</li>
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
                                <h5 class="card-title">List Pengeluaran</h5>

                                <div class="d-flex">
                                    @if ($data['count_cat_expense'] > 0)
                                        <button onclick="createData()" class="btn btn-primary m-2">
                                            <i class="feather-plus"></i> Buat Baru
                                        </button>
                                    @else
                                        <a href="javascript:void(0)" onclick="redirectCreate()" class="btn btn-primary m-2"><i class="feather-plus"></i> Buat Baru</a>
                                    @endif
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
                                    <div class="col-md-3">
                                        <select class="form-control cust-control" id="categoryExpense"
                                            name="category_expense_id">
                                            <option value="">Pilih Kategori</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select class="form-control cust-control" id="bulan" name="bulan">
                                            <option value="">Pilih Bulan</option>
                                            @foreach (bulan() as $id => $item)
                                                <option value="{{ $id }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
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
                                    <table class="table table-striped nowrap" id="data-table">
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
                                                <th>Nama Kategori</th>
                                                <th>Tanggal</th>
                                                <th>Diambil Dari</th>
                                                <th>Pengeluaran Untuk</th>
                                                <th>Amount</th>
                                                <th>Keterangan</th>


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
                    $("#sync-btn").removeAttr('hidden');
                } else {
                    $('#delete-btn').attr('hidden', 'hidden');
                    $("#sync-btn").attr('hidden', 'hidden');
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
                    url: "{{ route('expense.sync') }}",
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

            $('#categoryExpense').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Kategori Pengeluaran',
                ajax: {
                    url: "{{ route('expense.category_data') }}",
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
                url: "{{ route('expense.single.sync') }}",
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

        $(document).on('input', '#searchData', function() {
            filter();
        })

        $(document).on('change', '#categoryExpense', function() {
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
            var category = $('#categoryExpense').select2('data')[0].id;
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
                    url: "{{ route('expense.data') }}",
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
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'dari',
                        name: 'dari'
                    },
                    {
                        data: 'untuk',
                        name: 'untuk'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
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
                            url: "{{ route('expense.destroy', ':id') }}".replace(':id', id),
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
                        'ids': ids
                    };
                    $.ajax({
                            url: "{{ route('expense.destroyAll') }}",
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
                    url: "{{ route('expense.create') }}",
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

        function redirectCreate() {
            Swal.fire({
                title: 'Error',
                text: 'Silahkan Membuat Kategori Terlebih Dahulu!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, create it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('expense.category.index') }}";
                }
            })
        }

        function editData(id) {
            $.ajax({
                    url: "{{ route('expense.edit', ':id') }}".replace(':id', id),
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
                url: "{{ route('expense.single.unsync') }}",
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
