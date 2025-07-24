<!DOCTYPE html>
<html lang="en">

<head>
    @php
        $setting = \App\Models\Setting::findorFail(1);
    @endphp
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $setting->site_name }} - {{ $setting->site_slogan }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Kanakku is a Sales, Invoices & Accounts Admin template for Accountant or Companies/Offices with various features for all your needs. Try Demo and Buy Now.">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreams Technologies">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('reskin') }}/assets/img/favicon.png">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('reskin') }}/assets/img/apple-touch-icon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/bootstrap.min.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/tabler-icons/tabler-icons.min.css">

    <!-- Iconsax CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/iconsax.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/style.css">

    {{-- SWEETALERT --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>

</head>

<body class="bg-white">

    <!-- Begin Wrapper -->
    <div class="main-wrapper auth-bg">

        <!-- Start Content -->
        <div class="container-fuild">
            <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

                <!-- start row -->
                <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
                    <div class="col-lg-4 mx-auto">
                        <form method="POST" action="{{ route('login.action') }}" id="formSignIn"
                            class="d-flex justify-content-center align-items-center">
                            @csrf
                            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
                                <div class=" mx-auto mb-5 text-center">
                                    <img src="{{ asset('reskin') }}/assets/img/logo.svg" class="img-fluid"
                                        alt="Logo">
                                </div>
                                <div class="card border-0 p-lg-3 shadow-lg">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h5 class="mb-2">Log In</h5>
                                            <p class="mb-0">Silahkan masukkan username dan password anda untuk mulai menggunakan aplikasi</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-sms-notification"></i>
                                                </span>
                                                <input type="email" value=""
                                                    class="form-control border-start-0 ps-0" id="email"
                                                    name="email" required placeholder="masukkan email"
                                                    autocomplete="off">
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="pass-group input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-lock"></i>
                                                </span>
                                                <span class="isax toggle-password isax-eye-slash"></span>
                                                <input id="password" name="password" type="password"
                                                    class="pass-inputs form-control border-start-0 ps-0"
                                                    placeholder="****************" autocomplete="off" required>
                                            </div>
                                            
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-md mb-0">
                                                    <input class="form-check-input" id="remember_me" type="checkbox">
                                                    <label for="remember_me" class="form-check-label mt-0">Ingat saya</label>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <a href="{{ url('forget-password') }}">Lupa Password</a>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <button id="btn-login-submit" type="submit" class="btn bg-primary-gradient text-white w-100">Sign
                                                In</button>
                                        </div>
                                        <div class="login-or">
                                            <span class="span-or">Atau</span>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h6 class="fw-normal fs-14 text-dark mb-0">Belum punya akun.?
                                            <a href="{{ url('frontend_register') }}" class="hover-a"> Daftar</a>
                                        </h6>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                    </div>
                    </form>
                </div><!-- end col -->
            </div>
            <!-- end row -->

        </div>
    </div>
    <!-- End Content -->

    </div>
    <!-- End Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('reskin') }}/assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('reskin') }}/assets/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('reskin') }}/assets/js/script.js"></script>





    <script>

        function loading() {
            $("#btn-login-submit").text("Processing....");
            $("#btn-login-submit").attr("disabled", true);
        }

        function unloading() {
            $("#btn-login-submit").text("Sign In");
            $("#btn-login-submit").removeAttr("disabled");
        }

        document.getElementById('formSignIn').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const url = form.action;
            loading();
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
                    unloading();
                    if (!data.status) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: data.message
                        });

                    } else {
                        window.location.href = data.redirect;
                        
                    }
                })
                
        });
    </script>

</body>

</html>
