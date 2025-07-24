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
                        <li class="breadcrumb-item"><a href="{{ route('staff.index') }}">Staff</a></li>
                        <li class="breadcrumb-item">Edit Staff</li>
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
                                <h5 class="card-title">Edit Staff</h5>
                            </div>
                            <form method="POST" action="{{ route('staff.update',['staff' => $data->id]) }}">
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
                                                    <label>Nama Staff:</label>
                                                    <input type="text" name="fullname" id="name" class="form-control cust-control" placeholder="Masukkan nama cabang" value="{{ $data->fullname }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                <label>Cabang:</label>
                                                    <select name="branch_id" id="selectEditBranchStaff" class="form-control cust-control">
                                                        @foreach($branches as $branch)
                                                        <option value="{{ $branch->id }}" @if($branch->id == $data->branch_id) selected @endif>{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Posisi:</label>
                                                    <select name="position_id" class="form-control cust-control">
                                                        @foreach($positions as $position)
                                                            <option value="{{ $position->id }}" @if($position->id == $data->position_id) selected @endif>{{ $position->position }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Username:</label>
                                                    <input name="username" id="username" class="form-control cust-control" value="{{ $data->username }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Password:</label>
                                                    <input name="password" type="password" id="password" class="form-control cust-control" placeholder="Masukkan password baru, kosongkan jika tidak ingin ganti password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <label>Nomor Telepon:</label>
                                                <input name="phone" id="phone" type="numeric" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" class="form-control cust-control" value="{{ $data->phone }}" placeholder="Masukkan nomor telepon. Contoh : 081234567890" maxlength="12" >
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <label>Tanggal Mulai Kerja:</label>
                                                <input name="start_date" type="date" id="start_date" class="form-control cust-control" value="{{ $data->start_date }}">
                                            </div>
                                        </div>
                                        <div class="form-group mtop20">
                                            <label>Jam Masuk:</label>
                                            <input name="clock_in" type="time" id="clock_in" value="{{ $data->clock_in ?? null }}"
                                                class="form-control cust-control">
                                        </div>
                                        <div class="form-group mtop20">
                                            <label>Jam Pulang:</label>
                                            <input name="clock_out" type="time" id="clock_out" value="{{ $data->clock_out ?? null }}"
                                                class="form-control cust-control">
                                        </div>
                                        <div class="form-group mtop20">
                                            @include('main.staff.holiday')
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <label>Status:</label>
                                                <select name="is_active" class="form-control cust-control">
                                                    <option value="1" @if($data->is_active == 1) selected @endif>Aktif</option>
                                                    <option value="0" @if($data->is_active == 0) selected @endif>Non Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mtop20">
                                            <label>PIN</label>
                                            <input name="pin" type="password" id="pin" class="form-control cust-control" placeholder="Masukkan pin baru, kosongkan jika tidak ingin ganti pin">
                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <button style="float: right;" class="btn btn-primary" id="submitBtn">Simpan</button>
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
        $('#selectEditBranchStaff').select2();

        function validatePhoneNumber(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }
    });
</script>
@endsection