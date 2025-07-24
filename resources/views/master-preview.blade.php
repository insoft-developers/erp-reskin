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
    </style>
</head>

<body>
    @yield('content')
    <script src="{{ asset('template/main') }}/vendors/js/vendors.min.js"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('template/main') }}/vendors/js/select2.min.js"></script>
    <script src="{{ asset('template/main') }}/vendors/js/select2-active.min.js"></script>
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

    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>

    @include('main.js')
    @include('modal')

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


        @if(isset($dataUser))
        const balance = {{$dataUser->balance}}
        @else
        const balance = 0
        @endif
        $('#balance').text(`Rp ${new Intl.NumberFormat().format(balance)}`)
    </script>

    {{-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        Pusher.logToConsole = true;

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
</body>

</html>
