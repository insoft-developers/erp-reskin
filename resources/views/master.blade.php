@php
    $setting = \App\Models\Setting::findorFail(1);
    $company = DB::table('business_groups')->where('user_id', session('id'));
    if ($company->count() > 0) {
        $cq = $company->first();
        $cname = $cq->branch_name;
    } else {
        $cname = 'Randu Apps';
    }

@endphp

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="theme_ocean" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('topstyle')
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>{{ $setting->site_name }} - {{ $setting->site_slogan }}</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('template/main') }}/images/logo.png" />
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/css/bootstrap.min.css" />
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('vendor/intercom') }}/intercom16.css" /> --}}
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/vendors/css/daterangepicker.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/css/theme.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
        integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
    <script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @include('main.css')
    @yield('style')
    <style>
        .material-icons {
            margin-top: 5px
        }

        body {
            zoom: 90%;
        }

        .nxl-navigation .navbar-content {
            height: 100vh !important;
        }

        .modal-backdrop {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>

    <!--! ================================================================ !-->
    <!--! [Start] Navigation Manu !-->
    <!--! ================================================================ !-->
    @if (session('role') != 'staff')
        @include('components.sidebar')
    @else
        @include('components.sidebar-staff')
    @endif
    <!--! ================================================================ !-->
    <!--! [End]  Navigation Manu !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Header !-->
    <!--! ================================================================ !-->
    @include('components.header')
    <!--! ================================================================ !-->
    <!--! [End] Header !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->

    @yield('content')

    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Theme Customizer !-->
    <!--! ================================================================ !-->

    <!--! BEGIN: Vendors JS !-->

    {{-- MODALS POP UP --}}
    <img id="l-image" style="display: none;" src="{{ asset('template/main/images/loading.gif') }}">
    @if (user()->popup_show == 1)
        <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-popup-notification">
            <div class="modal-dialog modal-dialog-centered" id="content-modal-popup-notification">

            </div>
        </div>
    @endif

    <script src="{{ asset('template/main') }}/vendors/js/vendors.min.js"></script>

    <!--! <script id="sbinit" src="https://randu.balas.chat/js/main.js"></script> !-->
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
    <img src="https://randu.co.id/wp-content/uploads/2025/03/wa_circle_randu.svg" alt="Chat via WhatsApp" style="
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

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('template/main') }}/vendors/js/daterangepicker.min.js"></script>
    {{-- <script src="{{ asset('template/main') }}/vendors/js/apexcharts.min.js"></script> --}}
    <script src="{{ asset('template/main') }}/vendors/js/circle-progress.min.js"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('template/main') }}/js/common-init.min.js"></script>
    {{-- <script src="{{ asset('template/main') }}/js/dashboard-init.min.js"></script> --}}
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
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
    <script src="https://unpkg.com/vue@3.2.47/dist/vue.global.js"></script>

    @include('main.js')
    @include('modal')
    @include('components.intercom')
    @yield('js')

    <script>
        function markAllAsRead() {
            $.ajax({
                type: "GET",
                url: "{{ route('notification.markAllAsRead') }}",
                success: function(response) {
                    location.reload();
                }
            });
        }

        function alertPremiumMenu() {
            Swal.fire('Warning',
                    'Maaf hanya user premium yang bisa membuka Cabang dan menambahkan staff dalam aplikasi. Silahkan upgrade terlebih dahulu.',
                    'warning')
                .then((result) => {
                    if (result.value) {
                        window.location.href = '/premium';
                    }
                })
        }


        @if (isset($dataUser))
            const balance = {{ $dataUser->balance }}
        @else
            const balance = 0
        @endif
        $('#balance').text(`Rp ${new Intl.NumberFormat().format(balance)}`)
    </script>

    {{-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        Pusher.logToConsole = false;

        var pushermain = new Pusher('qwerty', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            wsHost: '{{ env('PUSHER_FE_HOST') }}',
            wsPort: {{ env('PUSHER_PORT') }},
            wssPort: 443,
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
            encrypted: true
        });

        var channel = pushermain.subscribe('pc-order-channel.{{ session('id') }}');
        channel.bind('e1', function(data) {
            var audio = document.getElementById('notification-sound');
            audio.play();

            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "300000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "toastClass": "toast toast-custom"
            };

            toastr.success(data.data.message);
        });

        // Logging tambahan untuk debugging
        pushermain.connection.bind('connected', function() {
            console.log('Successfully connected to Pusher');
        });

        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Subscribed to my-channel');
        });
    </script>

    <audio id="notification-sound" src="{{ asset('template/main/mp3/ka-ching.mp3') }}"></audio>
    <script>
        function add_product_module() {
            $.ajax({
                url: "{{ url('open_product_add') }}",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    if (data) {
                        Swal.fire('Warning',
                                'Silahkan membuat kategori produk terlebih dahulu.',
                                'warning')
                            .then((result) => {
                                if (result.value) {
                                    window.location.href = '/product_category';
                                }
                            })
                    } else {
                        window.location = "{{ url('product/create') }}";
                    }
                }
            });
        }
    </script>
</body>

</html>
