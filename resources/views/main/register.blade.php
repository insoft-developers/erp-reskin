@php
$setting = \App\Models\Setting::findorFail(1);

@endphp


<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>{{ $setting->site_name }} - {{ $setting->site_slogan }}</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('template/main') }}/images/logo.png" />
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/css/bootstrap.min.css">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/vendors.min.css">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/css/theme.min.css">
    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
			<script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

    <style>
        .select2-selection__rendered{
            margin-top: -5px !important;
            font-size: 14px;
            
        }
        @media only screen and (min-width: 769px) {
           
            .background-image {
                background: 
                    linear-gradient(
                    rgba(0, 0, 0, 0.7), 
                    rgba(0, 0, 0, 0.7)
                    ),
                    url("{{ asset('template/main/images/bg.jpg') }}");
                background-repeat: no-repeat;
                background-position: center center;
                background-attachment: fixed;
                background-size: cover;
                height: 100vh;
                width: 100%;
            }
            .register-box{
                width: 350px !important;
                margin-top: 40px;
                margin-bottom: 40px;
                border-radius: 5px;
            }
            .tampil-kiri{
                width: 500px;
                color: white;
                position: relative;
                top: 120px !important;
                left: 130px;
                text-align: justify;
            }
            .satu{
                font-size: 18px;
                font-weight: bold;
            }
            .dua{

            }
            .tiga{

            }
            .logo-register{
                position: absolute;
                width: 50px;
                height: 50px;
                left: -57px;
                top: -9px;
            }
            .footer-register{
                color: white;
                position: relative;
                bottom: 53px;
                left: 106px;
            }
            .help-block{
                color: red;
                margin-top: 10px;
            }
            #btn-submit-regiter{
                cursor: pointer !important;
            }
        }
        @media only screen and (max-width: 768px) {
            body {
                background: 
                    linear-gradient(
                    rgba(0, 0, 0, 0.7), 
                    rgba(0, 0, 0, 0.7)
                    ),
                    url("{{ asset('template/main/images/bg.jpg') }}");
                background-size: contain;
            
            }
            .help-block{
                color: red;
                margin-top: 10px;
            }
            #btn-submit-regiter{
                cursor: pointer !important;
            }
           
        }
    </style>
</head>

<body class="background-image">
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <div class="layer-belakang"></div>
    <main class="auth-cover-wrapper">
        <div class="auth-cover-content-inner">
            <div class="auth-cover-content-wrapper">
                <div class="tampil-kiri">
                    <img class="logo-register" src="{{ asset('template/main/images/logo.png') }}">
                    <p class="satu">Aplikasi POS (Point Of Sales) Dan Akuntansi GRATIS Untuk UMKM Indonesia</p>
                    <p class="dua">Akuntansi UKM adalah sistem aplikasi keuangan sederhana yang dapat digunakan oleh Usaha Kecil dan Menengah, serta untuk Pengelolaan Keuangan Sehari-hari. Akuntansi UKM digunakan untuk memenuhi kebutuhan standar pengelolaan sistem informasi keuangan dalam perusahaan.</p>
                    <p class="tiga">Selain itu Akuntansi UKM juga dilengkapi dengan sistem manajemen POS atau Point of Sales yang bisa digunakan di berbagai bidang usaha dari retail sampai restoran dan jasa. Sistem Point of Sales akan mempermudah transaksi penjualan yang terjadi dan memantau stok barang yang masuk dan keluar.</p>
                   
                </div>
            </div>
        </div>
        <div class="auth-cover-sidebar-inner register-box">
            <div class="auth-cover-card-wrapper">
                <div class="auth-cover-card p-sm-4">
                    
                    <h2 class="fs-20 fw-bolder mb-3">Daftar</h2>
                   
                    
                    <form method="POST" action="{{ url('signup') }}" class="w-100 mt-4 pt-2">
                        @csrf
                        <div class="mb-3">
                            {{ Request::segment(2) }}
                            <input type="text" value="{{ old('fullname') }}" class="form-control" id="fullname" name="fullname" placeholder="Nama Sesuai KTP">
                            @if($errors->has('fullname'))
                                <span class="help-block">{{ $errors->first('fullname') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="text" value="{{ old('username') }}" class="form-control" id="username" name="username" placeholder="Username">
                            @if($errors->has('username'))
                                <span class="help-block">{{ $errors->first('username') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="text" value="{{ old('email') }}" class="form-control" id="email" name="email" placeholder="Alamat Email">
                            @if($errors->has('email'))
                                <span class="help-block">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="text" value="{{ old('whatsapp') }}" class="form-control" id="whatsapp" name="whatsapp" placeholder="Nomor Whatsapp">
                            @if($errors->has('whatsapp'))
                                <span class="help-block">{{ $errors->first('whatsapp') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Kata Sandi">
                            @if($errors->has('password'))
                                <span class="help-block">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Konfirmasi Kata Sandi">
                            @if($errors->has('password_confirmation'))
                                <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                            @endif
                        </div>
                        <hr />
                        <div class="mb-3">
                            <select class="form-control" id="category" name="category">
                                <option value="">Kategori Usaha</option>
                                @foreach($category as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('category'))
                                <span class="help-block">{{ $errors->first('category') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="text" value="{{ old('business_name') }}" class="form-control" id="business_name" name="business_name" placeholder="Nama Bisnis">
                            @if($errors->has('business_name'))
                                <span class="help-block">{{ $errors->first('business_name') }}</span>
                            @endif
                        </div>
                       
                        <div class="mb-3">
                            <select class="form-control" id="district" name="district">
                                <option value="">Kecamatan, Kabupaten, Provinsi</option>
                                @foreach($district as $d)
                                <option value="{{ $d->provinsi }}, {{ $d->kabupaten }}, {{ $d->distrik }}">{{ $d->provinsi }}, {{ $d->kabupaten }}, {{ $d->distrik }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('district'))
                                <span class="help-block">{{ $errors->first('district') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" value="{{ old('full_address') }}" id="full_address" name="full_address" placeholder="Alamat Lengkap"></textarea>
                            @if($errors->has('full_address'))
                                <span class="help-block">{{ $errors->first('full_address') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" value="{{ old('business_phone') }}" id="business_phone" name="business_phone" placeholder="No Telepon Bisnis">
                            @if($errors->has('business_phone'))
                                <span class="help-block">{{ $errors->first('business_phone') }}</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" value="{{ $referal }}" id="referal_source" name="referal_source" placeholder="Referal/Sales">
                            
                        </div>

                        <div class="mt-4">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="tos" name="tos">
                                <label class="custom-control-label c-pointer text-muted" for="tos" style="font-weight: 400 !important">Data yang saya masukkan sudah benar</label>
                            </div>
                            
                        </div>
                        <div class="mt-4">
                            <button id="btn-submit-regiter" disabled="disabled" type="submit" class="btn btn-lg btn-primary w-100">Daftar</button>
                        </div>
                    </form>
                    <div class="mt-5 text-muted">
                        <span>Sudah mempunyai akun..?</span>
                        <a href="{{ url('frontend_login') }}" class="fw-bold">Masuk</a>
                    </div>
                </div>
            </div>
        </div>
       
    </main>
    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Theme Customizer !-->
    <!--! ================================================================ !-->
   
    <p class="footer-register">Aplikasi POS & Akuntansi UKM © 2023</p>
    <!--! ================================================================ !-->
    <!--! [End] Theme Customizer !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->

    

    <script src="{{ asset('template/main') }}/vendors/js/vendors.min.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('template/main') }}/vendors/js/lslstrength.min.js"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('template/main') }}/js/common-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('template/main') }}/js/theme-customizer-init.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/select2.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/select2-active.min.js"></script>
    <script>
        $("#tos").click(function(){
            if($('#tos').prop('checked')) {
                $("#btn-submit-regiter").removeAttr("disabled");
            } else {
                $("#btn-submit-regiter").attr("disabled", true);
            }
        })
    </script>
    <!--! END: Theme Customizer !-->

    <script>
        $("#category").select2();
        $("#district").select2();
    </script>
</body>

</html>