<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $head->title }}</title>

    <meta name="title" content="{{ $head->title }}" />
    <meta name="description" content="{{ $head->product->name }}, {{ $head->title }}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ env('APP_URL') }}/checkout/{{ $head->id }}/{{ $head->slug }}" />
    <meta property="og:title" content="{{ $head->title }}" />
    <meta property="og:description" content="{{ $head->product->name }}, {{ $head->title }}" />
    {{-- <meta property="og:image" content="" /> --}}

    {{-- <meta property="twitter:card" content="summary_large_image" /> --}}
    <meta property="twitter:url" content="{{ env('APP_URL') }}/checkout/{{ $head->id }}/{{ $head->slug }}" />
    <meta property="twitter:title" content="{{ $head->title }}" />
    <meta property="twitter:description" content="{{ $head->product->name }}, {{ $head->title }}" />
    {{-- <meta property="twitter:image" content="https://metatags.io/images/meta-tags.png" /> --}}

    <link rel="shortcut icon" type="image/x-icon" href="/template/main/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/template/main/css/bootstrap.min.css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/vendors.min.css" />
    <!--! END: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="/template/main/css/theme.min.css" />
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2-theme.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @if ($head->script_header)
        {!! $head->script_header !!}
    @endif
    <style>
        {!! $head->css_code !!}
    </style>
    <style>
        .pc-bump-editor {
            margin-top: 2em;
            /* padding: 2em; */
            background-color: #eee;
        }

        .pc-box .bump {
            margin: 0 auto;
        }

        .bump {
            padding: 1em;
            border: 2px dashed red;
            background-color: #fbf7eb;
        }

        .bump__checkbox {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            padding: 0.4em 0.8em;
            color: #000;
            font-weight: 700;
            background-color: #f5d765;
        }

        .bump__arrow {
            margin-right: 7px;
            min-width: 20px;
        }

        .bump__arrow img {
            width: 100%;
            height: auto;
        }


        .pc-bump-editor .bump__checkbox label {
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
        }

        .bump__checkbox label {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
        }

        .bump label {
            margin: 0;
            width: 100%;
            cursor: pointer;
        }

        .input-checkbox,
        .input-radio {
            display: none;
        }

        .bump .input-checkbox+span:after {
            border-color: #000;
        }

        .input-checkbox+span:after {
            content: "";
            position: absolute;
            width: 9px;
            height: 5px;
            background: transparent;
            top: 3px;
            left: 2px;
            border: 2px solid #00579e;
            border-top: none;
            border-right: none;
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
            opacity: 0;
        }

        .bump__checkbox label span {
            min-width: 15px;
        }

        .input-checkbox+span,
        .input-checkbox:checked+span {
            -webkit-transition: background-color .4s linear;
            transition: background-color .4s linear;
        }

        .input-checkbox+span {
            display: inline-block;
            margin: 0;
            width: 15px;
            height: 15px;
            vertical-align: middle;
            cursor: pointer;
            position: relative;
            border: 1px solid #bbb;
            background-color: #fff;
            border-radius: 2px;
        }

        .bump__checkbox .input-checkbox-text {
            width: 100%;
            overflow-wrap: break-word;
        }

        .bump__checkbox label span {
            min-width: 15px;
        }

        .input-checkbox-text {
            margin-left: 0.5em;
            vertical-align: middle;
        }

        .bump__checkbox .input-text {
            padding: 0 5px;
            width: 100%;
            font-weight: 700;
        }

        .bump .input-text {
            padding: 0;
            width: 100%;
            background: transparent;
            border: 0;
        }

        .bump .input-text:focus-visible {
            border: 0;
            outline: 0;
            box-shadow: 0;
            ;
        }

        button,
        input {
            overflow: visible;
        }

        .bump__title {
            margin-top: 5px;
            color: red;
            font-weight: 700;
        }

        button,
        input {
            overflow: visible;
        }

        .bump__content {
            margin-top: 5px;
            font-size: .9em;
        }

        textarea {
            overflow: auto;
            resize: vertical;
        }

        .poduct-variable {
            background-color: white !important;
        }

        .product-variable-radio {
            background: transparent !important;
            border: 0 !important;
            transform: scale(1.5);
            outline: none !important;
            box-shadow: none !important;
        }

        @media screen and (max-width: 468px) {
            .img-group img {
                width: 40%;
            }

            .table-bordered td {
                font-size: 13px !important;
            }

            .bumb-text {
                font-size: 15px !important;
            }
        }

        .bumb-text {
            font-size: 20px;
        }

        @media screen and (max-width: 768px) {

            .content-checkout,
            .card-body {
                padding: 0px !important;
            }

            .btn-checkout {
                font-size: 1.4em;
            }

            .form-checkout {
                padding: 20px 20px;
            }

            .f24px {
                font-size: 20px;
            }

            .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
                top: -7px;
            }

            .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder,
            .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
                font-size: 20px;
            }

            .form-control {
                padding: .490rem .75rem !important;
            }

            .content-checkout img {

                height: auto !important;
                width: 100% !important;

            }
        }
    </style>
</head>

<body>
    {!! $head->html_code !!}

    <div class="container px-4 mx-auto pb-10">
        <div class="grid grid-cols-12">
            <div class="col-span-12 flex flex-col items-center">
                <form method="POST"
                    action="{{ $head->click_to_wa == 0 ? route('landing-page.checkout', ['id' => $id]) : route('landing-page.checkout-wa', ['landing_id' => $id]) }}"
                    class="card p-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $head->product->id }}" />
                    <input type="hidden" name="landing_id" value="{{ $id }}" />
                    <input type="hidden" name="user_id" value="{{ $head->user_id }}" />
                    <div class="card-body">
                        @if ($head->click_to_wa)
                            {{-- empy form when click to wa is true --}}
                        @else
                            @if ($head->with_customer_name)
                                <div class="w-[320px] sm:w-[600px] md:w-[750px]">
                                    <input type="text" class="form-control" placeholder="Nama*" required
                                        name="cust_name" />
                                </div>
                            @endif
                            @if ($head->with_customer_wa_number)
                                <div class="w-[320px] sm:w-[600px] md:w-[750px] mt-3">
                                    <input type="number" class="form-control" placeholder="Nomor Whatsapp*" required
                                        name="cust_phone" />
                                </div>
                            @endif
                            @if ($head->with_customer_email)
                                <div class="w-[320px] sm:w-[600px] md:w-[750px] mt-3">
                                    <input type="email" class="form-control" placeholder="Alamat Email*" required
                                        name="cust_email" />
                                </div>
                            @endif
                            @if ($head->with_customer_full_address)
                                <div class="w-[320px] sm:w-[600px] md:w-[750px] mt-3">
                                    <textarea class="form-control" placeholder="Alamat Rumah Lengkap*" name="cust_alamat" required></textarea>
                                </div>
                            @endif
                            @if ($head->with_customer_proty)
                                <div class="w-[320px] sm:w-[600px] md:w-[750px] mt-3">
                                    <select class="form-control @error('province') is-invalid @enderror" id="province"
                                        required name="cust_kecamatan">
                                        <option value="">Kecamatan, Kabupaten, Provinsi</option>
                                        @foreach ($district as $d)
                                            <option
                                                value="{{ $d->provinsi }}, {{ $d->kabupaten }}, {{ $d->distrik }}">
                                                {{ $d->provinsi }}, {{ $d->kabupaten }}, {{ $d->distrik }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        @endif

                        <div class="grid grid-cols-12 mt-3">
                            @foreach ($head->bump_products as $product)
                                <input type="hidden" name="bump_product_id" value="{{ $product->product->id }}">
                                <div class="col-span-12 flex justify-center gap-[10px]">
                                    <div class="pc-bump-editor w-[320px] sm:w-[600px] md:w-[750px] ">
                                        <div class="bump">
                                            <div class="bump__checkbox flex items-center">
                                                <div class="bump__arrow flex-grow-0">
                                                    <img src="https://bulala.id/assets/images/bump-arrow.gif">
                                                </div>
                                                <div class="flex-grow flex items-center">
                                                    <input type="checkbox" name="selected_product"
                                                        id="product_{{ $product->id }}" value="{{ $product->id }}"
                                                        readonly="" class="input-checkbosx">
                                                    <label for="product_{{ $product->id }}"
                                                        class="input-checkbox-text bumb-text pl-3 text-[18px] sm:text-[20px] md:text-[22px]">
                                                        {{ $product->custom_name ?? $product->product->name }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="bumb-text bump__title input-text">
                                                @if ($product->title)
                                                    {{ $product->title }}
                                                @endif
                                            </div>
                                            <div class="flex justify-center">
                                                @if ($product->custom_photo)
                                                    <img src="{{ asset($product->custom_photo) }}" class="py-3" />
                                                @else
                                                    @if (count($product->product->product_images))
                                                        <img src="{{ asset($product->product->product_images[0]->url) }}"
                                                            class="py-3" />
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="flex mt-3 justify-center text-lg font-bold text-red-500">
                                                @if ($product->discount)
                                                    @php
                                                        $originalPrice = $product->product->price;
                                                        $discountAmount = ($originalPrice * $product->discount) / 100;
                                                        $finalPrice = $originalPrice - $discountAmount;
                                                    @endphp
                                                    <div>
                                                        <span
                                                            class="line-through">{{ number_format($originalPrice, 2) }}</span>
                                                        <span>{{ number_format($finalPrice, 2) }}</span>
                                                        (-{{ $product->discount }}%)
                                                    </div>
                                                    <input type="hidden" name="bump_product_price"
                                                        value="{{ $finalPrice }}">
                                                @else
                                                    <div>
                                                        : {{ number_format($product->product->price, 2) }}
                                                    </div>
                                                    <input type="hidden" name="bump_product_price"
                                                        value="{{ $product->product->price }}">
                                                @endif
                                            </div>
                                            <div class="mt-3 bumb-text">
                                                @if ($product->description)
                                                    {{ $product->description }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit"
                            class="rounded-[10px] bg-orange-400 hover:bg-orange-500 py-3 btn-block w-full text-white text-[20px] font-bold">
                            {{ $head->text_submit_button }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/template/main/vendors/js/vendors.min.js"></script>
    <script src="/template/main/vendors/js/select2.min.js"></script>
    <script src="/template/main/vendors/js/select2-active.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#province').select2({
                theme: 'bootstrap-5',
            })

            document.addEventListener('DOMContentLoaded', function() {
                const radioButtons = document.querySelectorAll('input[name="selected_product"]');
                radioButtons.forEach(radio => {
                    radio.addEventListener('change', function() {
                        radioButtons.forEach(r => {
                            if (r !== radio) {
                                r.checked = false;
                            }
                        });
                    });
                });
            });
        });
    </script>
</body>

</html>
