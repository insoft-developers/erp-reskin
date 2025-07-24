<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @php
        $setting = \App\Models\Setting::findorFail(1);
    @endphp
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

                        <form method="POST" action="{{ url('signup') }}" id="formSignup"
                            class="d-flex justify-content-center align-items-center">
                            @csrf
                            <div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pt-lg-4 pb-0 flex-fill">
                                <div class="mx-auto mb-5 text-center">
                                    <img src="{{ asset('reskin') }}/assets/img/logo.svg" class="img-fluid"
                                        alt="Logo">
                                </div>
                                <div class="card border-0 p-lg-3 shadow-lg rounded-2">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h5 class="mb-2">Daftar</h5>
                                            <p class="mb-0">Silahkan masukkan detail data anda untuk membuat akun</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input oninput="formatFullName()" type="text" value=""
                                                    id="fullname" name="fullname"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Masukkan nama lengkap anda">
                                            </div>
                                        </div>



                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input type="text" value="" id="username" name="username"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="username dibuat otomatis">
                                            </div>
                                        </div>


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
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input type="email" value="" id="emailInput" name="email"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Masukkan alamat email anda">
                                            </div>
                                        </div>
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

                                        <div class="mb-3">
                                            <label class="form-label">Nomor Whatsapp</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input type="number" value="" name="whatsapp" id="whatsapp"
                                                    autocomplete="off" class="form-control border-start-0 ps-0"
                                                    placeholder="Masukkan nomor whatsapp anda">
                                            </div>
                                        </div>


                                        <div class="mb-3">
                                            <label class="form-label">Kategori Usaha</label>
                                            <div class="input-group">

                                                <select id="category" name="category" id="whatsapp" autocomplete="off"
                                                    class="form-control border-start-0 ps-0">
                                                    <option value="" selected disabled>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Piih</option>
                                                    @foreach ($category as $cat)
                                                        <option value="{{ $cat->id }}">
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cat->category_name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nama Bisnis / Usaha</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input type="text" value="" name="business_name"
                                                    id="business_name" autocomplete="off"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Masukkan nama perusahaan/usaha anda">
                                            </div>
                                        </div>



                                        <div class="mb-3">
                                            <label class="form-label">No Telepon Usaha Anda (Opsional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input type="number" value="" name="business_phone"
                                                    id="business_phone" autocomplete="off"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Masukkan nomor telepon perusahaan/usaha anda">
                                            </div>
                                        </div>



                                        <div class="mb-3">
                                            <label class="form-label">Alamat Lengkap</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input type="text" value="" name="full_address"
                                                    id="full_address" autocomplete="off"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="Masukkan alamat lengkap anda">
                                            </div>
                                        </div>


                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="pass-group input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <span class="isax toggle-password isax-eye-slash"></span>
                                                <input type="password" name="password"
                                                    class="pass-input form-control border-start-0 ps-0"
                                                    placeholder="****************">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <div class="pass-group input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <span class="isax toggle-passwords isax-eye-slash"></span>
                                                <input type="password" name="password_confirmation"
                                                    class="pass-input form-control border-start-0 ps-0"
                                                    placeholder="****************">
                                            </div>
                                        </div>


                                        <div class="mb-3">
                                            <label class="form-label">No Referal</label>
                                            <div class="input-group">
                                                <span class="input-group-text border-end-0">
                                                    <i class="isax isax-profile"></i>
                                                </span>
                                                <input type="number" value="{{ \Request::get('ref') ?? '' }}"
                                                    name="referal_source" id="referal_source" autocomplete="off"
                                                    class="form-control border-start-0 ps-0"
                                                    placeholder="{{ \Request::get('ref') != '' ? '' : 'RESKIN2025' }}">
                                            </div>
                                        </div>


                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-md mb-0">
                                                    <input required class="form-check-input" id="tos" name="tos" type="checkbox">
                                                    <label for="tos" class="form-check-label mt-0">Dengan
                                                        mendaftar, saya menyetujui Syarat dan Ketentuan Pengguna dari
                                                        PT. Reskin Indonesia.</label>
                                                    <div class="d-inline-flex"><a href="#"
                                                            class="text-decoration-underline me-1">Syarat &
                                                            Ketentuan</a>
                                                        and <a href="#" class="text-decoration-underline ms-1">
                                                            Kebijakan Privasi</a></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <button id="btn-submit-daftar" type="submit"
                                                class="btn bg-primary-gradient text-white w-100">Daftar</button>
                                        </div>
                                        <div class="login-or">
                                            <span class="span-or">Atau</span>
                                        </div>

                                        <div class="text-center">
                                            <h6 class="fw-normal fs-14 text-dark mb-0">Sudah punya akun ?
                                                <a href="{{ url('frontend_login') }}" class="hover-a"> Log In</a>
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
            $("#btn-submit-daftar").text("Processing....");
            $("#btn-submit-daftar").attr("disabled", true);
        }

        function unloading() {  
            $("#btn-submit-daftar").text("Daftar");
            $("#btn-submit-daftar").removeAttr("disabled");
        }


        function validateUsername(input) {
            input.value = input.value.replace(/[^a-z0-9]/g, '');
        }

        $(document).on('change', '#category', function() {
            const categoryText = $(this).find(':selected').text().trim();
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



        document.getElementById('formSignup').addEventListener('submit', function(event) {
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
                    unloading();
                    console.log(data);
                    if (!data.status) {
                        
                         Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: data.message
                        })

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
               
        });
    </script>


</body>

</html>
