@extends('master')

@section('content')
<style>
    .nav-item{
        font-size: 15px;
        font-weight: 600;
    }
</style>
<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10"></h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('landing-page.index') }}">Followup & Upselling</a>
                    </li>
                    <li class="breadcrumb-item">Followup & Upselling Builder</li>
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
                <div class="col-md-12">
                    <div class='card'>
                        <div class='card-body'>
                            <div class="mb-3">
                                <h5>Followup & Upselling</h5>
                                <small>
                                    Silahkan ketik parameter untuk menambahkan data user [name], [phone], [kecamatan], [kelurahan], [alamat]
                                </small>
                            </div>
                            
                            <ul class="nav nav-tabs-custom card-header-tabs border-top" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link px-3 active"
                                        data-bs-toggle="tab" href="#password" role="tab" wire:click="passwordTab">
                                        Followup
                                    </a>
                                </li>
                                <li class="nav-item disable">
                                    <a class="nav-link px-3" data-bs-toggle="tab" href="#profile-pengguna" role="tab">
                                        Upselling
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane active" id="password" role="tabpanel">
                            <div class='card'>
                                <div class='card-body'>
                                    <form action='{{ route('crm.followup.update', [session('id')]) }}' method='POST' enctype='multipart/form-data'>
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="type" value="followup">
                                        @foreach ($followup as $item)
                                            <div class="mb-4">
                                                <label for="whatsapp_followup_1" class="form-label">{{ $item->name }}</label>
                                                <textarea name="{{ $item->name }}" id="whatsapp_followup_1" class="form-control" cols="30" rows="5">{{ $item->text }}</textarea>
                                            </div>
                                        @endforeach

                                        <button type='submit' class='btn btn-primary'>Submit</button>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
    
                        <div class="tab-pane" id="profile-pengguna" role="tabpanel">
                            <div class='card'>
                                <div class='card-body'>
                                    <form action='{{ route('crm.followup.update', [session('id')]) }}' method='POST' enctype='multipart/form-data'>
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="type" value="upselling">
                                        @foreach ($upselling as $item)
                                            <div class="mb-4">
                                                <label for="whatsapp_followup_1" class="form-label">{{ $item->name }}</label>
                                                <textarea name="{{ $item->name }}" id="whatsapp_followup_1" class="form-control" cols="30" rows="5">{{ $item->text }}</textarea>
                                            </div>
                                        @endforeach

                                        <button type='submit' class='btn btn-primary'>Submit</button>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->

    </div>
</main>
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
                
                ajax: {type: "GET", url: "{{ route('crm.followup_data') }}", data:{'keyword':keyword}},
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'name',name: 'name'},
                    {data: 'type',name: 'type'},
                    {data: 'text',name: 'text'},
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
                        url: "{{ route('crm.followup.destroy', ':id') }}".replace(':id', id),
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
</script>
@endsection