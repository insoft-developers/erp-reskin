<div class="title-bar offer-box">
	<span class="title mb-0">Categories</span>
</div>
<div class="row catagore-bx g-4">
    @foreach($categories as $category)
	<div class="col-4 text-center">
		<a href="{{route('product.category', ['username' => $username, 'category' => $category->id])}}">
			<div class="dz-media media-60">
                @if($category->image)
                <img src="{{Storage::url('images/category/')}}{{$category->image}}" alt="image">
                @else
				<img src="/storefront/assets/images/categore/5.png" alt="image">
                @endif
			</div>
			<span class="dz-media media-60">{{$category->name}}</span>
		</a>
	</div>
    @endforeach
    @if(count($categories) > 4)
    <div class="col-4 text-center">
        <a href="{{route('product.categories', $username)}}">
            <div class="dz-media media-60 icon-box-6">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 297.001 297.001"><path d="M107.883 0h-85.27C10.144 0 .001 10.143.001 22.612v85.27c0 12.469 10.143 22.612 22.612 22.612h85.27c12.469 0 22.612-10.143 22.612-22.612v-85.27C130.493 10.143 120.352 0 107.883 0zm166.505 0h-85.27c-12.469 0-22.612 10.143-22.612 22.612v85.27c0 12.469 10.143 22.612 22.612 22.612h85.27c12.469 0 22.612-10.143 22.612-22.612v-85.27C297 10.143 286.857 0 274.388 0zM107.883 166.507h-85.27c-12.469 0-22.612 10.142-22.612 22.611v85.27C.001 286.857 10.144 297 22.613 297h85.27c12.469 0 22.612-10.143 22.612-22.612v-85.27c-.002-12.469-10.143-22.611-22.612-22.611zm166.505 0h-85.27c-12.469 0-22.612 10.143-22.612 22.612v85.27c0 12.469 10.143 22.612 22.612 22.612h85.27C286.857 297 297 286.857 297 274.388v-85.27c0-12.469-10.143-22.611-22.612-22.611z"></path></svg>
            </div>
	    	<span class="dz-media media-60">Kategori Lainnya</span>
	    </a>
    </div>
    @endif
</div>
