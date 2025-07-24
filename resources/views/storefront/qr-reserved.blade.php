@extends('storefront.template.layouts.notfound')
@section('style')
<style>
    ul li{
        list-style-type: disc;
        list-style-position: outside;
        margin-left:20px
    }
</style>
@endsection
@section('content')
<header class="header">
	<div class="main-bar">
		<div class="container">
			<div class="header-content">
				<div class="left-content">
					<a href="/{{$username}}" class="back-btn">
						<i class="fa-solid fa-home"></i>
					</a>
					<h4 class="title mb-0 text-nowrap">QR Meja Reserved</h4>
				</div>
				<div class="mid-content"></div>
				<div class="right-content"></div>
			</div>
		</div>
	</div>
</header>
<div class="page-content">
    <div class="container fb">
            <div class="bill-detail">
                <h4 class="text-center">Mohon Maaf</h4>
                <p>Meja yang kamu scan saat ini statusnya: <strong style="font-weight: 700">Reserved</strong></p>
                <ul class="">
                    <li>Jika sudah melakukan pemesanan meja sebelumnya, silahkan menunju kasir untuk membuka reservasi</li>
                    <li>Jika tidak, silahkan pindah ke meja lain yang masih tersedia <strong style="font-weight: 700">(Available)</strong></li>
                </ul>
            </div>
	</div>
</div>
@endsection
