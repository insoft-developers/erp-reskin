@extends('storefront.template.layouts.app')
@section('style')
<link rel="stylesheet" href="{{ asset('/storefront/themes/custom.css') }}">
@endsection
@section('content')
    <header class="header transparent">
    	<div class="main-bar">
    		<div class="container">
    			<div class="header-content">
    				<div class="left-content">
    					<a href="javascript:void(0);" class="back-btn icon-box-3 icon-sm">
    						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    							<path d="M21.0001 9.99999H7.82806L11.4141 6.41399C11.7784 6.03679 11.98 5.53158 11.9754 5.00719C11.9709 4.48279 11.7605 3.98117 11.3897 3.61035C11.0189 3.23954 10.5173 3.0292 9.99286 3.02464C9.46847 3.02009 8.96327 3.22167 8.58606 3.58599L1.58606 10.586C1.21112 10.961 1.00049 11.4697 1.00049 12C1.00049 12.5303 1.21112 13.0389 1.58606 13.414L8.58606 20.414C8.77056 20.605 8.99124 20.7574 9.23525 20.8622C9.47926 20.967 9.7417 21.0222 10.0073 21.0245C10.2728 21.0268 10.5362 20.9762 10.782 20.8756C11.0278 20.7751 11.2511 20.6266 11.4389 20.4388C11.6266 20.251 11.7751 20.0277 11.8757 19.7819C11.9763 19.5361 12.0269 19.2727 12.0246 19.0072C12.0223 18.7416 11.9671 18.4792 11.8623 18.2352C11.7574 17.9912 11.6051 17.7705 11.4141 17.586L7.82806 14H21.0001C21.5305 14 22.0392 13.7893 22.4143 13.4142C22.7893 13.0391 23.0001 12.5304 23.0001 12C23.0001 11.4696 22.7893 10.9609 22.4143 10.5858C22.0392 10.2107 21.5305 9.99999 21.0001 9.99999Z" fill="#303733"></path>
    						</svg>
    					</a>
    				</div>
    				<div class="mid-content">
                        <h6 class="title">All Categories</h6>
    				</div>
    			</div>
    		</div>
    	</div>
    </header>
    <div class="page-content space-top p-b60">
        <div class="container p-0">
            <div class="row">
                @if($categories)
                @foreach($categories as $category)
				@php
				$total = \App\Models\Product::where('category_id', $category->id)->count();
				@endphp
				<div class="col-12 m-b15">
					<div class="dz-category-card">
						<div class="category-image" style="background-image: url('{{ Storage::url('images/category/') }}{{ $category->image }}');"></div>
						<div class="category-content">
							<h3 class="title">{{ $category->name }}</h3>
							<p>{{$total}} items</p>
							<div class="shop-btn"><a href="{{route('product.category', ['username' => $username, 'category' => $category->id])}}" class="btn btn-primary btn-sm">Shop Now</a></div>
						</div>
					</div>
				</div>
				@endforeach
                @else
                <div class="text-center">
                    <h5>Kategori belum tersedia</h5>
                </div>
                @endif
			</div>
        </div>
    </div>
@endsection
