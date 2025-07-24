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
                        <li class="breadcrumb-item"><a href="{{ route('invoice.invoice.index') }}">Invoice</a></li>
                        <li class="breadcrumb-item">Invoice Builder</li>
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
                                        <p class="mb-0">Total Paid Invoice: </p>
                                        <h3 class="display-6" id="total_paid"></h3>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col">
                                                <button type="button" class="btn btn-warning w-100 me-2" id="change_paid"
                                                    hidden>
                                                    Ubah Paid
                                                </button>
                                            </div>
                                            <div class="col">
                                                <select id="status_filter" class="form-control w-100"
                                                    aria-label="Pilih Status Pembayaran">
                                                    <option value="">Semua Status</option>
                                                    <option value="1">Paid</option>
                                                    <option value="0">Unpaid</option>
                                                </select>
                                            </div>
                                            <div class="col" id="filterPaymentMethod">
                                                <select id="payment_method_filter" class="form-control w-100"
                                                    aria-label="Pilih Metode Pembayaran">
                                                    <option value="">Semua Metode</option>
                                                    @foreach ($payment as $item)
                                                        <option value="{{ $item['code'] }}">{{ $item['method'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select id="year_filter" class="form-control w-100"
                                                    aria-label="Pilih Tahun">
                                                    <option value="">Tampilkan Semua Tahun</option>
                                                    @foreach (tahun() as $thn)
                                                        <option value="{{ $thn }}"
                                                            {{ now()->year == $thn ? 'selected' : '' }}>
                                                            {{ $thn }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select id="month_filter" class="form-control w-100"
                                                    aria-label="Pilih Bulan">
                                                    <option value="">Tampilkan Semua Bulan</option>
                                                    @foreach (bulan() as $key => $bln)
                                                        <option value="{{ $key }}"
                                                            {{ now()->month == $key ? 'selected' : '' }}>
                                                            {{ $bln }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Table] end -->

                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Invoice</h5>

                                <a href="{{ route('invoice.invoice.create') }}" class="btn btn-primary">
                                    <i class="feather-plus"></i> Buat Baru
                                </a>
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

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" id="searchData" placeholder="Cari disini.."
                                            class="form-control cust-control">
                                    </div>
                                </div>

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
                                                <th>Copy Link</th>
                                                <th>Pdf</th>
                                                <th>Sync Jurnal</th>
                                                <th>Status</th>
                                                <th>Termin / DP</th>
                                                <th>Nama Invoice</th>
                                                <th>Kode Invoice</th>
                                                <th>Total Rupiah</th>
                                                <th>Nominal Invoice</th>
                                                <th>Mata Uang</th>
                                                <th>Kurs</th>
                                                <th>Nama Client</th>
                                                <th>Tanggal Dibuat</th>
                                                <th>Jatuh Tempo</th>
                                                <th>Metode</th>
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
            filter();

            $('#checkAll').on('click', function() {
                $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));

                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#change_paid').removeAttr('hidden');
                    $("#sync-btn").removeAttr('hidden');
                } else {
                    $('#change_paid').attr('hidden', 'hidden');
                    $("#sync-btn").attr('hidden', 'hidden');
                }
            });

            $('tbody').on('click', 'input[type="checkbox"]', function() {
                if ($('tbody input[type="checkbox"]:checked').length > 0) {
                    $('#change_paid').removeAttr('hidden');
                    $("#sync-btn").removeAttr('hidden');
                } else {
                    $('#change_paid').attr('hidden', 'hidden');
                    $("#sync-btn").attr('hidden', 'hidden');
                }
            });


            $('#change_paid').on('click', function() {
                var elem = $('tbody input[type="checkbox"]:checked');
                var ids = [];
                elem.map(function() {
                    ids.push($(this).val());
                });

                changeBulkPaid(ids);
            });
        });

        $(document).on('input', '#searchData', function() {
            filter();
        })

        $(document).on('change', '#year_filter', function() {
            filter();
        })

        $(document).on('change', '#month_filter', function() {
            filter();
        })

        $(document).on('change', '#status_filter', function() {
            filter();
        })

        $(document).on('change', '#payment_method_filter', function() {
            filter();
        })

        function filter() {
            var keyword = $('#searchData').val();
            var year = $('#year_filter').val();
            var month = $('#month_filter').val();
            var status = $('#status_filter').val();
            var payment_method = $('#payment_method_filter').val();

            init_table(keyword, month, year, status, payment_method);
        }


        function init_table(keyword = '', month = '', year = '', status = '', payment_method = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#data-table');
            table.destroy();

            var filterData = {
                'keyword': keyword,
                'month': month,
                'year': year,
                'status': status,
                'payment_method': payment_method
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
                    url: "{{ route('invoice.invoice_data') }}",
                    data: filterData
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
                        data: 'copy_link',
                        name: 'copy_link'
                    },
                    {
                        data: 'payment_url',
                        name: 'payment_url'
                    },
                    {
                        data: 'sync_jurnal',
                        name: 'sync_jurnal'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'termin_action',
                        name: 'termin_action'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'total_rupiah',
                        name: 'total_rupiah'
                    },
                    {
                        data: 'grand_total',
                        name: 'grand_total'
                    },
                    {
                        data: 'currency',
                        name: 'currency'
                    },
                    {
                        data: 'kurs',
                        name: 'kurs'
                    },
                    {
                        data: 'client',
                        name: 'client'
                    },
                    {
                        data: 'created',
                        name: 'created'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },

                ]
            });

            $.ajax({
                    url: "{{ route('invoice.chart') }}",
                    type: 'GET',
                    data: filterData,
                    dataType: 'json',
                })
                .done(function(data) {
                    var total_paid = data.total_paid;

                    $('#total_paid').text(total_paid);
                })
                .fail(function() {
                    alert('Load data failed.');
                });
        }

        function detail(id) {
            $.ajax({
                    url: "{{ route('invoice.invoice.show', ':id') }}".replace(':id', id),
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

        function payment(id) {
            $.ajax({
                    url: "{{ route('invoice.todoTermin', ':id') }}".replace(':id', id),
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
                            url: "{{ route('invoice.invoice.destroy', ':id') }}".replace(':id', id),
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

        function changeBulkPaid(ids) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to undo this action!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Update Status!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        'ids': ids
                    };
                    $.ajax({
                            url: "{{ route('invoice.changeBulkPaid') }}",
                            type: 'POST',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Updated!', data['message'], 'success');
                            init_table();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                        });
                }
            });
        }

        $(document).on('click', '.copyLinkButton', function() {
            var paymentLink = $(this).data('url'); // Ambil URL dari data attribute

            // Membuat elemen textarea untuk sementara
            var tempInput = document.createElement('textarea');
            tempInput.value = paymentLink;
            document.body.appendChild(tempInput);

            // Menyalin teks ke clipboard
            tempInput.select();
            document.execCommand('copy');

            // Menghapus elemen textarea setelah menyalin
            document.body.removeChild(tempInput);

            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "toastClass": "toast toast-custom"
            };

            toastr.success('Link copied to clipboard!');
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
                url: "{{ route('invoice.single.sync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        filter();
                    }
                }
            });
        }
    </script>
@endsection
