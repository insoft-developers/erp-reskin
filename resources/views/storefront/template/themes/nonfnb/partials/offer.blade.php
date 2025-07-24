<div class="dz-banner">
	<div class="swiper banner-swiper">
		<div class="swiper-wrapper">
			<div class="swiper-slide">
                @if($store->banner_image1)
                    <img src="{{Storage::url('images/storefront/banners/')}}{{$store->banner_image1}}" alt="{{$store->banner_image1}}" style="width:100%">
                @else
				    <img src="/storefront/assets/images/banner/banner1.jpg" alt="/" style="width:100%">
                @endif

			</div>
			<div class="swiper-slide">
                @if($store->banner_image2)
                    <img src="{{Storage::url('images/storefront/banners/')}}{{$store->banner_image2}}" alt="{{$store->banner_image2}}" style="width:100%">
                @else
				    <img src="/storefront/assets/images/banner/banner2.jpg" alt="/" style="width:100%">
                @endif
			</div>
			<div class="swiper-slide">
                @if($store->banner_image3)
                    <img src="{{Storage::url('images/storefront/banners/')}}{{$store->banner_image3}}" alt="{{$store->banner_image3}}" style="width:100%">
                @else
				    <img src="/storefront/assets/images/banner/banner3.jpg" alt="/" style="width:100%">
                @endif
			</div>
		</div>
		<div class="swiper-pagination style-2"></div>
	</div>
</div>
