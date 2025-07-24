<!-- Menubar -->
<div class="menubar-area footer-fixed rounded-0">
	<div class="toolbar-inner menubar-nav">
		<a href="/{{$username}}" class="nav-link active">
			<i class="icon feather icon-home"></i>
			<span>Home</span>
		</a>
		<a href="{{route('product.categories', $username)}}" class="nav-link">
			<i class="icon feather icon-grid"></i>
			<span>Kategori</span>
		</a>
		<!-- <a href="cart.html" class="nav-link cart-handle">
			<div class="hexad-menu">
				<img src="/storefront/themes/nonfnb/assets/images/menu-shape-dark.svg" class="shape-dark" alt="">
				<img src="/storefront/themes/nonfnb/assets/images/menu-shape-light.svg" class="shape-light" alt="">
				<i class="icon feather icon-shopping-bag"></i>
			</div>
		</a> -->
		<a href="{{route('cart.index', $username)}}" class="nav-link">
			<i class="icon feather icon-shopping-bag"></i>
			<span>Keranjang</span>
		</a>
		<a href="{{route('storefront.about', $username)}}" class="nav-link">
			<i class="icon feather icon-help-circle"></i>
			<span>Tentang</span>
		</a>
	</div>
</div>
<!-- Menubar -->
