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
	<meta name="description" content="Kanakku is a Sales, Invoices & Accounts Admin template for Accountant or Companies/Offices with various features for all your needs. Try Demo and Buy Now.">
	<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
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
						<form action="email-verification.html" class="d-flex justify-content-center align-items-center">
							<div class="d-flex flex-column justify-content-lg-center p-4 p-lg-0 pb-0 flex-fill">
								<div class=" mx-auto mb-5 text-center">
									<img src="{{ asset('reskin') }}/assets/img/logo.svg" class="img-fluid" alt="Logo">
								</div>
								<div class="card border-0 p-lg-3 shadow-lg rounded-2">
									<div class="card-body">
										<div class="text-center mb-3">
											<h5 class="mb-2">Lupa Password</h5>
											<p class="mb-0">Tenang, Kami akan mengirim instruksi untuk mereset password anda.</p>
										</div>
										<div class="mb-3">
											<label class="form-label">Email</label>
											<div class="input-group">
												<span class="input-group-text border-end-0">
													<i class="isax isax-sms-notification"></i>
												</span>
												<input type="text" value="" class="form-control border-start-0 ps-0" placeholder="masukkan email anda">
											</div>
										</div>
										<div class="mb-3">
											<button type="submit" class="btn bg-primary-gradient text-white w-100">Reset Password</button>
										</div>
										<div class="text-center">
											<h6 class="fw-normal fs-14 text-dark mb-0">Kembali ke
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

</body>

</html>