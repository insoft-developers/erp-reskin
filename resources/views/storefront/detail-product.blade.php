@extends('storefront.template.layouts.app')
@section('style')
    <style>
        /* Fonts */
        @import url('https://fonts.googleapis.com/css?family=Raleway:800');


        /* Add to cart button */
        .container2 .cart {
            position: absolute;
            width: 20px;
            height: 20px;
            background: red;
            border-radius: 50%;
            margin-bottom: -5px;
            margin-left: -32px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            transition: all 0.2s ease;
            z-index: 2;
        }

        .container2 .cart span {
            font-size: 25px;
        }

        /* .container2:hover .cart {
              margin-left: -15px
            } */

        .container2 .cart:active {
            transform: scale(0.9);
        }

        /* The "Added" text */
        .container2 .added {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
            font-size: 18px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            transform: translateY(-100%);
            transition: all 0.3s ease;
        }

        /* .container2:hover .added {
              transform: translateY(0);
            } */

        /* Add to cart icon */
        .container2 .icon {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 22px;
            height: 22px;
            background: #ff5e62;
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: all 0.3s ease;
            z-index: 1;
        }

        .container2:hover .icon {
            transform: translate(-50%, -50%) scale(1);
        }

        .container2 .icon::before,
        .container2 .icon::after {
            content: '';
            position: absolute;
            background: #fff;
        }

        .container2 .icon::before {
            width: 50%;
            height: 2px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .container2 .icon::after {
            width: 2px;
            height: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Shake Animation */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            50% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-5px);
            }
        }

        .shake {
            animation: shake 0.5s;
        }

        /* Send to cart animation */
        @keyframes sendtocart {
            0% {
                transform: translate(0, 0);
                opacity: 1;
            }

            100% {
                transform: translate(calc(100vw - 50px), calc(-160vh + 20px));
                opacity: 0;
            }
        }

        .sendtocart {
            animation: sendtocart 1s forwards cubic-bezier(1.000, 0.440, 0.840, 0.165);
        }

        .add-to-cart {
            z-index: 100;
        }
    </style>
@endsection
@section('content')
    <div class="container2">
        <header class="header transparent">
            <div class="main-bar">
                <div class="container">
                    <div class="header-content">
                        <div class="left-content">
                            <a href="javascript:void(0);" class="back-btn icon-box-3 icon-sm">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21.0001 9.99999H7.82806L11.4141 6.41399C11.7784 6.03679 11.98 5.53158 11.9754 5.00719C11.9709 4.48279 11.7605 3.98117 11.3897 3.61035C11.0189 3.23954 10.5173 3.0292 9.99286 3.02464C9.46847 3.02009 8.96327 3.22167 8.58606 3.58599L1.58606 10.586C1.21112 10.961 1.00049 11.4697 1.00049 12C1.00049 12.5303 1.21112 13.0389 1.58606 13.414L8.58606 20.414C8.77056 20.605 8.99124 20.7574 9.23525 20.8622C9.47926 20.967 9.7417 21.0222 10.0073 21.0245C10.2728 21.0268 10.5362 20.9762 10.782 20.8756C11.0278 20.7751 11.2511 20.6266 11.4389 20.4388C11.6266 20.251 11.7751 20.0277 11.8757 19.7819C11.9763 19.5361 12.0269 19.2727 12.0246 19.0072C12.0223 18.7416 11.9671 18.4792 11.8623 18.2352C11.7574 17.9912 11.6051 17.7705 11.4141 17.586L7.82806 14H21.0001C21.5305 14 22.0392 13.7893 22.4143 13.4142C22.7893 13.0391 23.0001 12.5304 23.0001 12C23.0001 11.4696 22.7893 10.9609 22.4143 10.5858C22.0392 10.2107 21.5305 9.99999 21.0001 9.99999Z"
                                        fill="#303733"></path>
                                </svg>
                            </a>
                        </div>
                        <div class="mid-content">
                        </div>
                        <!-- <div class="right-content d-flex align-items-center">
                <a href="{{ route('cart.index', $username) }}" class="item-bookmark icon-box-3 icon-sm" id="cart">
                                    <span id="cart-count" class="cart-count-header cart">{{ $totalQuantity }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M160 112c0-35.3 28.7-64 64-64s64 28.7 64 64l0 48-128 0 0-48zm-48 48l-64 0c-26.5 0-48 21.5-48 48L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-208c0-26.5-21.5-48-48-48l-64 0 0-48C336 50.1 285.9 0 224 0S112 50.1 112 112l0 48zm24 48a24 24 0 1 1 0 48 24 24 0 1 1 0-48zm152 24a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"/></svg>
                </a>
                </div> -->
                    </div>
                </div>
            </div>
        </header>
        <div class="product-view">
            <div class="container mt-5">
                <div class="overlay-black-light">
                    @if (!empty($product->productImages) && count($product->productImages) > 0)
                        <img src="{{ Storage::url('images/product/' . $product->productImages[0]->url) }}" alt="/">
                    @else
                        <img src="{{ asset('template/main/images/product-placeholder.png') }}" alt="image">
                    @endif
                </div>
            </div>
            <div class="account-box style-2">
                <div class="container">
                    <div class="company-detail">
                        <div class="detail-content">
                            <div class="flex-1">
                                <h4>{{ $product->name }}</h4>
                                <p class="text-light">{!! strlen($product->description) > 120 ? substr($product->description, 0, 120) . '...' : $product->description !!}</p>
                                <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#productModal"><u>Selengkapnya</u></a>
                            </div>
                        </div>

                    </div>
                    <div class="divider divider-dashed border-light"></div>
                    <div class="item-list-2">
                        <div class="price">
                            <span class="text-style text-soft">Harga</span>
                            <h3 class="sub-title"><i
                                    class="fa-solid fa-rupiah-sign"></i>{{ number_format($product->price, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="stock-wrapper" style="margin-top:-20px;">
                            @if ($product->buffered_stock == 1)
                                @if ($product->quantity > 0)
                                    <span style="color: green;">Stok Tersedia : {{ number_format($product->quantity) }}</span>
                                @else
                                    <span style="color: red;">Stok Kosong</span>
                                @endif
                            @else
                                <span style="color: green;">Stok Tersedia</span>
                            @endif
                        </div>

                    </div>


                    @if ($variants)
                        <div class="flex-nowrap mb-5">
                            <div class="sub-title">
                                @foreach ($variants as $groupName => $groupVariants)
                                    <div class="variant-group" id="group-{{ $loop->index + 1 }}">
                                        <h5>{{ $groupName }}</h5>
                                        @foreach ($groupVariants as $variant)
                                            <label class="form-check-label row mb-2">
                                                <div class="col-6">
                                                    {{ $variant->varian_name }}
                                                </div>
                                                <div class="col-6 d-flex justify-content-end align-items-center">

                                                    <span style="margin-right:10px">+
                                                        Rp{{ number_format($variant->varian_price, 0, ',', '.') }}</span>&nbsp;
                                                    @if ($variant->single_pick == 1)
                                                        <input type="radio" name="{{ $groupName }}"
                                                            value="{{ $variant->id }}" class="ml-3 varian-item"
                                                            data-variant-name="{{ $variant->varian_name }}"
                                                            data-variant-price="{{ $variant->varian_price }}" required>
                                                    @else
                                                        <input type="checkbox" name="{{ $groupName }}"
                                                            value="{{ $variant->id }}" class="ml-3 varian-item"
                                                            data-variant-name="{{ $variant->varian_name }}"
                                                            data-variant-price="{{ $variant->varian_price }}">
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                        <div class="row" id="{{ $groupName }}" style="display:none">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i
                                                        class="fa-regular fa-clipboard"></i></span>
                                                <input type="text" class="form-control notes"
                                                    placeholder="Catatan Varian" id="note-{{ $loop->index + 1 }}"
                                                    data-group="{{ $groupName }}" data-variant="{{ $variant->id }}"
                                                    aria-describedby="basic-addon1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="divider divider-dotted border-light"></div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- <div class="d-flex align-items-center justify-content-between">
                <div class="badge bg-accent rounded-sm badge-warning font-w400">20% OFF DISCOUNT</div>
                <a href="javascript:void(0);"><h6 class="mb-0 font-14">Apply promo code</h6></a>
                </div> -->
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer fixed ssc">
            <div class="container d-flex">
                <div class="dz-stepper border-1 rounded-stepper stepper-fill small-stepper mt-1">
                    <input class="stepper form-control quantity cart-item" type="text" value="1" id="qty"
                        data-product-id="{{ $product->id }}">
                </div>

                <div class="w-10"></div>

                <input type="hidden" name="total" id="totalPrice" value="{{ $product->price }}">
                <input type="hidden" name="total" id="totalAmount" value="{{ $product->price }}">
                <button class="btn btn-primary btn-block w-50 add-to-cart font-12" data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}"
                    data-product-image="{{ $product->url }}" data-product-unit="{{ $product->unit }}"
                    data-product-weight="{{ $product->weight }}" disabled><span id="total"></span> Tambah</button>
                <div class="w-10"></div>
                <div class="right-content d-flex align-items-center w-10">
                    <a href="{{ route('cart.index', $username) }}" class="item-bookmark icon-box-3 icon-sm bg-primary"
                        id="cart">
                        <span id="cart-count" class="cart-count-header cart">{{ $totalQuantity }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path
                                d="M160 112c0-35.3 28.7-64 64-64s64 28.7 64 64l0 48-128 0 0-48zm-48 48l-64 0c-26.5 0-48 21.5-48 48L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-208c0-26.5-21.5-48-48-48l-64 0 0-48C336 50.1 285.9 0 224 0S112 50.1 112 112l0 48zm24 48a24 24 0 1 1 0 48 24 24 0 1 1 0-48zm152 24a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="productModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $product->name }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{!! $product->description !!}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FOOTER -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let variants = {}
            // console.log("VARIANTS", variants)
            function updateCartDisplay(res) {
                $('#cart-count').text(res.totalQuantity);
                console.log("RES", res)
            }
            let amount = $('#totalPrice').val();
            //let totalAmount = $('#totalAmount').val();


            $.ajax({
                url: '{{ route('cart.data', $username) }}',
                method: 'GET',
                success: function(response) {

                    updateCartDisplay(response);
                }
            });
            $('input[type="radio"], input[type="checkbox"]').change(function() {
                let groupName = $(this).attr('name');
                let variantPrice = $(this).data('variant-price');
                let variantName = $(this).data('variant-name');
                let selectedValue = $(this).val();
                console.log("Selected Value", selectedValue)
                // Update the variants object

                if (!variants[groupName]) {
                    variants[groupName] = {
                        data: [], // Initialize the data array
                        notes: "" // Initialize notes
                    };
                }
                console.log("VARIANTS", variants)
                if ($(this).attr('type') === 'radio') {
                    // Clear previous radio selection in this group
                    variants[groupName].data = [{
                        id: selectedValue,
                        price: variantPrice,
                        name: variantName,
                        qty: 1
                    }];
                    $('#' + groupName).show();
                } else {
                    if ($(this).is(':checked')) {
                        // Add selection for checkbox
                        variants[groupName].data.push({
                            id: selectedValue,
                            price: variantPrice,
                            name: variantName,
                            qty: 1
                        });
                        $('#' + groupName).show();
                    } else {
                        // Remove selection for checkbox
                        variants[groupName].data = variants[groupName].data.filter(selection => selection
                            .id !== selectedValue);
                    }
                }
                let totalAmount = (parseInt(amount) + parseInt(variantPrice))
                console.log("TOTALSSS: ", totalAmount)

                // $('#totalAmount').val(totalAmount)
                console.log('Current variants:', variants);
                updateTotalAmount();
            });
            $('input.notes').change(function() {
                let groupName = $(this).data('group');
                let variantId = $(this).data('variant');
                let note = $(this).val();
                // Update the variants object with the note
                if (!variants[groupName]) {
                    variants[groupName].notes = {};
                }
                // variants[groupName] = variants[groupName].notes || {};
                variants[groupName].notes = note;
                // updateProductNotes(variants, groupName, variantId, note)
                console.log('Current variants with Notes:', variants);

            });

            function updateProductNotes(data, groupName, id, newNotes) {
                console.log(groupName, id, newNotes)
                if (data[groupName]) {
                    const item = data[groupName].find(product => product.id === id);
                    if (item) {
                        item.notes = newNotes;
                    }
                }
            }

            function updateTotalAmount() {
                let quantityInput = $('#qty').val();
                let totalPrice = parseInt(amount);
                console.log("VARIAAAAANTS", variants);
                if (!$.isEmptyObject(variants)) {
                    for (let group in variants) {
                        if (Array.isArray(variants[group].data)) {
                            variants[group].data.forEach(function(selection) {
                                totalPrice += parseFloat(selection.price);
                            });
                        }
                    }
                }
                let totalAmount = parseInt(quantityInput) * parseInt(totalPrice);
                if (totalAmount > 0) {
                    $('#total').html("Rp" + totalAmount.toLocaleString('id-ID'))
                    $('#totalAmount').val(totalAmount)
                    $('.add-to-cart').prop('disabled', false)
                } else {
                    $('#total').html("")
                    $('#totalAmount').val(0)
                    $('.add-to-cart').prop('disabled', true)
                }
                console.log("TOTAL AMOUNT: ", totalAmount, quantityInput)

            }
            $('.quantity').change(function() {
                updateTotalAmount();
            })

            $('.quantity').keyup(function() {
                updateTotalAmount();
            })


            $('.add-to-cart').click(function() {

                let quantityInput = $('#qty').val();
                var button = $(this);
                var productId = button.data('product-id');
                var isValid = true;
                console.log("VARIANTS INPUT", variants);
                $('input[type="radio"][required]').each(function() {
                    var name = $(this).attr('name');
                    if ($('input[name="' + name + '"]:checked').length === 0) {
                        isValid = false;
                        alert('Pilihan ' + name + ' belum dipilih.');
                        return false; // Stop looping if any required radio is not selected
                    }
                });

                var cart_count = $("#cart-count").text();
                var product = {
                    id: button.data('product-id'),
                    name: button.data('product-name'),
                    price: button.data('product-price'),
                    image: button.data('product-image'),
                    unit: button.data('product-unit'),
                    weight: button.data('product-weight'),
                    subtotal: parseInt($('#totalAmount').val()),
                    quantity: quantityInput,
                    notes: ''
                };
                if (Object.keys(variants).length > 0) {
                    // console.log("VARIANTSSS", variants)
                    product.variants = variants;
                }
                if (isValid) {

                    $.ajax({
                        url: '{{ route('cart.add', $username) }}',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            cart_count: cart_count,
                            product: product,
                            quantity: quantityInput,
                            username: '{{ $username }}'
                        }),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {

                            if (response.status == 'failed') {
                                Swal.fire({
                                    icon: "warning",
                                    title: "Gagal",
                                    html: response.message,
                                    footer: ''
                                });
                            } else {
                                var cart = $('#cart');
                                var cartTotal = cart.attr('data-totalitems');
                                var newCartTotal = parseInt(cartTotal) + 1;

                                updateCartDisplay(response);
                                cart.addClass('shake');
                                setTimeout(function() {
                                    cart.removeClass('shake');
                                }, 500)

                            }
                            // Animate

                            // end of animate

                            // swal.fire('Informasi', 'Berhasil memasukan ke keranjang', 'success').then(() => {
                            // })
                        }
                    });
                }
            });
            updateTotalAmount();

        });
    </script>
@endsection
