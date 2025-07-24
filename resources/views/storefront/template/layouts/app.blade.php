@php
    $setting = \App\Models\Setting::findorFail(1);
    $storeSetting = \App\Models\Account::where('username', $username)->select('storefronts.template', 'storefronts.theme_color')->join('storefronts', 'ml_accounts.id', '=', 'storefronts.user_id')->first();

    $company = DB::table('ml_company')->where('userid', session('id'));
    if ($company->count() > 0) {
        $cq = $company->first();
        $cname = $cq->company_name;
    } else {
        $cname = 'Randu Apps';
    }

@endphp
<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Title -->
	<title>{{ @$title}} {{ $setting->site_name }} - {{ $setting->site_slogan }}</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, minimal-ui, viewport-fit=cover">
	<meta name="theme-color" content="#009688">
	<meta name="author" content="Bertumbuh Labs">
	<meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="keywords" content="">
	<meta name="description" content="">

	<meta property="og:title" content="{{ $setting->site_name }} - {{ $setting->site_slogan }}">
	<meta property="og:description" content="">
	<meta name="format-detection" content="telephone=no">

	<!-- Favicons Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('/template/main') }}/images/logo.png" />

	<!-- PWA Version -->
	<link rel="manifest" href="/storefront/manifest.json">

    <!-- Global CSS -->
	<link href="/storefront/assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/storefront/assets/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css">
	<link rel="stylesheet" href="/storefront/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/template/main/vendors/css/select2.min.css">
	<!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="/storefront/themes/fnb/assets/css/style.css">
    @if($storeSetting->template != "FNB")
    <link rel="stylesheet" type="text/css" href="/storefront/themes/nonfnb/assets/css/style.css">
    @endif

    <!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .cart-count {
            content:attr(value);
            font-size:12px;
            color: #fff;
            background: red;
            border-radius:50%;
            padding: 0 5px;
            position:absolute;
            width:20px;
            height:20px;
            opacity:0.9;
            margin-bottom:-10px;
            margin-left: 7px;
            right: 80px;
        }

        .search-results {
            background-color: #fff;
            border: 1px solid #ddd;
            position: absolute;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            border-radius: 5px;
            margin-top: 5px;
        }

        
        .search-result-item:hover, .search-result-item.highlight {
            background-color: #f0f0f0;  /* Change background on hover or highlight */
        }

        .search-results a {
            display: block;  /* Make items block for full-width click area */
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            text-decoration: none;  /* Remove underline from links */
            color: black;  /* Default text color */
        }
        
        #fnb-search-results {
            width: 100%;
            margin-top: 50px;
            border-radius: 10px;
        }


    </style>
    @yield('style')
</head>
<body data-theme-color="{{$storeSetting ? $storeSetting->theme_color : 'color-teal'}}">
<div class="page-wraper">

	<!-- Preloader -->
	<div id="preloader">
		<div class="loader">
			<div class="load-circle"><div></div><div></div></div>
		</div>
	</div>
    <!-- Preloader end-->
	@yield('content')
    @if($footer)
	    @include('storefront.template.themes.fnb.components.menubar')
    @endif

</div>
<!--**********************************
    Scripts
***********************************-->
<script src="/storefront/assets/js/jquery.js"></script>
<script src="/storefront/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/storefront/assets/vendor/swiper/swiper-bundle.min.js"></script><!-- Swiper -->
<script src="/storefront/assets/vendor/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js"></script><!-- Swiper -->
<script src="/storefront/assets/js/dz.carousel.js"></script><!-- Swiper -->
<script src="/storefront/assets/js/settings.js"></script>
<script src="/storefront/assets/js/custom.js"></script>
<script src="/template/main/vendors/js/select2.min.js"></script>
<script src="/storefront/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@yield('js')
<script>
$(document).ready(function() {
    $('#searchProduct').on('keydown', function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            window.location.href = `/{{$username}}/search/${event.target.value}`;
        }
    });
});

</script>
</body>
</html>
