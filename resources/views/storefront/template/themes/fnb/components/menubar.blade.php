<!-- Menubar -->
<div class="menubar-area style-7 footer-fixed rounded-0">
	<div class="toolbar-inner menubar-nav">
		<a href="/{{$username}}" class="nav-link">
			<i class="fa-solid fa-house"></i>
			<span>Home</span>
		</a>
		<a href="{{route('product.categories', $username)}}" class="nav-link">
			<i class="fa-solid fa-box"></i>
			<span>Kategori</span>
		</a>
		<!-- <a href="favorite.html" class="nav-link">
			<i class="fa-solid fa-heart"></i>
			<span>Favorites</span>
		</a> -->
		<a href="{{route('cart.index', $username)}}" class="nav-link">
            <span id="cart-count" class="cart-count">{{ $totalQuantity }}</span>
			<i class="fa-solid fa-bag-shopping"></i>
			<span>Keranjang</span>
		</a>
		<a href="{{route('storefront.about', $username)}}" class="nav-link">
			<i class="fa-solid fa-shop"></i>
			<span>Tentang</span>
		</a>
	</div>
</div>
<!-- Menubar -->
