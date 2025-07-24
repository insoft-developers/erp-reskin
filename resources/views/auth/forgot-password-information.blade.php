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
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/vendors.min.css">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/css/theme.min.css">
    <!--! END: Custom CSS-->
    <style>
       
        .background-image {
            background: 
                linear-gradient(
                rgba(0, 0, 0, 0.7), 
                rgba(0, 0, 0, 0.7)
                ),
                url("{{ asset('template/main/images/bg_login.jpg') }}");
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;
            height: 100vh;
            width: 100%;
        }
        .img-login{
            width: 150px;
            height: 150px;
            object-fit: contain;
            margin-top: 53px;
            margin-bottom: -41px;
        }
        .card-login{
            opacity: 0.9;
            margin-left: 30px !important;
            margin-right: 30px !important;
        }
        .help-block{
            color: red;
            margin-top: 10px;
        }
    </style>


</head>

<body class="background-image">
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <center><img class="img-login" src="{{ asset('template/main/images/login_depan.png') }}"></center>
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative card-login">
                    <div class="card-body p-sm-5 text-dark">
                        <p>Link konfirmasi sudah kami kirim ke Email/No WA anda, silahkan klik link tersebut untuk mengganti kata sandi</p>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top: 100px;"></div>
    </main>
    
   
    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('template/main') }}/vendors/js/vendors.min.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('template/main') }}/js/common-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('template/main') }}/js/theme-customizer-init.min.js"></script>
    <!--! END: Theme Customizer !-->
</body>

</html>