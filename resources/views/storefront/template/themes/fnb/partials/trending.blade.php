<div class="title-bar">
	<span class="title mb-0">Product on trend</span>
</div>
<div class="swiper-btn-center-lr mt-0">
	<div class="swiper product-swiper">
		<div class="swiper-wrapper">
            @foreach($onSale as $tr)
			<div class="swiper-slide">
				<div class="card-item style-6">
					<a href="#" class="dz-media">
						<img src="/template/main/images/{{$tr->url}}" alt="image">
					</a>
					<div class="dz-content">
						<span class="product-title">1 {{$tr->unit}} </span>
						<h4 class="item-name">
							<a href="#">
								{{$tr->name}}
							</a>
						</h4>
						<div class="footer-wrapper">
							<div class="price-wrapper">
								<h6 class="current-price"><i class="fa-solid fa-rupiah-sign"></i>{{number_format($tr->price, 0, '.', ',')}}</h6>

							</div>
							@include('storefront.components.add-to-cart', ['product' => $tr])
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</div>
