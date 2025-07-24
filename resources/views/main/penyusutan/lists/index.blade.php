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
                        <li class="breadcrumb-item"><a href="{{ route('penyusutan.index') }}">Penyusutan</a></li>
                        <li class="breadcrumb-item">Penyusutan Builder</li>
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
                                <h5 class="card-title">Penyusutan</h5>

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
                                    <div class="col" hidden>
                                        <select class="form-control cust-control" id="ml_fixed_asset" name="ml_fixed_asset">
                                        </select>
                                    </div>

                                    <div class="col" hidden>
                                        <select class="form-control cust-control" id="ml_accumulated_depreciation"
                                            name="ml_accumulated_depreciation">
                                        </select>
                                    </div>

                                    <div class="col">
                                        <select class="form-control cust-control" id="ml_admin_general_fee"
                                            name="ml_admin_general_fee">
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
                                                <option value="{{ $item }}"
                                                    {{ date('Y') == $item ? 'selected' : '' }}>{{ $item }}</option>
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
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-danger btn-insoft" id="delete-btn" hidden>
                                            <i class="feather-trash-2"></i> Bulk Delete
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
                                                <th>Kategori Penyusutan</th>
                                                <th>Akumulasi Penyusutan</th>
                                                <th>Beban Penyusutan</th>
                                                <th>Nama Asset</th>
                                                <th>Nilai Awal</th>
                                                <th>Umur Manfaat </th>
                                                <th>Nilai Residu</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-kurang">
        <div class="modal-dialog">
            <form id="form-kurang-asset" method="POST">
                @csrf
                <div class="modal-content">
                    @csrf
                    <div class="modal-header" style="background-color: #2f467a;">
                        <h5 class="modal-title" style="color:white;">Proses Kurangi Aset</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="transaction_id" name="transaction_id">
                        <div class="form-group mtop20">
                            <label>Nama Aset:</label>
                            <input type="text" name="asset_name" id="asset_name" class="form-control cust-control"
                                readonly>
                        </div>
                        <div class="form-group mtop20">
                            <label>Jumlah Aset:</label>
                            <input type="text" name="asset_quantity" id="asset_quantity"
                                class="form-control cust-control" readonly>
                        </div>
                        <div class="form-group mtop20">
                            <label>Harga Per Unit:</label>
                            <input type="text" id="asset_price_text" class="form-control cust-control" readonly>
                            <input type="hidden" name="asset_price" id="asset_price">
                        </div>
                        <div class="form-group mtop20">
                            <label>Jumlah Aset Berkurang/Hilang:</label>
                            <input type="number" required name="lost_quantity" id="lost_quantity"
                                class="form-control cust-control">
                        </div>
                        <div class="form-group mtop20">
                            <label>Nilai Aset Berkurang/Hilang:</label>
                            <input type="text" id="lost_value_text" class="form-control cust-control" readonly>
                            <input type="hidden" name="lost_value" id="lost_value" class="form-control cust-control">
                        </div>
                        <div class="form-group mtop20">
                            <label>Keterangan:</label>
                            <textarea id="asset_note" name="asset_note" class="form-control cust-control"></textarea>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        function reset_input() {
            $("#lost_quantity").val("");
            $("#lost_value_text").val("");
            $("#lost_value").val("");
            $("#asset_note").val("");
        }

        $("#form-kurang-asset").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('penyusutan.lost-store') }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(data) {
                    $("#modal-kurang").modal("hide");
                    if (data.success) {
                        reloadTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'danger',
                            title: 'Failed',
                            text: data.message
                        });
                    }
                }
            });
        });

        $("#lost_quantity").blur(function() {
            var jumlah = parseInt($("#asset_quantity").val());
            var lost = parseInt($(this).val());
            if (lost > jumlah) {
                $("#lost_quantity").val("");
                Swal.fire({
                    icon: 'danger',
                    title: 'Failed',
                    text: "Jumlah Asset hilang tidak boleh lebih dari jumlah pembelian"
                })
                return;
            }
        });

        $("#lost_quantity").keyup(function() {
            var jumlah = $("#asset_quantity").val();
            var price = parseInt($("#asset_price").val());
            var lost = parseInt($(this).val());
            if (lost > jumlah) {
                $("#lost_quantity").val("");
                Swal.fire({
                    icon: 'danger',
                    title: 'Failed',
                    text: "Jumlah Asset hilang tidak boleh lebih dari jumlah pembelian"
                })
                return;
            }


            var total = price * lost;
            $("#lost_value_text").val(formatAngka(total));
            $("#lost_value").val(total);
        });


        function kurangi_asset(id) {
            reset_input();
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('penyusutan.lost') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        $("#modal-kurang").modal("show");
                        $("#transaction_id").val(id);
                        $("#asset_name").val(data.data.name);
                        $("#asset_quantity").val(data.data.quantity);
                        $("#asset_price_text").val(formatAngka(data.data.buying_price));
                        $("#asset_price").val(data.data.buying_price);
                        $("#lost_value_text").val(0);
                        $("#lost_value").val(0);

                    }
                }
            })

        }

        function sync(id) {
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
                    confirm_sync(id);
                }
            });
        }

        function confirm_sync(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('penyusutan.sync') }}",
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
                url: "{{ route('penyusutan.single.unsync') }}",
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

        $(document).ready(function() {
            init_table();

            $('#checkAll').on('click', function() {
                $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));

                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#delete-btn').removeAttr('hidden');
                } else {
                    $('#delete-btn').attr('hidden', 'hidden');
                }
            });

            $('tbody').on('click', 'input[type="checkbox"]', function() {
                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#delete-btn').removeAttr('hidden');
                } else {
                    $('#delete-btn').attr('hidden', 'hidden');
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

            $('#ml_fixed_asset').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Kategori Penyusutan',
                ajax: {
                    url: "{{ route('penyusutan.mlFixedAsset') }}",
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
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#ml_accumulated_depreciation').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Akumulasi Penyusutan',
                ajax: {
                    url: "{{ route('penyusutan.mlAccumulateDepreciation') }}",
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
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#ml_admin_general_fee').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Beban Penyusutan',
                ajax: {
                    url: "{{ route('penyusutan.mlAdminGeneralFee') }}",
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
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
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

        $(document).on('change', '#ml_fixed_asset', function() {
            filter();
        })

        $(document).on('change', '#ml_accumulated_depreciation', function() {
            filter();
        })

        $(document).on('change', '#ml_admin_general_fee', function() {
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
            var ml_fixed_asset = $('#ml_fixed_asset').val();
            var ml_accumulated_depreciation = $('#ml_accumulated_depreciation').val();
            var ml_admin_general_fee = $('#ml_admin_general_fee').val();
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();

            init_table(keyword, ml_fixed_asset, ml_accumulated_depreciation, ml_admin_general_fee, bulan, tahun);
        }

        function init_table(keyword = '', ml_fixed_asset = '', ml_accumulated_depreciation = '', ml_admin_general_fee = '',
            bulan = '', tahun = '') {
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
                    url: "{{ route('penyusutan.data') }}",
                    data: {
                        'keyword': keyword,
                        'ml_fixed_asset': ml_fixed_asset,
                        'ml_accumulated_depreciation': ml_accumulated_depreciation,
                        'ml_admin_general_fee': ml_admin_general_fee,
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
                    }, {
                        data: 'sync_status',
                        name: 'sync_status'
                    },

                    {
                        data: 'ml_fixed_asset',
                        name: 'ml_fixed_asset'
                    },
                    {
                        data: 'ml_accumulated_depreciation',
                        name: 'ml_accumulated_depreciation'
                    },
                    {
                        data: 'ml_admin_general_fee',
                        name: 'ml_admin_general_fee'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'initial_value',
                        name: 'initial_value'
                    },
                    {
                        data: 'useful_life',
                        name: 'useful_life'
                    },
                    {
                        data: 'residual_value',
                        name: 'residual_value'
                    },
                    {
                        data: 'date',
                        name: 'date'
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
                            url: "{{ route('penyusutan.destroy', ':id') }}".replace(':id', id),
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
                            url: "{{ route('penyusutan.destroyAll') }}",
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
                    url: "{{ route('penyusutan.create') }}",
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
                    url: "{{ route('penyusutan.show', ':id') }}".replace(':id', id),
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
    </script>
@endsection
