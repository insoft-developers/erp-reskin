@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        
            <div style="background: whitesmoke;" class="content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Landing Page</h5>

                                <a style="float: right;" href="{{ route('landing-page.create') }}" class="btn btn-success">Buat Baru</a>
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
                                <div style="min-width: 300px; overflow-y: auto">
                                    <table class="table table-striped" id="landing-pages-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Produk</th>
                                                <th>Judul</th>
                                                <th>Slug</th>
                                                <th>Bump Produk</th>
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
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#landing-pages-table').DataTable({
                processing: true,
                serverSide: true,
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
                ajax: '{{ route('landing-pages.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'slug',
                        name: 'slug'
                    },
                    {
                        data: 'bump_product',
                        name: 'bump_product',
                        render: function(data) {
                            var badgeClass = data == 'yes' ? 'badge bg-success' : 'badge bg-danger';
                            return '<div class="' + badgeClass + '">' + data + '</div>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
