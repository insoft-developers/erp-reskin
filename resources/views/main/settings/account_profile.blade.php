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
                        <li class="breadcrumb-item"><a href="{{ url('/setting') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">Pengaturan Akun</li>
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
                                <h5 class="card-title">Pengaturan Akun</h5>
                            </div>
                            {{-- @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif --}}
                            <form method="POST" action="{{ route('account.profile.update.api',['id' => $account->id]) }}" enctype="multipart/form-data" id="formEdit">
                                @csrf
                                @method('PATCH')
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
                                        <div class="row mt20">
                                            <h5>Informasi Akun</h5>
                                        </div>
                                            <div class="row mtop20">
                                                <div class="col-md-12">
                                                    <div class="p-4 bg-soft-warning rounded-3">
                                                        <p class="fs-12 text-dark"><strong>Ganti Email & Nomor Handphone</strong><br>
                                                            Jika ingin mengganti email dan nomor handphone silakan request melalui "Live Chat" di Pojok Kanan Bawah
                                                        </p>
                                                        <p class="fs-12 text-dark"><strong>Ganti Username</strong><br>
                                                            JIka ingin menggantu username silakan menuju ke menu "Storefront" lalu "Pengaturan Toko Online"
                                                        </p>

                                                    </div>
                                                </div>
                                            </div>

                                        
                                        <div class="row mtop10">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Nama</label>
                                                    <input type="text" name="fullname" id="name" class="form-control cust-control" value="{{ $account->fullname }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop10">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" id="email" class="form-control cust-control" readonly value="{{ $account->email ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop10">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="phone">Nomor Telepon</label>
                                                    <input type="text" name="phone" id="phone" class="form-control cust-control" readonly value="{{ $account->phone ?? '-' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop10">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Username:</label>
                                                    <input name="username" id="username" class="form-control cust-control" readonly value="{{ $account->username }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop20">
                                            <h5>Status Akun</h5>
                                        </div>
                                        <div class="row mtop10">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="accountType">Premium / Free User:</label>
                                                    <input name="account_type" type="text" id="accountType" class="form-control cust-control" readonly value="{{ $account->is_upgraded ? 'Premium' : 'Free User' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mtop10">
                                            <div class="col-md-12">
                                                <label>Berlaku Sampai:</label>
                                                <input name="expire" id="expire" type="text" class="form-control cust-control" readonly value="{{ !is_null($account->upgrade_expiry) ? \Carbon\Carbon::parse($account->upgrade_expiry)->format('d M Y H:i:s') : '-' }}">
                                            </div>
                                        </div>
                                        <div class="row mtop10">
                                            <div class="col-md-12">
                                                <label>Referral / Sales</label>
                                                <input name="referral" id="refererral" type="text" class="form-control cust-control" readonly value="{{ $account->referal_source }}">
                                            </div>
                                        </div>
                                        <div class="row mtop30">
                                            <h5>Ganti Password</h5>
                                        </div>
                                        <div class="form-group mtop10">
                                            <label for="oldPassword">Masukkan Password Lama</label>
                                            <div class="input-group">
                                                <input name="old_password" type="password" id="oldPassword" class="form-control cust-control" placeholder="Masukkan password lama anda">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="height: 35px;">
                                                        <i class="fa fa-eye toggle-password" data-target="#oldPassword" style="cursor: pointer;"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <small id="oldPasswordError" class="text-danger"></small>
                                        </div>
                                        <div class="form-group mtop10">
                                            <label for="newPassword">Password Baru</label>
                                            <div class="input-group">
                                                <input name="password" type="password" id="newPassword" class="form-control cust-control" placeholder="Masukkan password baru">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="height: 35px;">
                                                        <i class="fa fa-eye toggle-password" data-target="#newPassword" style="cursor: pointer;"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <small id="newPasswordError" class="text-danger"></small>
                                        </div>
                                        <div class="form-group mtop10">
                                            <label for="newPasswordConfirmation">Konfirmasi Password Baru</label>
                                            <div class="input-group">
                                                <input name="password_confirmation" type="password" id="newPasswordConfirmation" class="form-control cust-control" placeholder="Konfirmasi password baru">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="height: 35px;">
                                                        <i class="fa fa-eye toggle-password" data-target="#newPasswordConfirmation" style="cursor: pointer;"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <small id="newPasswordConfirmationError" class="text-danger"></small>
                                        </div>
                                        <div class="row mtop30">
                                            <h5>Ganti PIN</h5>
                                        </div>
                                        <div class="form-group mtop10">
                                            <label for="pin">PIN</label>
                                            <div class="input-group">
                                                <input name="pin" value="{{ $account->pin }}" type="password" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" id="pin" class="form-control cust-control" placeholder="Masukkan PIN jika ingin mengubah PIN. 6 Digit angka" maxlength="6">
                                            </div>
                                            <small id="pinError" class="text-danger"></small>
                                        </div>
                                        <div class="form-group mtop10">
                                            <label for="image">Upload Foto Profil</label>
                                            <input type="file" class="form-control" id="image" name="profile_picture" accept="image/*">
                                        </div>

                                        <div class="mtop30">
                                            <h5>Setting Absensi</h5>
                                        </div>
                                        <div class="form-group mtop20">
                                            <label>Jam Masuk:</label>
                                            <input name="clock_in" type="time" id="clock_in" value="{{ $account->clock_in ?? null }}"
                                                class="form-control cust-control">
                                        </div>
                                        <div class="form-group mtop20">
                                            <label>Jam Pulang:</label>
                                            <input name="clock_out" type="time" id="clock_out" value="{{ $account->clock_out ?? null }}"
                                                class="form-control cust-control">
                                        </div>
                                        <div class="form-group mtop20">
                                            <div class="accordion-item mb-3">
                                                <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                                    <button class="accordion-button collapsed btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                                        Pengaturan hari Libur
                                                    </button>
                                                </h2>
                                                <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingFour">
                                                    <div class="accordion-body">
                                                        <div class="mb-3 row">
                                                            <label class="col-sm-2 col-form-label">
                                                                Aktifkan Hari Libur
                                                            </label>
                                                            <div class="col-sm-10">
                                                            <div id="shippingOption">
                                                              <label class="col-sm-2 col-form-label">
                                                                  Hari Libur
                                                              </label>
                                                              <div class="col-sm-10">
                                                                  <div class="row">
                                                                      <div class="col-sm-6">
                                                                          <div class="form-check shipping" data-method="Senin">
                                                                            <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Senin" value="Senin" {{ (isset($account->holiday) && is_array(json_decode($account->holiday, true)) && in_array('Senin', json_decode($account->holiday)) ? 'checked' : '') }}>
                                                                            <label class="form-check-label" for="Senin">Senin</label>
                                                                          </div>
                                                                      </div>
                                                                      <div class="col-sm-6">
                                                                        <div class="form-check shipping" data-method="Selasa">
                                                                          <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Selasa" value="Selasa" {{ (isset($account->holiday) && is_array(json_decode($account->holiday, true)) && in_array('Selasa', json_decode($account->holiday)) ? 'checked' : '') }}>
                                                                          <label class="form-check-label" for="Selasa">Selasa</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-check shipping" data-method="Rabu">
                                                                          <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Rabu" value="Rabu" {{ (isset($account->holiday) && is_array(json_decode($account->holiday, true)) && in_array('Rabu', json_decode($account->holiday)) ? 'checked' : '') }}>
                                                                          <label class="form-check-label" for="Rabu">Rabu</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-check shipping" data-method="Kamis">
                                                                          <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Kamis" value="Kamis" {{ (isset($account->holiday) && is_array(json_decode($account->holiday, true)) && in_array('Kamis', json_decode($account->holiday)) ? 'checked' : '') }}>
                                                                          <label class="form-check-label" for="Kamis">Kamis</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-check shipping" data-method="Jumat">
                                                                          <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Jumat" value="Jumat" {{ (isset($account->holiday) && is_array(json_decode($account->holiday, true)) && in_array('Jumat', json_decode($account->holiday)) ? 'checked' : '') }}>
                                                                          <label class="form-check-label" for="Jumat">Jumat</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-check shipping" data-method="Sabtu">
                                                                          <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Sabtu" value="Sabtu" {{ (isset($account->holiday) && is_array(json_decode($account->holiday, true)) && in_array('Sabtu', json_decode($account->holiday)) ? 'checked' : '') }}>
                                                                          <label class="form-check-label" for="Sabtu">Sabtu</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-check shipping" data-method="Minggu">
                                                                          <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Minggu" value="Minggu" {{ (isset($account->holiday) && is_array(json_decode($account->holiday, true)) && in_array('Minggu', json_decode($account->holiday)) ? 'checked' : '') }}>
                                                                          <label class="form-check-label" for="Minggu">Minggu</label>
                                                                        </div>
                                                                    </div>
                                                                  </div>
                                                              </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>

<div class="row mtop20">
    <div class="col-md-12 d-flex justify-content-start">
        <a href="{{ url('/setting') }}" class="btn btn-secondary me-3">Kembali</a>
        <button class="btn btn-primary" type="submit" id="submitBtn" {{ $account->pin ? '' : 'disabled' }}>Simpan</button>
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
        const submitBtn = $('#submitBtn');
        const pinInput = $('#pin');
        const oldPasswordInput = $('#oldPassword');
        const newPasswordInput = $('#newPassword');
        const newPasswordConfirmationInput = $('#newPasswordConfirmation');

        function validatePin() {
            if (pinInput.val().length > 0 && pinInput.val().length < 6) {
                $('#pinError').text('PIN harus 6 digit.');
                submitBtn.prop('disabled',true);
            } else {
                $('#pinError').text('');
                submitBtn.prop('disabled',false);
            }
        }

        function validateOldPassword() {
            if (oldPasswordInput.val().length == 0) {
                $('#oldPasswordError').text('Password lama harus diisi.');
                return false;
            } else {
                $('#oldPasswordError').text('');
                return true;
            }
        }

        function validateNewPassword() {
            if (newPasswordInput.val().length > 0 && newPasswordInput.val().length < 8) {
                $('#newPasswordError').text('Password baru harus minimal 8 karakter.');
                return false;
            } else {
                $('#newPasswordError').text('');
                return true;
            }
        }

        function validatePasswordConfirmation() {
            if (newPasswordConfirmationInput.val() != newPasswordInput.val()) {
                $('#newPasswordConfirmationError').text('Konfirmasi password tidak cocok.');
                return false
            } 
            else {
                $('#newPasswordConfirmationError').text('');
                return true
            }
        }

        function validatePassword()
        {
            const isNewPasswordValid = validateNewPassword();
            const isPasswordConfirmationValid = validatePasswordConfirmation();

            const isValid = isNewPasswordValid && isPasswordConfirmationValid;

            submitBtn.prop('disabled', !isValid);
        }

        pinInput.on('input', validatePin);
        oldPasswordInput.on('input', validateOldPassword);
        newPasswordInput.on('input', validatePassword);
        newPasswordConfirmationInput.on('input', validatePassword);

        $('.toggle-password').on('click', function() {
            var input = $($(this).data('target'));
            var type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        function show_validation_errors(errors) {
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");

            $.each(errors, function(field, messages) {
                var input = $('[name="' + field + '"]');
                input.addClass("is-invalid");
                var inputGroupAppend = input.closest('.input-group').find('.input-group-append');
                $.each(messages, function(index, message) {
                    inputGroupAppend.after('<div class="invalid-feedback">' + message + '</div>');
                });
            });
        }
        $("#formUpdateAccount").submit(function(e) {
                e.preventDefault();
                var form = document.getElementById('formUpdateAccount');
                var formData = new FormData();
                var oldPassword = $('#oldPassword').val();
                var password = $('#newPassword').val();
                var passwordConfirm = $('#newPasswordConfirmation').val();
                let photo = $('#image')[0].files[0];
                let pin = $('#pin').val();
                console.log(photo);
                formData.append('old_password', oldPassword);
                formData.append('password', password);
                formData.append('password_confirmation', passwordConfirm);
                formData.append('photo', photo);
                formData.append('pin', pin);

                $.ajax({
                    url: "{{ route('account.profile.update.api',['id' => $account->id]) }}",
                    type: "PATCH",
                    contentType: 'multipart/form-data',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        Swal.fire('Success',data.message,'success').then((val) => window.location.href='/setting');
                    },
                    error: function(err) {
                        if(err.responseJSON.errors)
                        {
                            var errors = err.responseJSON.errors;
                            show_validation_errors(errors);
                        }
                        else {
                            Swal.fire('Error',err.responseJSON.message,'error');
                        }
                        
                    }
                })
        });

        $('#image').change(function () {
            if ($(this).val()) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        });
    }); 
    
</script>

<script>
    function validateHoliday() {
        // const checkboxes = document.querySelectorAll('input[name="holiday[]"]:checked');
        // const checkedCount = checkboxes.length;

        // if (checkedCount == 0) {
        //     $('#submitBtn').prop('disabled',true);
        // } else {
        //     $('#submitBtn').prop('disabled',false);
        // }
    }
</script>
@endsection
