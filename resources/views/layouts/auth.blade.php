<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login page</title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet'>

    @php
        $setting = \App\Models\Setting::findorFail(1);
    @endphp
    
    <title>{{ $setting->site_name }} - {{ $setting->site_slogan }}</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('template/main') }}/images/logo.png" />

    <link rel="stylesheet" href="{{ asset('auth/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/css/login-page_demo.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/css/login-page_style.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/css/login-page_responsive.css') }}">

    {{-- SWEETALERT --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>



    {{-- SELECT2 --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2-theme.min.css">

    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #c5c5c5 !important;

            top: 9px;
            left: 9px;
            color: #8a8a8a;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            transition: .3s ease;
            cursor: text;
            font-weight: 600;
            font-size: 13px;
        }

        .select2-results__options li{
            color: #8a8a8a;
            overflow: hidden;
            white-space: nowrap;
            transition: .3s ease;
            cursor: text;
            font-size: 13px;
            opacity: 0.8;
        }

        @media only screen and (max-width: 600px) {
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                max-width: 180px;
            }
        }

        .select2-container--default.select2-container--disabled .select2-selection--single{
            background-color: grey !important;
        }

        .select2-container--open .select2-dropdown--below{
            border-radius: 5px;
        }

        .select2-container--default .select2-results>.select2-results__options{
            padding: 0px 20px;
        }

        .select2-search--dropdown{
            padding: 10px !important;
        }
        
        .select2-container--default .select2-search--dropdown .select2-search__field{
            border-radius: 5px;
            padding: 10px;
        }

        .select2-container--default .select2-selection--single {
            border-top: none;
            border-right: none;
            border-left: none;
            border-bottom: 1px solid #fafafa;
            outline: none;
            padding-bottom: 35px;
            width: 100%;
            background-color: transparent;
        }

        .select2-container{
            width: 100% !important;
        }

        .custom-control {
            display: block;
            min-height: 1.8px;
            position: relative;
            padding-left: .75rem;
        }

        .custom-control-input {
            left: 10px;
            opacity: 0;
            z-index: -1;
            width: 1.25rem;
            height: 1.375rem;
            position: absolute;
        }

        .custom-control-label {
            left: 15px;
            cursor: pointer;
            margin-bottom: 0;
            position: relative;
            color: #283c50;
            font-size: 13px;
            vertical-align: top;
            font-weight: 500;
            text-transform: inherit;
        }

        input:disabled{
            background-color: grey !important;
        }

        .alert-danger{
            background-color: #17c666;
            border-color: #f5c6cb;
            border-radius: 5px;
            padding: 25px;
            margin-bottom: 20px;
            font-size: 15px;
            font-family: 'Open Sans', sans-serif;
            color: white;
            font-weight: 300;
        }
    </style>

</head>

<body>

    <div class="login-page_container">
        @yield('content')
    </div>


    <script src="{{ asset('auth/js/jquery-1.12.1.min.js')}}"></script>
    <script src="{{ asset('auth/js/login-page_script.js')}}"></script>

    <script src="{{ asset('template/main') }}/vendors/js/vendors.min.js"></script>
    <!-- <script id="sbinit" src="https://randu.balas.chat/js/main.js"></script> !-->
    <!--<script type="text/javascript">
        window.mychat = window.mychat || {};
        window.mychat.server = 'https://live.cekat.ai/widget.js';
        window.mychat.iframeWidth = '400px';
        window.mychat.iframeHeight = '700px';
        window.mychat.accessKey = 'Randu-2e1VOxzy';
        (function() {
            var mychat = document.createElement('script');
            mychat.type = 'text/javascript';
            mychat.async = true;
            mychat.src = window.mychat.server;
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(mychat, s);
        })();
    </script>!-->
    <!-- Tombol Floating WhatsApp -->
<a href="https://randu.co.id/chat/lewat-whatsapp/" target="_blank" id="whatsapp-float" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    z-index: 99999;
">
    <img src="https://app.balesotomatis.id/assets/images/wa_circle_balesoto.svg" alt="Chat via WhatsApp" style="
        width: 100%;
        height: auto;
        cursor: pointer;
    ">
</a>

<!-- Pop-up Sapaan -->
<div id="popup-message" style="
    position: fixed;
    bottom: 90px; /* Pastikan muncul di atas tombol WhatsApp */
    right: 20px;
    background: white;
    padding: 12px 15px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    max-width: 250px;
    display: none;
    font-family: Arial, sans-serif;
    z-index: 99999; /* Pastikan pop-up selalu di atas */
    font-size: 14px;
    color: #444;
    line-height: 1.4;
    position: fixed;
">
    <strong style="font-size: 15px; color: #222; display: flex; align-items: center;">
        👋 Mau tanya-tanya?
    </strong>
    <p style="margin: 5px 0; color: #666;">Trainer online mulai jam 7 pagi sampai 10 malam 😍👇</p>
    
    <!-- Tombol Close -->
    <span id="close-popup" style="
        position: absolute;
        top: 8px;
        right: 10px;
        cursor: pointer;
        font-size: 14px;
        color: #888;
        transition: color 0.3s ease;
    ">❌</span>

    <!-- Bubble Arrow -->
    <div style="
        content: '';
        position: absolute;
        bottom: -10px;
        right: 20px;
        border-width: 10px;
        border-style: solid;
        border-color: white transparent transparent transparent;
    "></div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Periksa apakah popup sudah pernah ditutup sebelumnya
        if (!localStorage.getItem("popupClosed")) {
            // Tampilkan pop-up setelah 3 detik
            setTimeout(function () {
                document.getElementById('popup-message').style.display = 'block';
            }, 3000);
        }

        // Tutup pop-up ketika tombol "X" diklik
        document.getElementById('close-popup').addEventListener('click', function () {
            document.getElementById('popup-message').style.display = 'none';
            // Simpan status di localStorage agar popup tidak muncul lagi
            localStorage.setItem("popupClosed", "true");
        });
    });
</script>

@yield('pixel')
    
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('template/main') }}/vendors/js/select2.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/select2-active.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/daterangepicker.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/apexcharts.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/circle-progress.min.js"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('template/main') }}/js/common-init.min.js"></script>
    <script src="{{ asset('template/main') }}/js/dashboard-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('template/main') }}/js/theme-customizer-init.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/select2.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/select2-active.min.js"></script>
    <!--! END: Theme Customizer !-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('js')
</body>

</html>
