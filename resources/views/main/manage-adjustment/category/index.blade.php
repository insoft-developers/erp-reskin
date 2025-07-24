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
                        <li class="breadcrumb-item"><a href="{{ route('adjustment.category.index') }}">Kategori Penyesuaian</a></li>
                        <li class="breadcrumb-item">Kategori Penyesuaian Builder</li>
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
                                <h5 class="card-title">Kategori Penyesuaian</h5>

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
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="searchData" placeholder="Cari disini.."
                                                class="form-control cust-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <button type="button"
                                            class="btn btn-danger" id="delete-btn" hidden>
                                            <i class="feather-trash-2"></i> Bulk Delete
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="data-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    {{-- CHECKBOX SELECT ALL --}}
                                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox" id="checkAll" />
                                                    </div>
                                                </th>
                                                <th>Nama Kategori</th>
                                                <th>Kode Kategori</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
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

        $('#checkAll').on('click', function() {
            $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));

            if ($('tbody input[type="checkbox"]:checked').length > 0) {
                $('#delete-btn').removeAttr('hidden');
            }else{
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
                        visible: true,
                        searchable: false
                    },
                ],
                
                ajax: {type: "GET", url: "{{ route('adjustment.category_data') }}", data:{'keyword':keyword}},
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false},
                    {data: 'name',name: 'name'},
                    {data: 'code',name: 'code'},
                    {data: 'action',name: 'action',orderable: false,searchable: false}
                ]
            });
        }

        function deleteData(event, id) {
            event.preventDefault();
            Swal.fire({
                title: 'Yakin Menghapus?',
                text: 'Jangan pernah menghapus data yang sudah pernah dibuat transaksi!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, yakin hapus!',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'DELETE',
                    };
                    $.ajax({
                        url: "{{ route('adjustment.category.destroy', ':id') }}".replace(':id', id),
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

        function bulkDelete(ids) {
            Swal.fire({
                title: 'Yakin Menghapus?',
                text: 'Jangan pernah menghapus data yang sudah pernah dibuat transaksi!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, yakin hapus!',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var postForm = {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'DELETE',
                        'ids': ids
                    };
                    $.ajax({
                        url: "{{ route('adjustment.category.destroyAll') }}",
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
                url: "{{ route('adjustment.category.create') }}",
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
                url: "{{ route('adjustment.category.edit', ':id') }}".replace(':id', id),
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
