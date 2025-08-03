@php
    $setting = \App\Models\Setting::findorFail(1);
    $company = DB::table('business_groups')->where('user_id', session('id'));
    if ($company->count() > 0) {
        $cq = $company->first();
        $cname = $cq->branch_name;
    } else {
        $cname = 'Reskin Apps';
    }

@endphp


<!DOCTYPE html>
<html lang="en" data-layout="single">

<head>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('topstyle')
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
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

    <!-- Theme Script js -->
    {{-- <script src="{{ asset('reskin') }}/assets/js/theme-script.js"></script> --}}

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/bootstrap.min.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/tabler-icons/tabler-icons.min.css">
     <!-- Feather CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/icons/feather/feather.css">


    <!-- Daterangepikcer CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/daterangepicker/daterangepicker.css">

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/bootstrap-datetimepicker.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/fontawesome/css/all.min.css">


    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/select2/css/select2.min.css">

    <!-- Tabler Icon CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/tabler-icons/tabler-icons.min.css">
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/dataTables.bootstrap5.min.css">

    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/plugins/simplebar/simplebar.min.css">

    <!-- Iconsax CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/iconsax.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('reskin') }}/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">



    @include('main.css')

    @yield('style')
    
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css"
        integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
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

        .fs-6 {
            font-size: 14px !important;
        }
    </style>

</head>

<body>

    <!-- Begin Wrapper -->
    <div class="main-wrapper">

        @include('component_new.header')

        @include('component_new.sidebar')


        @yield('content')

    </div>






    <!-- jQuery -->
    <script src="{{ asset('reskin') }}/assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('reskin') }}/assets/js/bootstrap.bundle.min.js"></script>

    <!-- Daterangepikcer JS -->
    <script src="{{ asset('reskin') }}/assets/js/moment.min.js"></script>
    <script src="{{ asset('reskin') }}/assets/plugins/daterangepicker/daterangepicker.js"></script>

    <!-- Simplebar JS -->
    <script src="{{ asset('reskin') }}/assets/plugins/simplebar/simplebar.min.js"></script>

    <!-- Datetimepicker JS -->
    <script src="{{ asset('reskin') }}/assets/js/bootstrap-datetimepicker.min.js"></script>

    <!-- Chart JS -->
    <script src="{{ asset('reskin') }}/assets/plugins/apexchart/apexcharts.min.js"></script>
    <script src="{{ asset('reskin') }}/assets/plugins/apexchart/chart-data.js"></script>

    <!-- Datatable JS -->
    <script src="{{ asset('reskin') }}/assets/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('reskin') }}/assets/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('reskin') }}/assets/plugins/select2/js/select2.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('reskin') }}/assets/js/script.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>


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
