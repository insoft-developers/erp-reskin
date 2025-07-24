@extends('master')

{{-- @section('style')
    <style>
        .ck.ck-editor__editable_inline{
            height: 200px;
        }
    </style>
@endsection --}}

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
                    <li class="breadcrumb-item"><a href="{{ url('/setting') }}">Pengaturan Printer POS/Kasir</a></li>
                    <li class="breadcrumb-item">Pengaturan Printer POS/Kasir Akun</li>
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
                            <h5 class="card-title">Pengaturan Printer POS/Kasir</h5>
                        </div>
                        <form method="POST" action="{{ route('save-printer') }}" enctype="multipart/form-data" id="formUpdate">
                            @csrf

                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <div class="row">
                                        @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="row mtop10">
                                        <div class="col-md-12">
                                            <label for="is_rounded" class="form-label">Pembulatan Transaksi
                                                <div>
                                                    <small>Jika Transaksi dibawah Rp 500 Transaksi akan dibulatkan ke bawah, jika diatas Rp 500 Transaksi akan dibulatkan ke atas</small>
                                                </div>
                                            </label>
                                            <div class="input-group">
                                                <div class="form-check" style="margin-right: 10px;">
                                                    <input class="form-check-input" type="radio" name="is_rounded"
                                                        id="is_rounded1" value="1" {{ ($data->is_rounded == true) ? 'checked' : ''}}>
                                                    <label class="form-check-label" for="is_rounded1">
                                                        Yes
                                                    </label>
                                                </div>
                                                <div class="form-check" style="margin-right: 10px;">
                                                    <input class="form-check-input" type="radio" name="is_rounded"
                                                        id="is_rounded2" value="0" {{ ($data->is_rounded == false) ? 'checked' : ''}}>
                                                    <label class="form-check-label" for="is_rounded2">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                       <div class="col-md-12">
                                            <label for="printer_connection" class="form-label">Koneksi Printer</label>
                                            <div class="input-group">
                                                <div class="form-check" style="margin-right: 10px;">
                                                    <input class="form-check-input" type="radio" name="printer_connection"
                                                        id="printer_connection1" value="bluetooth" {{ ($data->printer_connection == 'bluetooth') ? 'checked' : ''}}>
                                                    <label class="form-check-label" for="printer_connection1">
                                                        Bluetooth
                                                    </label>
                                                </div>
                                                <div class="form-check" style="margin-right: 10px;">
                                                    <input class="form-check-input" type="radio" name="printer_connection"
                                                        id="printer_connection2" value="usb" {{ ($data->printer_connection == 'usb') ? 'checked' : ''}}>
                                                    <label class="form-check-label" for="printer_connection2">
                                                        USB
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

<div class="col-md-12" style="display: none;">
    <label for="printer_paper_size" class="form-label">Ukuran Kertas</label>
    <div class="input-group">
        <div class="form-check" style="margin-right: 10px;">
            <input class="form-check-input" type="radio" name="printer_paper_size"
                id="printer_paper_size1" value="5.8" {{ ($data->printer_paper_size == '5.8' ? 'checked' : '')}} >
            <label class="form-check-label" for="printer_paper_size1">
                5.8mm
            </label>
        </div>
        <div class="form-check" style="margin-right: 10px;">
            <input class="form-check-input" type="radio" name="printer_paper_size"
                id="printer_paper_size2" value="8.0" {{ ($data->printer_paper_size == '8.0' ? 'checked' : '')}} >
            <label class="form-check-label" for="printer_paper_size2">
                8.0mm
            </label>
        </div>
    </div>
</div>



                                            <div class="mtop20">
                                                <label for="printer_port" class="form-label">Custom Footer</label>
                                                <textarea class="form-control cust-cuntrol" id="printer_custom_footer" name="printer_custom_footer" placeholder="Custom Footer">{{ $data->printer_custom_footer }}</textarea>
                                            </div>
 

<div class="row mtop20">
    <div class="col-md-12 d-flex justify-content-start">
        <a href="{{ url('/setting') }}" class="btn btn-secondary me-3">Kembali</a>
        <button class="btn btn-primary" type="submit" id="submitBtn">Simpan</button>
    </div>
</div>


                                    <div class="mtop30"></div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- [Recent Orders] end -->
                <!-- [] start -->
            </div>

        </div>
        <!-- [ Main Content ] end -->

    </div>
</main>
@endsection
@section('js')

<script>
    $(document).ready(function() {
        CKEDITOR.replace("printer_custom_footer");
        
        document.getElementById('formUpdate').addEventListener('submit', function(event) {
        event.preventDefault();

        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    
        const form = event.target;
        const formData = new FormData(form);
        const url = form.action;
    
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.errors) {
                let errorMessages = '';
                for (const [field, messages] of Object.entries(data.errors)) {
                    errorMessages += messages.join('<br>') + '<br>';
                }
    
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMessages
                });
            }else if(!data.status){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!'
            });
        });
    });
    }); 
    
</script>
@endsection
