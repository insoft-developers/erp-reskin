@php
    $setting = \App\Models\Setting::findorFail(1);
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
	<title>{{ $setting->site_name }} - {{ $setting->site_slogan }}</title>

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

	<!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="/storefront/assets/css/style.css">

    <!-- Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @yield('style')
</head>
<body>
<div class="page-wraper">

	<!-- Preloader -->
	<div id="preloader">
		<div class="loader">
			<div class="load-circle"><div></div><div></div></div>
		</div>
	</div>
    <!-- Preloader end-->
	@include('storefront.template.themes.fnb.components.sidebar')
	@yield('content')

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
<script src="/storefront/index.js"></script>
@yield('js')
</body>
</html>
