@extends('layouts.auth')

@section('content')
    <div class="login-section page-side section-ope">
        <div class="section-page_intro">
            <img src="{{ asset('auth/Image/signin-icon-black.png') }}" alt="signin-icon">
            <p class="section-page-intro_title">Login</p>
        </div>

        <div class="login-form-area">
            <p class="form-title">Login</p>
            <div class="section-form">


                <form method="POST" action="{{ route('login.action') }}" id="formSignIn" class="login-form">
                    {{-- ALERT --}}
                    <div class="row">
                        <div class="col-sm-12 alert">

                        </div>
                    </div>

                    @csrf
                    <label class="login-page_label">
                        <input class="login-page_input" type="email" name="email" autocomplete="off"
                            value="{{ old('email') }}" required>
                        <span class="login-page_placeholder">Email</span>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </label>
                    <label class="login-page_label">
                        <input class="login-page_input" type="password" name="password" autocomplete="off">
                        <span class="login-page_placeholder">Password</span>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </label>
                    <div class="login-section_submit">
                        <div class="login-page-submit-btn">
                            <input type="submit" name="submit-login" value="submit">
                        </div>
                    </div>
                    <div class="login-page_forget">
                        <a href="">Lupa Password?</a>
                    </div>
                    <div class="login-page_aktivasi"
                        style="color: #2b2e2f; font-size: 14px; display: block; margin: 20px 0 20px; font-weight: 600;">
                        <a href="https://randu.co.id/chat/bantuan-aktivasi-akun" target="_blank">Perlu Bantuan
                            Login/Aktivasi?</a>
                    </div>
                </form>

                <form method="POST" id="formForget" action="{{ route('forgot_password.send_token') }}" class="forget-form">
                    @csrf
                    <p class="forget-title">Lupa Password</p>

                    {{-- ALERT --}}
                    <div class="row">
                        <div class="col-sm-12 alert">

                        </div>
                    </div>

                    <label class="login-page_label">
                        <input class="login-page_input" type="email" name="email" autocomplete="off">
                        <span class="login-page_placeholder">Email</span>

                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                    </label>
                    <div class="login-section_submit">
                        <div class="login-page-submit-btn"><input type="submit" name="submit-login" value="submit"></div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!--       Sign up Side      -->

    <div class="signup-section page-side section-clos">
        <div class="section-page_intro">
            <img src="{{ asset('auth/Image/signup-icon.png') }}" alt="signup-icon">
            <p class="section-page-intro_title">Daftar</p>
        </div>

        <div class="signup-form-area">
            <p class="form-title">Daftar</p>
            <div class="section-form">
                <form method="POST" action="{{ url('signup') }}" id="formSignup" class="signup-form">
                    @csrf

                    {{-- ALERT --}}
                    <div class="row">
                        <div class="col-sm-12 alert">

                        </div>
                    </div>

                    <div id="step1">
                        <!-- Label dan input untuk fullname -->
                        <label class="login-page_label">
                            <input class="login-page_input" type="text" id="fullname" name="fullname" autocomplete="off"
                                oninput="formatFullName()">
                            <span class="login-page_placeholder">Nama Lengkap</span>
                        </label>

                        <!-- Label dan input untuk username yang dihasilkan -->
                        <label class="login-page_label">
                            <input class="login-page_input" type="text" name="username" id="username" readonly
                                autocomplete="off">
                            <span class="login-page_placeholder"></span>
                        </label>

                        <!-- Skrip untuk memformat fullname dan menghasilkan username -->
                        <script>
                            // Fungsi untuk memformat setiap kata di fullname agar dimulai dengan huruf besar
                            function formatFullName() {
                                var input = document.getElementById('fullname');
                                var words = input.value.split(' ');

                                for (var i = 0; i < words.length; i++) {
                                    words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1).toLowerCase();
                                }

                                input.value = words.join(' ');

                                // Memanggil generateUsername setelah memformat fullname
                                generateUsername();
                            }

                            // Fungsi untuk menghasilkan username dari fullname
                            function generateUsername() {
                                const fullnameInput = document.getElementById('fullname').value;
                                const usernameInput = document.getElementById('username');

                                const nameParts = fullnameInput.trim().split(" ");
                                const randomNumber = Math.floor(1000 + Math.random() * 9000); // Menghasilkan angka antara 1000 hingga 9999

                                let username = "";

                                if (nameParts.length >= 2) {
                                    const firstName = nameParts[0];
                                    const secondName = nameParts[1];
                                    username = `${firstName}${secondName}${randomNumber}`;
                                } else if (nameParts.length === 1) {
                                    const firstName = nameParts[0];
                                    username = `${firstName}${randomNumber}`;
                                }

                                usernameInput.value = username.toLowerCase(); // Set username menjadi lowercase
                            }
                        </script>


                        <label class="login-page_label">
                            <input class="login-page_input" type="email" name="email" autocomplete="off"
                                id="emailInput">
                            <span class="login-page_placeholder">Email</span>
                        </label>

                        <script>
                            const emailInput = document.getElementById('emailInput');

                            emailInput.addEventListener('input', function() {
                                // Menghapus spasi saat mengetik
                                this.value = this.value.replace(/\s/g, '');
                            });

                            emailInput.addEventListener('blur', function() {
                                // Menghapus spasi saat input kehilangan fokus (blur)
                                this.value = this.value.replace(/\s/g, '');
                            });
                        </script>
                        <label class="login-page_label">
                            <input class="login-page_input" type="number" name="whatsapp" autocomplete="off">
                            <span class="login-page_placeholder">Nomor Whatsapp</span>
                        </label>
                        <label class="login-page_label">
                            <input class="login-page_input" type="password" name="password">
                            <span class="login-page_placeholder">Password</span>
                        </label>
                        <label class="login-page_label">
                            <input class="login-page_input" type="password" name="password_confirmation">
                            <span class="login-page_placeholder">Ketik Ulang Password</span>
                        </label>

                        <div class="signup-section_submit" style="float: right;">
                            <div class="login-page-submit-btn">
                                <input type="button" name="submit-signup" value="next" onclick="step2()">
                            </div>
                        </div>
                    </div>

                    <div id="step2" hidden>
                        <label class="login-page_label">
                            <select name="category" id="category" class="login-page_input select2">
                                <option value="" selected disabled> Kategori Usaha</option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="login-page_label">
                            <input class="login-page_input" type="text" id="business_name" name="business_name">
                            <span class="login-page_placeholder">Nama Bisnis / Usaha</span>
                        </label>

                        {{-- <label class="login-page_label" id="formDistrict">
                        <select name="district" id="district" class="login-page_input select2">
                            <option value="" selected disabled>Kecamatan, Kabupaten, Provinsi</option>
                            @foreach ($district as $d)
                                <option value="{{ $d->distrik }}, {{ $d->kabupaten }}, {{ $d->provinsi }}">{{ $d->distrik }}, {{ $d->kabupaten }}, {{ $d->provinsi }} </option>
                            @endforeach
                        </select>
                    </label> --}}

                        <label class="login-page_label">
                            <input class="login-page_input" type="text" id="full_address" name="full_address">
                            <span class="login-page_placeholder">Alamat Lengkap</span>
                        </label>

                        <label class="login-page_label">
                            <input class="login-page_input" type="number" id="business_phone" name="business_phone">
                            <span class="login-page_placeholder">No Telepon (Opsional)</span>
                        </label>

                        <label class="login-page_label">
                            <input class="login-page_input" type="text" name="referal_source"
                                value="{{ \Request::get('ref') ?? '' }}" readonly>
                            <span
                                class="login-page_placeholder">{{ \Request::get('ref') != '' ? '' : 'RANDU2025' }}</span>
                        </label>

                        {{-- TOS --}}
                        <label class="login-page_label" style="display: flex;">
                            <input class="login-page_input" type="checkbox" name="tos"
                                style="width: 30px; margin-right: 10px;" required>
                            <span
                                style="color: white; font-family: 'Open Sans', sans-serif; font-size: 14px; font-weight: 300;">
                                Dengan mendaftar, saya menyetujui
                                <a href="https://randu.co.id/ketentuan-layanan/" target="_blank"
                                    style="color: white; font-weight: bold; text-decoration: none;">
                                    Syarat dan Ketentuan Pengguna
                                </a> dari PT Randu Bertumbuh Digital.
                            </span>

                        </label>

                        <div class="signup-section_submit" style="float: left;">
                            <div class="login-page-submit-btn">
                                <input type="button" name="submit-signup" value="next" onclick="step1()"
                                    style="transform: rotate(180deg);">
                            </div>
                        </div>

                        <div class="signup-section_submit" style="float: right;">
                            <div class="login-page-submit-btn">
                                <input type="submit" name="submit-signup" value="submit">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @elseif (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    <script>
        $("#category").select2();
        $("#district").select2();

        function step1() {
            document.getElementById('step1').hidden = false;
            document.getElementById('step2').hidden = true;
        }

        function step2() {
            document.getElementById('step1').hidden = true;
            document.getElementById('step2').hidden = false;
        }

        function validateUsername(input) {
            input.value = input.value.replace(/[^a-z0-9]/g, '');
        }

        $(document).on('change', '#category', function() {
            const categoryText = $(this).find(':selected').text();
            if (categoryText == 'Catat Keuangan Pribadi') {
                $('#business_name').attr('disabled', true);
                $('#district').attr('disabled', true)
                $('#full_address').attr('disabled', true)
                $('#business_phone').attr('disabled', true)
            } else {
                $('#business_name').attr('disabled', false);
                $('#district').attr('disabled', false)
                $('#full_address').attr('disabled', false)
                $('#business_phone').attr('disabled', false)
            }
        })

        document.getElementById('formSignIn').addEventListener('submit', function(event) {
            event.preventDefault();

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

                        var elem = `<div class="alert alert-danger">
                            ` + errorMessages + `
                        </div>`

                        $('.alert').html(elem);
                    } else if (!data.status) {
                        var elem = `<div class="alert alert-danger">
                            ` + data.message + `
                        </div>`

                        $('.alert').html(elem);

                    } else {
                        
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Success',
                        //     text: data.message
                        // }).then(() => {
                        window.location.href = data.redirect;
                        // });
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

        document.getElementById('formSignup').addEventListener('submit', function(event) {
            event.preventDefault();

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

                        var elem = `<div class="alert alert-danger">
                            ` + errorMessages + `
                        </div>`

                        $('.alert').html(elem);
                    } else if (!data.status) {
                        var elem = `<div class="alert alert-danger">
                            ` + data.message + `
                        </div>`

                        $('.alert').html(elem);

                    } else {
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Success',
                        //     text: data.message
                        // }).then(() => {
                        window.location.href = data.redirect;
                        // });
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

        document.getElementById('formForget').addEventListener('submit', function(event) {
            event.preventDefault();

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

                        var elem = `<div class="alert alert-danger">
                            ` + errorMessages + `
                        </div>`

                        $('.alert').html(elem);
                    } else if (!data.status) {
                        var elem = `<div class="alert alert-danger">
                            ` + data.message + `
                        </div>`

                        $('.alert').html(elem);
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
    </script>
@endpush
