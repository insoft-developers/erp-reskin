@extends($userKey ? 'master-preview' : 'master')

@section('style')
    <style>
        .select2-container {
            z-index: 0;
            /* Adjust the z-index as needed */
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
                            <li class="breadcrumb-item"><a href="{{ route('visit.index') }}">Laporan Marketing</a></li>
                            <li class="breadcrumb-item">Laporan Absensi</li>
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
                                    <h5 class="card-title">Laporan Kunjungan</h5>

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
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-2">
                                                    <select id="selectRange" class="form-select">
                                                        <option value="isToday">Hari Ini</option>
                                                        <option value="isYesterday">Hari Kemarin</option>
                                                        <option value="isThisMonth">Bulan Ini</option>
                                                        <option value="isLastMonth">Bulan Kemarin</option>
                                                        <option value="isThisYear">Tahun Ini</option>
                                                        <option value="isLastYear">Tahun Kemarin</option>
                                                        <option value="isRangeDate">Range Tanggal</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" id="startDate" class="form-control"
                                                        placeholder="dd/mm/yyyy" disabled>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" id="endDate" class="form-control"
                                                        placeholder="dd/mm/yyyy" disabled>
                                                </div>
                                                <div class="col-md-3">
                                                    <select id="isApproved" class="form-select">
                                                        <option value="">Pilih Status</option>
                                                        <option value="1">Approved</option>
                                                        <option value="0">Not Approved</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nama Marketing</th>
                                                    <th>Nama Tempat</th>
                                                    <th>Menemui</th>
                                                    <th>Kontak</th>
                                                    <th>Waktu</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
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
                            <h5 class="card-title">Laporan Kunjungan</h5>
                        </div>
                    @endif
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
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-2">
                                        <select id="selectRange" class="form-select">
                                            <option value="isToday">Hari Ini</option>
                                            <option value="isYesterday">Hari Kemarin</option>
                                            <option value="isThisMonth">Bulan Ini</option>
                                            <option value="isLastMonth">Bulan Kemarin</option>
                                            <option value="isThisYear">Tahun Ini</option>
                                            <option value="isLastYear">Tahun Kemarin</option>
                                            <option value="isRangeDate">Range Tanggal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="startDate" class="form-control"
                                            placeholder="dd/mm/yyyy" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="endDate" class="form-control"
                                            placeholder="dd/mm/yyyy" disabled>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="isApproved" class="form-select">
                                            <option value="">Pilih Status</option>
                                            <option value="1">Approved</option>
                                            <option value="0">Not Approved</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Marketing</th>
                                        <th>Nama Tempat</th>
                                        <th>Menemui</th>
                                        <th>Kontak</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Action</th>
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
    <div class="modal fade" role="dialog" aria-hidden="true" id="modal-ce">
        <div class="modal-dialog" id="content-modal-ce">

        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            init_table();
        });

        $('.select2').select2({
            placeholder: "Pilih Sales",
        });

        $(document).on('input', ['#selectRange', '#startDate', '#endDate', '#marketingId', '#isApproved'], function() {
            init_table();
        })

        $(document).on('change', '#selectRange', function() {
            if ($(this).val() == 'isRangeDate') {
                $('#startDate').prop('disabled', false);
                $('#endDate').prop('disabled', false);
            } else {
                $('#startDate').prop('disabled', true);
                $('#endDate').prop('disabled', true);
            }
        })

        function init_table() {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#data-table');
            table.destroy();

            let data = {};

            data.isToday = $('#selectRange').val() == 'isToday' ? true : null;
            data.isYesterday = $('#selectRange').val() == 'isYesterday' ? true : null;
            data.isThisMonth = $('#selectRange').val() == 'isThisMonth' ? true : null;
            data.isLastMonth = $('#selectRange').val() == 'isLastMonth' ? true : null;
            data.isThisYear = $('#selectRange').val() == 'isThisYear' ? true : null;
            data.isLastYear = $('#selectRange').val() == 'isLastYear' ? true : null;
            if ($('#selectRange').val() == 'isRangeDate') {
                data.startDate = $('#startDate').val() != "" ? $('#startDate').val() : null;
                data.endDate = $('#endDate').val() != "" ? $('#endDate').val() : null;
            }
            data.marketingId = $('#marketingId').val() != "" ? $('#marketingId').val() : null;
            data.isApproved = $('#isApproved').val() != "" ? parseInt($('#isApproved').val()) : null;

            @if($userKey)
                data.user_key = "{{$userKey}}";
            @endif
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Blfrtip',

                columnDefs: [{
                    target: 0,
                    visible: false,
                    searchable: false,
                    user_key: "{{$userKey ?? 'null'}}",
                }],

                ajax: {
                    type: "GET",
                    url: "{{ !$userKey ? route('visit_data') : url('/api/report/visit-data') }}",
                    data: data
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'Nama Marketing'
                    },
                    {
                        data: 'address',
                        name: 'Nama Tempat'
                    },
                    {
                        data: 'visited',
                        name: 'Menemui'
                    },
                    {
                        data: 'contact',
                        name: 'Kontak'
                    },
                    {
                        data: 'created_at',
                        name: 'Waktu'
                    },
                    {
                        data: 'is_approved',
                        name: 'Status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
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
                            url: "{{ !$userKey ? route('visit.destroy', ':id') : route('preview.visit.destroy', ':id') }}".replace(':id', id),
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

        function editData(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                icon: 'warning',
                showDenyButton: true,
                confirmButtonText: 'Yes, approve it!',
                denyButtonText: 'No, not approve!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        'is_approved': 1 // 1 = approved
                    };
                    $.ajax({
                            url: "{{ !$userKey ? route('visit.update', ':id') : route('preview.visit.update', ':id') }}".replace(':id', id),
                            type: 'PUT',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Approved!', data['message'], 'success');
                            init_table();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while approving the record.', 'error');
                        });
                }
                if (result.isDenied) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        'is_approved': 0 // 0 = not approved
                    };
                    $.ajax({
                            url: "{{ !$userKey ? route('visit.update', ':id') : route('preview.visit.update', ':id') }}".replace(':id', id),
                            type: 'PUT',
                            data: postForm,
                            dataType: 'json',
                        })
                        .done(function(data) {
                            Swal.fire('Not Approved!', data['message'], 'success');
                            init_table();
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'An error occurred while approving the record.', 'error');
                        });
                }
            });
        }

        function showPhoto(photo) {
            $.ajax({
                    url: "{{ !$userKey ? route('visit.show_photo') : route('preview.visit.show_photo') }}",
                    type: 'GET',
                    data: {
                        'photo': photo
                    }
                })
                .done(function(data) {
                    $('#content-modal-ce').html(data);

                    $("#modal-ce").modal("show");
                })
                .fail(function() {
                    Swal.fire('Error!', 'An error occurred while creating the record.', 'error');
                });
        }

        function show(id) {
            $.ajax({
                    url: "{{ !$userKey ? route('visit.show', ':id') : route('preview.visit.show', ':id') }}".replace(':id', id),
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
    </script>
@endsection
