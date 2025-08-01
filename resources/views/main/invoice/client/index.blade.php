@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        
            
            <div class="content" style="background: whitesmoke;">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Klien</h5>

                                <button style="float: right;" onclick="createData()"
                                    class="btn btn-success">
                                    <i class="fa fa-plus"></i> Buat Baru
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

                                <div class="table-responsive" style="margin-top:10px;">
                                    <table style="width: 100%;" class="table table-striped" id="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Alamat</th>
                                                <th>Phone</th>
                                                <th>Mobile</th>
                                                <th>Fax</th>
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
                "language": {
                    search: ' ',
                    sLengthMenu: '_MENU_',
                    searchPlaceholder: "Search",
                    sLengthMenu: 'Row Per Page _MENU_ Entries',
                    info: "_START_ - _END_ of _TOTAL_ items",
                    paginate: {
                        next: '<i class="isax isax-arrow-right-1"></i>',
                        previous: '<i class="isax isax-arrow-left"></i> '
                    },
                },
                dom: 'Blfrtip',
                columnDefs: [
                    {
                        target: 0,
                        visible: false,
                        searchable: false
                    },
                ],
                
                ajax: {type: "GET", url: "{{ route('invoice.client_data') }}", data:{'keyword':keyword}},
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'name',name: 'name'},
                    {data: 'email',name: 'email'},
                    {data: 'address',name: 'address'},
                    {data: 'phone',name: 'phone'},
                    {data: 'mobile',name: 'mobile'},
                    {data: 'fax',name: 'fax'},
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
                        url: "{{ route('invoice.client.destroy', ':id') }}".replace(':id', id),
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
                url: "{{ route('invoice.client.create') }}",
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
                url: "{{ route('invoice.client.edit', ':id') }}".replace(':id', id),
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
