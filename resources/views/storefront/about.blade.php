@extends('storefront.template.layouts.app')
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
					<h4 class="title mb-0 text-nowrap">Tentang</h4>
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
            <h4>{{$store->branch_name}}</h4>
            <p>{{$store->business_address}}</p>
            <p>{{$store->company_email}}</p>
            <p>{{$store->business_phone}}</p>
        </div>
	</div>
</div>
@endsection
