<div class="swiper category-swiper">
	<div class="swiper-wrapper">
		<div class="swiper-slide">
			<a href="{{route('product.categories', $username)}}" class="category-btn">All</a>
		</div>
        @foreach($categories as $category)
		<div class="swiper-slide">
			<a href="{{route('product.category', ['username' => $username, 'category' => $category->id])}}" class="category-btn">{{$category->name}}</a>
		</div>
		@endforeach
	</div>
</div>
