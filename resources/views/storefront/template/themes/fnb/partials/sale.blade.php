<div class="title-bar">
	<span class="title mb-0">Sale for you</span>
</div>
<div class="container">
    @foreach($onSale as $sale)
    <div class="product-list">
		<div class="dz-content">
			<h4 class="item-name">
				<a href="cart.html">
					Goat + Chicken Skinless + Cleaned Prawns
				</a>
			</h4>
			<div class="offer-code">
				FLAT 60% off Code: 636GCP
			</div>
			<div class="price-wrapper">
				<h6 class="current-price"><i class="fa-solid fa-indian-rupee-sign"></i>930</h6>
				<span class="old-price"><i class="fa-solid fa-indian-rupee-sign"></i>1100</span>
			</div>
			<div class="footer-wrapper">
				<span class="product-title">Combo pack</span>
			</div>
		</div>
		<div class="text-end">
			<a href="cart.html" class="dz-media media-100">
				<img class="rounded-sm" src="assets/images/product/1.jpg" alt="image">
			</a>
			<a href="cart.html" class="btn btn-sm btn-block btn-outline-primary">ADD</a>
		</div>
	</div>
	<div class="col-xs-6 col-md-4 col-lg-3">
		<div class="card-item style-6">
			<a href="product-detail.html" class="dz-media">
				<img src="/template/main/images/{{$sale->url}}" alt="image">
			</a>
			<div class="dz-content">
				<span class="product-title">Combo pack</span>
				<h4 class="item-name">
					<a href="#">
                        {{$sale->name}}
					</a>
				</h4>
				<div class="footer-wrapper">
					<div class="price-wrapper">
						<h6 class="current-price"><i class="fa-solid fa-rupiah-sign"></i>{{number_format($sale->price, 0, '.', ',')}}</h6>
					</div>
					@include('storefront.components.add-to-cart', ['product' => $sale])
				</div>
			</div>
		</div>
	</div>
    @endforeach
</div>
