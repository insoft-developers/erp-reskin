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
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Cabang</a></li>
                        <li class="breadcrumb-item">Edit Cabang</li>
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
                                <h5 class="card-title">Edit Cabang</h5>
                            </div>
                            <form method="POST" action="{{ route('branch.update',['branch' => $data->id]) }}">
                                @csrf
                                @method('PUT')
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
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Nama Cabang:</label>
                                                    <input type="text" name="name" id="name" class="form-control cust-control" placeholder="Masukkan nama cabang" value="{{ $data->name }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>No Telepon:</label>
                                                    <input type="text" name="phone" id="name" class="form-control cust-control" placeholder="Masukkan nomor telepon" value="{{ $data->phone }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Alamat</label>
                                                    <textarea name="address" id="" cols="30" class="form-control" rows="10">{{ $data->address}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Kecamatan, Kota, Provinsi</label>
                                                    <select class="form-control" id="districtEdit" name="district_id">
                                                        @foreach($district as $d)
                                                            <option value="{{ $d->district_id }}" {{ $data->district_id == $d->district_id ? "selected" : ""}}>{{ $d->provinsi }}, {{ $d->kabupaten }}, {{ $d->distrik }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <button style="float: right;" class="btn btn-primary">Simpan</button>
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
    $(document).ready(function(){
        $('#districtEdit').select2();
    });
</script>
@endsection