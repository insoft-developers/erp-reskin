@extends('master')
@section('style')
<style>
    .dt-paging {
        margin-top: 0px !important; /* Adjust the value as needed */
    }
</style>
@endsection
@section('content')
<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10"> Qrcode </h5>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Kelola Qrcode</h5>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div class="container mtop30 main-box">
                                <input type="hidden" name="iduser" id="user" value="{{$user->username}}">
                                <div class="row">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="">Generate QR Code</label>
                                            <input type="number" id="jml_meja" name="jml_meja" placeholder="Jumlah Meja" class="form-control" min="1" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="branch">Pilih Cabang</label>
                                            <select name="" id="branch" class="form-control">
                                                <option value="">-- Pilih Cabang --</option>
                                                @foreach($branch as $br)
                                                    <option value="{{$br->id}}">{{$br->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mt-4">
                                            <button id="submitMeja" class="btn btn-primary">
                                                <i class="fa fa-qrcode"></i> Generate
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 float-end text-end">
                                            <div id="checkedValue" class="d-flex mt-4 d-none">
                                                <button class="btn btn-danger" id="bulk-delete-btn" style="margin-right:5px"><i class="fa fa-trash mr-2"></i> Hapus Data</button>
                                                <button class="btn btn-success bulk-status-btn" id="bulk-available-btn" data-status="Available" style="margin-right:5px"><i class="fa fa-pencil mr-2"></i> Set Available</button>
                                                <button class="btn btn-warning bulk-status-btn" id="bulk-reserved-btn" data-status="Reserved"><i class="fa fa-pencil mr-2"></i> Set Reserved</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="mtop30"></div> -->
                                    <div class="table-responsive mt-3">
                                        <table id="table-qr" class="table" width="100%">
                                            <thead>
                                                <tr class="border-b">
                                                    <th>
                                                        <div class="btn-group mb-1">
                                                            <div class="custom-control custom-checkbox ms-1">
                                                                <input type="checkbox" class="custom-control-input" id="checkAllQr">
                                                                <label class="custom-control-label" for="checkAllQr"></label>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th>QR Code</th>
                                                    <th>Nama Meja</th>
                                                    <th>Cabang</th>
                                                    <th>Ketersediaan</th>
                                                    <th>Dibuat</th>
                                                    <th>Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="items-wrapper"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@include('main.qrcode.qrcode_modal')
@include('main.qrcode.qrcode_modal_edit')
@include('main.qrcode.qrcode_modal_bulk_edit')
@endsection

@section('js')
<script>
    "use strict";
    $(document).ready(function () {
        // $('#branch').select2();
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var table = $('#table-qr');
        let pages = 0;
        let checkedValues = [];
        $('#submitMeja').on('click', function(e){
            var jml_meja = $("#jml_meja").val();
            var username = $("#user").val();
            var branch = $("#branch").find(":selected").val();
            if(jml_meja == 0 || jml_meja == undefined){
                swal.fire('Oops...', 'Jumlah meja tidak boleh kosong!', 'error');
                return
            }
            $.ajax({
                url: "{{route('add-qrcode-meja')}}",
                type: 'POST',
                data: {
                    '_token':csrf_token,
                    jumlah: jml_meja,
                    branch: branch,
                    username: username
                }
            })
            .done(function(response) {
                swal.fire(response.title, response.message, response.icon).then(() => {
                    if(response.success)
                        location.reload();
                })
            })
            .fail(function() {
                swal.fire('Oops...', 'Something went wrong with data !', 'error');
            });

        });
        table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            ajax: {
                url: `{{ url('/ajax-get-data-qrcode') }}`,
                type: 'GET',
                data: function(d){
                    d.pages = pages;

                }
            },
            columns: [
                {
                    data: 'id',
                    render: function(data, type, row, meta){
                        return '<div class="item-checkbox ms-1 single-item">'
                                    +'<div class="custom-control custom-checkbox">'
                                        +'<input type="checkbox" class="custom-control-input checkbox" id="checkBox_'+data+'" value="'+row.id+'#'+row.no_meja+'#'+row.availability+'">'
                                        +'<label class="custom-control-label" for="checkBox_'+data+'"></label>'
                                    +'</div>'
                                +'</div>'
                    },
                    orderable: false
                },
                {
                    data: 'qr_id',

                },
                {
                    data: 'no_meja',

                },
                {
                    data: 'name',
                    render: function(data, type, row, meta){
                        return data
                    },
                },
                {
                    data: 'availability',
                    orderable: false
                },
                {
                    data: 'created_at',
                    render: function(data,type,full,meta){
                        moment.locale('id')
                        return moment(data).format("LLLL")
                    }

                },
                {
                    data: null,
                    render: function(data, type, row, meta){
                        return '<div class="d-flex">'
                        + '<button class="btn btn-sm btn-primary open-modal-btn" style="margin-right:5px" data-meja="'+row.no_meja+'" data-number="'+row.qr_id+'" data-qrcode="'+row.qr_code+'" data-available="'+row.availability+'" ><i class="fa fa-eye"></i> &nbsp; Detail</button>'
                        + '<button class="btn btn-sm btn-success edit-modal-btn mr-3" style="margin-right:5px" data-id="'+row.id+'" data-meja="'+row.no_meja+'" data-number="'+row.qr_id+'" data-available="'+row.availability+'"><i class="fa fa-pencil"></i> &nbsp; Edit</button>'
                        + '<button class="btn btn-sm btn-danger btn-hapus mr-3" data-key="'+row.id+'"><i class="fa fa-trash"></i> &nbsp; Hapus</button>'
                        + '</div>'
                    },
                    orderable: false
                }
            ]
        })

        table.on('click', '.open-modal-btn', function() {
            var number = $(this).data('number');
            var meja = $(this).data('meja');
            var qrcode = $(this).data('qrcode');
            var availability = $(this).data('available');

            $('#noMeja').text(meja);
            $('#qrNumber').text(number);
            $('#qrCode').html(qrcode);
            $('#availability').html(availability);

            $('#qrModal').modal('show');
        });
        table.on('click', '.edit-modal-btn', function() {
            var id = $(this).data('id');
            var number = $(this).data('number');
            var meja = $(this).data('meja');
            var qrcode = $(this).data('qrcode');
            var availability = $(this).data('available');


            $('#idEdit').val(id);
            $('#noMejaEdit').val(meja);
            $('#availabilityEdit').val(availability);

            $('#qrModalEdit').modal('show');
        });
        $('#submitEdit').on('click', function(e){
            var id = $('#idEdit').val();
            var nomor = $('#noMejaEdit').val();
            var availability = $('#availabilityEdit').val();
            $.ajax({
                url: "{{route('edit-qrcode-meja')}}",
                type: 'POST',
                data: {
                    '_token':csrf_token,
                    id: id,
                    nomor: nomor,
                    availability: availability
                }
            })
            .done(function(res) {
                swal.fire(res.title, res.message, res.icon).then(() => {
                    if(res.success){
                        $('#qrModalEdit').modal('hide');
                        location.reload();
                    }
                })

            })
            .fail(function() {
                swal.fire('Oops...', 'Something went wrong with data !', 'error');
            });

        });
        table.on('click', '.btn-hapus', function(e){
            e.preventDefault();
            let key = $(this).data('key');
            Swal.fire({
                title: 'Peringatan',
                text: "Apakah anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                            url: "{{route('delete-qrcode-meja')}}",
                            type: 'POST',
                            data: {
                                '_token':csrf_token,
                                key
                            }
                        })
                        .done(function(response) {
                            swal.fire(response.title, response.text, response.icon).then(() => {
                                location.reload();
                            });

                        })
                        .fail(function() {
                            swal.fire('Oops...', 'Something went wrong with data !', 'error');
                        });
                }

            })
        })


        $("#checkAllQr").change(function () {
            if (this.checked) {
            $(".checkbox").each(function () {
                this.checked = true;
                $(this).closest(".single-items").addClass("selected");
            });
        } else {
            $(".checkbox").each(function () {
                this.checked = false;
                $(this).closest(".single-items").removeClass("selected");
            });
        }
        updateCheckedValues();
        })
        function updateMainCheckboxState() {
            var allChecked = false;
            $(".checkbox").each(function () {
                if (!this.checked) {
                    allChecked = false;
                }

            });

            $("#checkAllQr").prop("checked", allChecked);
        }
        function updateCheckedValues() {
            checkedValues = []
            $(".checkbox:checked").each(function () {
                checkedValues.push($(this).val());
            });
            if(checkedValues.length > 0){
                $('#checkedValue').removeClass('d-none');
                $('#bulk-delete-btn').text(`Hapus (${checkedValues.length}) Data`);
                $('#bulk-available-btn').text(`Set Available (${checkedValues.length}) Data`);
                $('#bulk-reserved-btn').text(`Set Reserved (${checkedValues.length}) Data`);
            }else{
                $('#checkedValue').addClass('d-none');
            }
        }
        // Handle individual checkboxes
        $(".checkbox").click(function () {
            updateMainCheckboxState();
            updateCheckedValues();
        });

        // Toggle class on checkbox click
        $(".items-wrapper").on("click", "input:checkbox", function () {
            $(this).closest(".single-items").toggleClass("selected", this.checked);
            updateMainCheckboxState();
            updateCheckedValues();
        });

        // Ensure all initially checked checkboxes have the 'selected' class
        $(".items-wrapper input:checkbox:checked").closest(".single-items").addClass("selected");

        // Initial state check
        updateMainCheckboxState();
        updateCheckedValues();

        $('#bulk-delete-btn').on('click', function(e){
            Swal.fire({
                title: 'Peringatan',
                text: "Apakah anda yakin ingin menghapus "+checkedValues.length+" data?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
            }).then((result) => {
                const ids = checkedValues.map(item => item.split('#')[0]);
                if (result.value) {
                    $.ajax({
                            url: "{{route('delete-qrcode-meja')}}",
                            type: 'POST',
                            data: {
                                '_token':csrf_token,
                                key: ids
                            }
                        })
                        .done(function(response) {
                            swal.fire(response.title, response.text, response.icon).then(() => {
                                location.reload();
                            });

                        })
                        .fail(function() {
                            swal.fire('Oops...', 'Something went wrong with data !', 'error');
                        });
                }

            })
        })
        $('.bulk-status-btn').on('click', function(){
            Swal.fire({
                title: 'Informasi',
                text: "Apakah anda yakin ingin mengubah "+checkedValues.length+" data?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
            }).then((result) => {
                const ids = checkedValues.map(item => item.split('#')[0]);
                const status = $(this).data('status');
                if (result.value) {
                    $.ajax({
                            url: "{{route('set-qrcode-availaibility')}}",
                            type: 'POST',
                            data: {
                                '_token':csrf_token,
                                key: ids,
                                status: status
                            }
                        })
                        .done(function(response) {
                            swal.fire(response.title, response.text, response.icon).then(() => {
                                location.reload();
                            });

                        })
                        .fail(function() {
                            swal.fire('Oops...', 'Something went wrong with data !', 'error');
                        });
                }

            })
        })
    });

</script>
@endsection
