<div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
            Pengaturan Tampilan
        </button>
    </h2>

    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
        <div class="accordion-body">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">
                    Template Storefront
                </label>
                <div class="col-sm-6">
                    <!-- <select name="" id="template" class="form-control">
                        <option value="FNB">Food & Beverages</option>
                        <option value="Non-FNB">Non Food & Beverages</option>
                    </select> -->
                    <div class="row radio-group">
                        <div class="col-6 text-center">
                            <label class="radio-label text-center select-template">
                                <img src="/template/main/images/home.png" style="width:100px;" alt="">
                                <p>Food & Beferages</p>
                                <input type="radio" name="template" id="FNB" value="FNB" {{$setting ? $setting->template == 'FNB' ? 'checked' : '' : ''}}>
                            </label>
                        </div>
                        <div class="col-6 text-center">
                            <label class="radio-label text-center select-template">
                                <img src="/template/main/images/home2.png" style="width:100px" alt="">
                                <p>Toko Online</p>
                                <input type="radio" name="template" id="Ecommerce" value="Ecommerce" {{$setting ? $setting->template == 'Ecommerce' ? 'checked' : '' : ''}}>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">
                    Warna Template
                </label>
                <div class="col-sm-6">
                <ul class="theme-color-settings">
                    <li>
                        <input class="filled-in" id="primary_color_10" name="theme_color" type="radio" value="color-teal" {{$setting ? $setting->theme_color == 'color-teal' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_10"></label>
                        <span>Default</span>
                    </li>
                    <li>
		        		<input class="filled-in" id="primary_color_2" name="theme_color" type="radio" value="color-green" {{$setting ? $setting->theme_color == 'color-green' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_2"></label>
                        <span>Green</span>
                    </li>
                    <li>
                        <input class="filled-in" id="primary_color_3" name="theme_color" type="radio" value="color-blue" {{$setting ? $setting->theme_color == 'color-blue' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_3"></label>
                        <span>Blue</span>
                    </li>
                    <li>
                        <input class="filled-in" id="primary_color_4" name="theme_color" type="radio" value="color-pink" {{$setting ? $setting->theme_color == 'color-pink' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_4"></label>
                        <span>Pink</span>
                    </li>
                    <li>
                        <input class="filled-in" id="primary_color_5" name="theme_color" type="radio" value="color-yellow" {{$setting ? $setting->theme_color == 'color-yellow' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_5"></label>
                        <span>Yellow</span>
                    </li>
                    <li>
                        <input class="filled-in" id="primary_color_6" name="theme_color" type="radio" value="color-orange" {{$setting ? $setting->theme_color == 'color-orange' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_6"></label>
                        <span>Orange</span>
                    </li>
                    <li>
                        <input class="filled-in" id="primary_color_7" name="theme_color" type="radio" value="color-purple" {{$setting ? $setting->theme_color == 'color-purple' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_7"></label>
                        <span>Purple</span>
                    </li>
                    <li>
		        		<input class="filled-in" id="primary_color_1" name="theme_color" type="radio" value="color-red" {{$setting ? $setting->theme_color == 'color-red' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_1"></label>
                        <span>Red</span>
                    </li>
                    <li>
		        		<input class="filled-in" id="primary_color_9" name="theme_color" type="radio" value="color-lightblue" {{$setting ? $setting->theme_color == 'color-lightblue' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_9"></label>
                        <span>Light blue</span>
                    </li>
                    <li>
                        <input class="filled-in" id="primary_color_11" name="theme_color" type="radio" value="color-lime" {{$setting ? $setting->theme_color == 'color-lime' ? 'checked' : '' : ''}} />
		        		<label for="primary_color_11"></label>
                        <span>Lime</span>
                    </li>
                    <!-- <li>
                        <input class="filled-in" id="primary_color_12" name="theme_color" type="radio" value="color-deeporange" />
		        		<label for="primary_color_12"></label>
                        <span>Deep orange</span>
                    </li> -->
                </ul>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">
                    Banner
                </label>
                <div class="col-sm-10">
                    <label for="">Banner 1</label>
                    <div class="row">
                        <div class="col-12">
                            @if(@$setting->banner_image1)
                            <img src="{{Storage::url('images/storefront/banners/')}}{{$setting->banner_image1}}" style="width:200px" alt="{{$setting->banner_image1}}">
                            @endif
                        </div>
                        <div class="col-6">

                            <input type="file" id="bannerImage1" name="bannerImage1" class="form-control">
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" id="bannerLink1" placeholder="Banner Link" value="{{@$setting->banner_link1 ? @$setting->banner_link1 : ''}}">
                        </div>
                    </div>
                    <label for="">Banner 2</label>
                    <div class="row">
                    <div class="col-12">
                            @if(@$setting->banner_image2)
                            <img src="{{Storage::url('images/storefront/banners/')}}{{$setting->banner_image2}}" style="width:200px" alt="{{$setting->banner_image2}}">
                            @endif
                        </div>
                        <div class="col-6">
                            <input type="file" id="bannerImage2" name="bannerImage2" class="form-control">
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" id="bannerLink2" placeholder="Banner Link" value="{{@$setting->banner_link2 ? @$setting->banner_link2 : ''}}">
                        </div>
                    </div>
                    <label for="">Banner 3</label>
                    <div class="row">
                        <div class="col-12">
                            @if(@$setting->banner_image3)
                            <img src="{{Storage::url('images/storefront/banners/')}}{{$setting->banner_image3}}" style="width:200px" alt="{{$setting->banner_image3}}">
                            @endif
                        </div>
                        <div class="col-6">
                            <input type="file" id="bannerImage3" name="bannerImage3" class="form-control">
                        </div>
                        <div class="col-6">
                            <input type="text" class="form-control" id="bannerLink3" placeholder="Banner Link" value="{{@$setting->banner_link3 ? @$setting->banner_link3 : ''}}">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop20">
                    <div class="alert alert-success">
                        Randu sudah menyediakan template Banner dari Canva supaya desain bannermu keren, Download disini <a
                                                        href="https://www.canva.com/design/DAGO6MlXUFY/YJrb_6o9tcvg7q2TLwobRA/view?utm_content=DAGO6MlXUFY&utm_campaign=designshare&utm_medium=link&utm_source=publishsharelink&mode=preview" style="color: blue;"
                                                        target="_blank">Template Banner Canva</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
