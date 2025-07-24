<div class="title-bar">
	<span class="title mb-0">Offer for you</span>
</div>
<div class="swiper-btn-center-lr mt-0">
	<div class="swiper meat-swiper">
		<div class="swiper-wrapper">

			<div class="swiper-slide">
				<div class="card add-banner2">
                    @if($store->banner_image1)
                        <img class="img-fluid w-100" style="height: 160px; object-fit: cover; border-radius: 12px;" src="{{ Storage::url('images/storefront/banners/') }}{{ $store->banner_image1 }}" alt="{{ $store->banner_image1 }}">
                    @else
					    <img class="img-fluid w-100" style="height: 160px; object-fit: cover; border-radius: 12px;" src="/storefront/assets/images/banner/banner1.jpg" alt="/">
                    @endif
				</div>
			</div>

			<div class="swiper-slide">
				<div class="card add-banner2">
                    @if($store->banner_image2)
                        <img class="img-fluid w-100" style="height: 160px; object-fit: cover; border-radius: 12px;" src="{{ Storage::url('images/storefront/banners/') }}{{ $store->banner_image2 }}" alt="{{ $store->banner_image2 }}">
                    @else
					<img class="img-fluid w-100" style="height: 160px; object-fit: cover; border-radius: 12px;" src="/storefront/assets/images/banner/banner2.jpg" alt="/">
                    @endif
				</div>
			</div>

			<div class="swiper-slide">
				<div class="card add-banner2">
                    @if($store->banner_image3)
                        <img class="img-fluid w-100" style="height: 160px; object-fit: cover; border-radius: 12px;" src="{{ Storage::url('images/storefront/banners/') }}{{ $store->banner_image3 }}" alt="{{ $store->banner_image3 }}">
                    @else
					<img class="img-fluid w-100" style="height: 160px; object-fit: cover; border-radius: 12px;" src="/storefront/assets/images/banner/banner3.jpg" alt="/">
                    @endif
				</div>
			</div>

		</div>
	</div>
</div>
