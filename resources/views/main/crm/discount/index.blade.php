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
                        <li class="breadcrumb-item"><a href="{{ route('crm.discount.index') }}">Diskon</a></li>
                        <li class="breadcrumb-item">Diskon Builder</li>
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
                                <h5 class="card-title">Diskon</h5>

                                <button onclick="createData()"
                                    class="btn btn-primary">
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
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Code</th>
                                                <th>Minimal Order</th>
                                                <th>Nilai</th>
                                                <th>Expired Date</th>
                                                <th>Status</th>
                                                {{-- <th>Maksimal Dipakai</th> --}}
                                                {{-- <th>Boleh Digunakan Beberapa Kali</th> --}}
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
            init_table($(this).val());
        })

        function init_table(keyword = '') {
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
                
                ajax: {type: "GET", url: "{{ route('crm.discount_data') }}", data:{'keyword':keyword}},
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'name',name: 'name'},
                    {data: 'code',name: 'code'},
                    {data: 'min_order',name: 'min_order'},
                    {data: 'value',name: 'value'},
                    {data: 'expired_at',name: 'expired_at'},
                    {data: 'status',name: 'status'},
                    // {data: 'max_use',name: 'max_use'},
                    // {data: 'allowed_multiple_use',name: 'allowed_multiple_use'},
                    {data: 'action',name: 'action',orderable: false,searchable: false}
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
                        url: "{{ route('crm.discount.destroy', ':id') }}".replace(':id', id),
                        type: 'POST', 
                        data : postForm,
                        dataType  : 'json',
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
                url: "{{ route('crm.discount.create') }}",
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
                url: "{{ route('crm.discount.edit', ':id') }}".replace(':id', id),
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
