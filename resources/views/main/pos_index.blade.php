@extends('master', [
    'use_tailwind' => true,
])
@section('style')
    <style>
        .selectedKategori {
            background-color: #c3e0ff;
            color: white;
        }

        .counter-btn {
            width: 30px;
            height: 30px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 50%;
            cursor: pointer;
        }

        .modal-backdrop {
            display: none;
        }

        .modal {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css"
        integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
@endsection
@section('content')
    <div id="app">

        <main class="nxl-container">
            <div class="nxl-content">
                <!-- [ page-header ] start -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">POS (Point of Sales)</li>
                            <li class="breadcrumb-item">Aplikasi Kasir Randu POS Versi Web</li>
                        </ul>
                    </div>
                    <div class="page-header-right ms-auto"
                        style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-top: 10px; margin-bottom: 10px;">
                        <button type="button" id="btn-close-shift" @click="methods.onCloseShiftClick"
                            class="btn btn-sm text-white"
                            style="background-color: #dc3545; border: none; font-size: 14px; padding: 10px 20px; border-radius: 6px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                            TUTUP SHIFT
                        </button>
                    </div>



                </div>
                <div class="main-content">
                    {{-- Content Header --}}
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <div class="col-span-12 xl:col-span-8">
                            <div class="flex bg-white gap-2 p-2 max-h-[65px] align-items-center">
                                <button type="button" class="btn btn-outline-info text-dark me-1 rounded-3"
                                    @click="methods.onShowingModalSelectTable">
                                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> <span class="fs-7">Pilih Meja</span>
                                </button>
                                <div class="input-group me-1">
                                    <select id="select2-pos" name="data" style="width: 100%">
                                        <option value="">Ketik Nama Konsumen lalu Tekan Enter...</option>
                                    </select>
                                </div>
                                <button type="button" @click="methods.onShowingAddNewCustomerModal"
                                    class="btn btn-outline-info text-dark rounded-3 me-1">
                                    <i class="bi bi-card-heading"></i>
                                </button>
                                <div class="border h-[43.1076px] w-[250px] rounded-3 flex items-center justify-center">
                                    <input :disabled="data.petty_cash" type="date" v-model="data.value.custom_date">
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 xl:col-span-4">
                            <div class="flex bg-white gap-2 p-2 max-h-[65px]">
                                <button type="button" @click="methods.onShowingShippingModal"
                                    class="flex-grow btn btn-outline-primary rounded-3 normal-case">
                                    <span class="mdi mdi-truck text-[18px] mr-2"></span> Tambahan / Ongkir
                                </button>
                                <button type="button" @click="methods.onShowingVoucherModal"
                                    class="flex-grow btn btn-outline-primary rounded-3 normal-case">
                                    <span class="mdi mdi-sale-outline text-[18px] mr-2"></span> Voucher / Diskon
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Body Content --}}
                    <div class="grid grid-cols-12 gap-2">
                        {{-- Pilih Kategori, List Product, Pagination --}}
                        <div class="relative col-span-12 xl:col-span-8 bg-white p-3 rounded w-full overflow-auto"
                            style="min-height: calc(100vh - 255px)">
                            {{-- Pilih Kategori --}}
                            <div class="container">
                                <div class="grid grid-cols-2">
                                    <div class="col-span-2 sm:col-span-1 mb-4 sm:mb-0 sm:my-auto">
                                        <div class="flex items-center">
                                            <h6 class="mb-0">Kategori</h6>
                                            <i class="bi bi-chevron-compact-right fw-bold ms-1"></i>
                                            <span type="button" class="p-1 text-primary"
                                                @click="methods.onShowingModalCategory" style="font-weight: bold">
                                                <span class="kategoriText fs-6">Pilih Kategori</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-span-2 sm:col-span-1 flex justify-end">
                                        <div
                                            class="relative max-h-[47px] max-w-[300px] w-full border flex items-center px-2 mr-2 rounded">
                                            <select name="" id="" class="w-full bg-transparent" xxx
                                                v-model="data.price_type" @change="methods.onChangePriceType">
                                                <option value="price" selected>Default (Dine In)</option>
                                                <option value="price_ta">Take Away</option>
                                                <option value="price_mp">Marketplace (Shopee, GoFood, Tokopedia, Etc)
                                                </option>
                                                <option value="price_cus">Custom Price</option>
                                            </select>
                                        </div>
                                        <div class="relative max-w-[300px] w-full">
                                            <i class="bi bi-search absolute left-[14px] top-[14px]"></i>
                                            <input ref="searchInput" type="text"
                                                class="form-control form-control-sm w-full pl-[40px]"
                                                placeholder="Cari nama produk/layanan disini" v-model="data.searchQuery"
                                                @input="methods.onSearchInput" @blur="methods.unfocusSearchInput" />
                                            <small>Tekan CTRL + B untuk mode barcode</small>
                                        </div>
                                        <button type="button" @click="methods.focusSearchInput"
                                            class="rounded min-w-[47px] h-[47px] ml-2 flex items-center justify-center"
                                            :class="data.bg_barcode">
                                            <i class="bi bi-upc-scan text-white text-xl"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- List Product --}}
                            <div class="container product-content hidden">
                                <div class="grid grid-cols-12 mt-4 gap-2 products">
                                    <div v-for="(item) in data.products" :key="item.id"
                                        :class="{
                                            'col-span-6 md:col-span-4 xl:col-span-3': true,
                                            'hidden': item
                                                .buffered_stock && !item.qty_allowed_to_sell
                                        }">
                                        <div @click="methods.onSelectProduct(item)"
                                            class="bg-gradient-to-t from-sky-500 to-[#2F467A] rounded-3 text-center border rounded-3 border-info d-flex flex-column justify-content-center cursor-pointer h-[180px] hover:outline-red-400 hover:outline-2 hover:outline">
                                            <h6 class="text-white mb-2 text-lg font-bold">@{{ item.name }}</h6>
                                            <h6 class="mb-0 text-lg text-yellow-200">Rp @{{ new Intl.NumberFormat().format(item.price) }}</h6>
                                            <div v-if="item.variant.length" class="text-emerald-300 mt-2 font-bold">
                                                (Tersedia Variant)
                                            </div>
                                            <div v-if="item.buffered_stock" class="text-white mt-2">
                                                Stock @{{ item.qty_allowed_to_sell }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="absolute bottom-[24px] right-[24px] checkout-section hidden">
                                <nav aria-label="Page navigation example" v-if="data.pagination.total_pages > 1">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item" :class="{ disabled: data.pagination.current_page === 1 }">
                                            <a class="page-link" href="#"
                                                @click.prevent="methods.changePage(data.pagination.current_page - 1)">Previous</a>
                                        </li>
                                        <li class="page-item" v-for="page in data.pagination.total_pages"
                                            :key="page"
                                            :class="{ active: page === data.pagination.current_page }">
                                            <a class="page-link" href="#"
                                                @click.prevent="methods.changePage(page)">@{{ page }}</a>
                                        </li>
                                        <li class="page-item"
                                            :class="{ disabled: data.pagination.current_page === data.pagination.total_pages }">
                                            <a class="page-link" href="#"
                                                @click.prevent="methods.changePage(data.pagination.current_page + 1)">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        {{-- Kotak Detail Checkout --}}
                        <div class="col-span-12 xl:col-span-4 w-full p-0">
                            <div class="relative bg-white rounded-3" style="height: calc(100vh - 325px)">
                                {{-- Informasi Meja --}}
                                <div class="checkout-section hidden">
                                    <div v-if="data.value.table" class="p-3">
                                        <div class="w-100 px-2 rounded-3 border-2"
                                            style="background-color: rgb(233, 233, 233)">
                                            <div class="d-flex align-items-center">
                                                <div class="m-0 ms-2 gap-[15px] flex items-center">
                                                    <spa class="mdi mdi-check-circle text-success text-[25px]"></spa>
                                                    <div class="font-semibold text-md">
                                                        Meja @{{ data.value.table.no_meja }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 checkout-section hidden px-3 overflow-y-auto"
                                    :style="{ height: `calc(100vh - ${!data.value.table ? '525px' : '550px'})` }">
                                    {{-- Tombol Bersihkan Product Checkout --}}
                                    <div v-if="data.value.products.length"
                                        :class="{ 'flex justify-end mb-2': true, 'pt-3': !data.value.table }">
                                        <div class="text-red-500 font-medium -tracking-[2%] text-[14px] cursor-pointer"
                                            @click="methods.onRemoveAllItems">
                                            <span class="mdi mdi-trash-can-outline"></span> Bersihkan Semua
                                        </div>
                                    </div>
                                    <div v-for="(pro, index) in data.value.products">
                                        <div v-if="index" class="border-b w-full my-4"></div>
                                        {{-- Detail Produk --}}
                                        <div class="flex items-center gap-[10px]">
                                            <div class="flex-grow-0">
                                                <img :src="pro.image_url || '/template/main/images/product-placeholder.png'"
                                                    class="w-[40px] h-[40px] rounded-[8px]" />
                                            </div>
                                            <div class="flex-grow">
                                                <div class="text-[16px] font-semibold -mt-[3px]">@{{ pro.name }}
                                                </div>
                                                <div class="text-[12px] font-normal text-orange-500">
                                                    <span class="mdi mdi-tag"></span> @{{ pro.category_name }}
                                                </div>
                                                <div v-if="pro.note" class="text-sm text-gray-500 italic">
                                                    Catatan: @{{ pro.note }}
                                                </div>
                                            </div>
                                            <div v-if="!pro.is_editable"
                                                class="flex-grow-0 font-bold text-[14px] -tracking-[2%] text-gray-700">
                                                Rp @{{ new Intl.NumberFormat().format(pro.price) }}
                                            </div>
                                            <input v-if="pro.is_editable" type="text"
                                                class="w-[80px] text-center border border-gray-300 rounded-[8px] text-[14px] font-bold h-[32px]"
                                                v-model="pro.price" @input="methods.onChangePricePerItem(pro)" />
                                        </div>
                                        {{-- Tombol Action Product Checkout --}}
                                        <div class="flex justify-end gap-[20px] mt-2">
                                            {{-- Tombol Edit Note --}}
                                            <div @click="methods.onShowingAddProductNoteFromCheckout(pro)"
                                                class="text-gray-500 text-[23px] cursor-pointer">
                                                <span class="mdi mdi-note-edit"></span>
                                            </div>
                                            {{-- Tombol Hapus Produk --}}
                                            <div class="text-gray-500 text-[23px] cursor-pointer"
                                                @click="methods.onRemoveItem(pro)">
                                                <span class="mdi mdi-trash-can-outline"></span>
                                            </div>
                                            {{-- Tombol Ubah Quantity --}}
                                            <div class="border w-[100px] h-[32px] grid grid-cols-3 rounded-[8px]">
                                                <div @click="methods.changeQuantityPerItem('-', pro)"
                                                    :class="{
                                                        'col-span-1 flex items-center justify-center text-[18px] cursor-pointer': true,
                                                        'text-gray-100': pro.quantity < 2,
                                                        'text-blue-400': pro.quantity
                                                    }">
                                                    <span class="mdi mdi-minus"></span>
                                                </div>
                                                <div
                                                    class="col-span-1 border flex items-center justify-center text-[12px]">
                                                    <input type="text" class="w-auto max-w-[30px] text-center"
                                                        v-model="pro.quantity" pattern="\d*"
                                                        @focus="methods.onFocusPerItem(pro)"
                                                        @input="methods.changeQuantityPerItem('?', pro)" />
                                                </div>
                                                <div @click="methods.changeQuantityPerItem('+', pro)"
                                                    :class="{
                                                        'col-span-1 flex items-center justify-center text-[18px] cursor-pointer': true,
                                                        'text-blue-400': !pro.buffered_stock || (pro.buffered_stock &&
                                                            pro.quantity < pro.qty_allowed_to_sell),
                                                        'text-gray-100': pro.buffered_stock && pro.quantity === pro
                                                            .qty_allowed_to_sell
                                                    }">
                                                    <span class="mdi mdi-plus"></span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- List Varian --}}
                                        <div class="mt-2">
                                            <div v-for="variant in pro.variant"
                                                :class="{
                                                    'flex gap-[10px] pl-[70px] pr-[0px]': true,
                                                    'hidden': !variant
                                                        .quantity
                                                }">
                                                <div class="flex-grow-0 min-w-[25px]">
                                                    @{{ variant.quantity }}x
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="text-md font-medium text-cyan-500">
                                                        @{{ variant.varian_group }} @{{ variant.varian_name }}
                                                    </div>
                                                    <div v-if="variant.note" class="text-xs text-gray-500 italic">
                                                        Catatan: @{{ variant.note }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-0 text-md font-medium text-cyan-500">
                                                    Rp @{{ new Intl.NumberFormat().format(variant.varian_price * variant.quantity) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Rincia Pembayaran --}}
                                <div class="absolute bottom-0 w-full border-t-2 border-dashed p-3 checkout-section hidden">
                                    <div class="d-flex justify-content-between align-items-center kuantitas">
                                        <span class="fs-6 font-medium">Kuantitas</span>
                                        <span class="fs-6 total-kuantitas">@{{ data.value.quantity }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center subtotal mt-1">
                                        <span class="fs-6 font-medium">Subtotal</span>
                                        <span class="fs-6 total-subtotal">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center subtotal mt-1">
                                        <span class="fs-6 font-medium">Pajak</span>
                                        <span class="fs-6 total-tax">@{{ methods.calcTax() }}</span>
                                    </div>
                                    <div v-if="data.value.shipping"
                                        class="d-flex justify-content-between align-items-center subtotal mt-1">
                                        <span class="fs-6 font-medium">Tambahan / Ongkir</span>
                                        <span class="fs-6 total-shipping">Rp 0</span>
                                    </div>
                                    <div v-if="data.value.voucher"
                                        class="d-flex justify-content-between align-items-center subtotal mt-1">
                                        <div class="fs-6 font-medium">Potongan Voucher</div>
                                        <div class="fs-6 total-voucher text-green-500">
                                            @{{ methods.onCalcTotalVoucher() }}
                                            <span v-if="data.value.tempVoucherPercent" class="text-orange-600">
                                                (@{{ `${data.value.tempVoucherPercent}%` }})
                                            </span>
                                        </div>
                                    </div>
                                    <div v-if="data.value.selisihPembulatan"
                                        class="d-flex justify-content-between align-items-center subtotal mt-1">
                                        <div class="fs-6 font-medium">Pembulatan</div>
                                        <div class="fs-6 total-voucher">
                                            @{{ data.value.selisihPembulatan }}
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center subtotal mt-1">
                                        <span class="fs-6 font-medium text-yellow-500">Total</span>
                                        <span class="fs-6 total-transaction text-yellow-500">
                                            @{{ methods.onCalcTotalTransaction() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="d-flex justify-content-center w-full p-2 bg-white rounded align-items-center text-center mt-2">
                                <button type="button" :disabled="!data.value.quantity || data.paymentProgress"
                                    @click="methods.onPayment" class="btn w-100 p-3 text-white bayar"
                                    style="background-color: #EB7302">Bayar | <span
                                        class="total-bayar-button mx-2">@{{ methods.onCalcTotalTransaction() }}</span>
                                    <i v-if="data.paymentProgress" class="fas fa-spinner fa-spin ms-2"></i><i v-else
                                        class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div>
            {{-- Modal Open Shift --}}
            <div class="modal fade" data-bs-backdrop="static" id="modalOpenShift" tabindex="-1"
                aria-labelledby="modalOpenShiftLabel" aria-hidden="true">
                <form @submit.prevent="methods.onSubmitOpenShiftForm" id="form-proses-open-shift"
                    class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-body w-full">
                            <div class="mb-4">
                                <div class="fw-bold" style="font-size: 13px; color: #375ed1">
                                    <span style="font-size: 15px" class="mdi mdi-account"></span> Kasir
                                </div>
                                <div class="shift-open-kasir">
                                    {{ $user->fullname }}
                                </div>
                            </div>
                            <div class="w-full mb-4 text-white rounded mb-4">
                                <label for="nominal" class="fw-bold" style="font-size: 13px; color: #375ed1">
                                    <span style="font-size: 15px" class="mdi mdi-wallet"></span> Kas Awal
                                </label>
                                <input type="text" class="form-control" id="shift-open-kas-awal" required
                                    placeholder="Masukan Kas Awal">
                            </div>
                            <div class="d-flex justify-content-end">
                                {{-- <button type="button" class="btn btn-outline-danger p-3 me-3"
                        data-bs-dismiss="modal">Batal</button> --}}
                                <button type="submit" class="btn btn-primary p-3">Proses</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Modal Category --}}
            <div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalKategoriLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pilih Kategori Product/Layanan</h5>
                        </div>
                        <div class="modal-body w-full">
                            <select class="category-products" name="category">
                                <option value="">Pilih Kategori</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Select Table --}}
            <div class="modal fade" id="modalSelectTable" tabindex="-1" aria-labelledby="modalSelectTableLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body w-full">
                            <div class="w-full bg-primary text-white p-3 rounded d-flex align-items-center mb-4">
                                <h5 class="text-white m-0"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Pilih Meja</h5>
                            </div>
                            {{-- <h5 class="mb-4"><i class="bi bi-people-fill me-2"></i>Jumlah Meja</h5> --}}
                            <div class="d-flex justify-content-center align-items-center selected mb-3"></div>
                            <div class="row mb-5">
                                <div class="col-12 mb-5">
                                    <input type="text" id="input-search-meja" class="form-control"
                                        placeholder="Cari Meja" />
                                </div>
                                <div v-for="(item, index) in data.tables" class="col-lg-2 col-md-2 col-sm-3 mb-2"
                                    style="cursor: pointer" @click="methods.onSelectTable(item)">
                                    <div class="border p-2 text-center border-info rounded">
                                        <h3 class="m-0">@{{ item.no_meja }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger p-3 me-3"
                                    data-bs-dismiss="modal">Batal</button>
                                <button @click="methods.onResetTable" type="button"
                                    class="btn btn-outline-primary p-3 me-3">
                                    Reset
                                </button>
                                <button type="button" class="btn btn-secondary p-3 btn-proses" disabled>Proses</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Edit Variant --}}
            <div v-if="data.modal.product" class="modal fade" id="modalVarian" tabindex="-1"
                aria-labelledby="modalVarianLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-body w-full">
                            <div class="flex justify-between items-center bg-secondary rounded-3 p-3 mb-3">
                                <div class="flex items-center">
                                    <img :src="data.modal.product.image_url ? data.modal.product.image_url :
                                        '/storefront/varian_img.png'"
                                        alt="" class="w-[70px] h-[50px] object-cover">
                                    <div class="ms-3 d-flex flex-column">
                                        <span class="text-white fs-6 fw-bold variant-product-name">
                                            @{{ data.modal.product.name }}
                                        </span>
                                        <span class="fs-6 fw-bold text-warning variant-product-price">
                                            Rp @{{ new Intl.NumberFormat().format(data.modal.product.price) }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="methods.onAddNoteForProduct"
                                    class="rounded-[10px] h-[55px] bg-white text-black flex items-center justify-center text-lg px-4">
                                    <i class="bi bi-plus me-1"></i> Catatan <i class="bi bi-pencil-square ms-2"></i>
                                </button>
                            </div>
                            <div class="container">
                                <div class="row mb-3">
                                    <div class="col px-0 flex items-center gap-1">
                                        <div :class="{
                                            'p-3 bg-blue-100 rounded-3 fw-bold me-2 text-center border-2': true,
                                            'border-blue-400': data
                                                .modal.variantGroupActive.length === data.modal.product.variant_groups
                                                .length,
                                            'border-blue-100': data
                                                .modal.variantGroupActive.length !== data.modal.product.variant_groups
                                                .length
                                        }"
                                            style="color: #1f84f0; cursor: pointer;"
                                            @click="() => (data.modal.variantGroupActive = data.modal.product.variant_groups)">
                                            Tampilkan Semua
                                        </div>
                                        <div v-for="item in data.modal.product.variant_groups"
                                            :class="{
                                                'p-3 bg-blue-100 rounded-3 fw-bold me-2 text-center border-2': true,
                                                'border-blue-400': data
                                                    .modal.variantGroupActive.length === 1 && data.modal
                                                    .variantGroupActive
                                                    .includes(item),
                                                'border-blue-100': data
                                                    .modal.variantGroupActive.length === 1 && !data.modal
                                                    .variantGroupActive
                                                    .includes(item)
                                            }"
                                            style="color: #1f84f0; cursor: pointer;"
                                            @click="() => (data.modal.variantGroupActive = [item])">
                                            @{{ item }}
                                        </div>
                                    </div>
                                    <div class="col px-0 col-md-3 col-sm-12 d-flex justif-content-end">
                                        <div class="relative w-100 px-1">
                                            <span class="absolute left-[17px] top-[13px]">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" v-model="data.modal.search"
                                                @change="methods.onSearchVariant"
                                                class="form-control border border-info pl-[40px]" placeholder="Cari">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 rounded-3 mb-5" style="background-color: #eee">
                                <div class="container px-1">
                                    <div v-if="data.modal.product.variant.length && data.modal.variantGroupActive.length"
                                        class="grid grid-cols-4 gap-2 varian overflow-y-auto max-h-[250px] pr-2">
                                        <div v-for="item in data.modal.product.variant"
                                            :class="{
                                                'col-span-4 md:col-span-2 xl:col-span-1 m-0 p-0': true,
                                                'hidden': !data.modal.variantGroupActive.includes(item.varian_group) ||
                                                    (data.modal.search &&
                                                        (item.varian_group.toLowerCase() + ' ' + item.varian_name
                                                            .toLowerCase()).indexOf(data.modal.search.toLowerCase()) ===
                                                        -1)
                                            }">
                                            <div class="card m-0">
                                                <div class="card-body">
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="text-dark">
                                                            @{{ item.varian_group }} @{{ item.varian_name }}
                                                        </h4>
                                                        <p class="card-text font-bold">( + Rp @{{ new Intl.NumberFormat().format(item.varian_price) }} )</p>
                                                    </div>
                                                    <div class="flex justify-between mt-4">
                                                        <button type="button" @click="methods.addNoteForVariant(item)"
                                                            class="bg-orange-50 text-orange-400 px-2 py-2 rounded-lg cursor-pointer">
                                                            + Catatan
                                                        </button>
                                                        <div class="flex items-center">
                                                            <div @click="() => {
                                                            if (item.quantity > 0) {
                                                                methods.onChangeVariantQuantity(item, '-')
                                                            }    
                                                        }"
                                                                :class="{
                                                                    'text-xl cursor-pointer': true,
                                                                    'text-gray-100': !item.quantity,
                                                                    'text-blue-400': item.quantity
                                                                }">
                                                                <span class="mdi mdi-minus-circle"></span>
                                                            </div>
                                                            <input type="number" v-model.number="item.quantity"
                                                                @input="methods.onChangeVariantQuantity(item, '?')"
                                                                min="0"
                                                                :max="item.max_quantity !== 0 ? item.max_quantity : null"
                                                                class="border-b text-center w-[60px]" />
                                                            <div @click="methods.onChangeVariantQuantity(item, '+')"
                                                                :class="{
                                                                    'text-xl cursor-pointer': true,
                                                                    'text-blue-400': item.quantity < item
                                                                        .max_quantity || item.max_quantity === 0,
                                                                    'text-gray-100': item.quantity >= item
                                                                        .max_quantity && item.max_quantity !== 0
                                                                }">
                                                                <span class="mdi mdi-plus-circle"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Card Footer --}}
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="flex items-center">
                                    <div class="font-bold mr-3 text-md text-black">Jumlah</div>
                                    <div @click="!data.modal.product.variant.some(v => v.quantity > 0) ? methods.changeQuantityPerItemOnModalVariant('-') : null"
                                        :class="{
                                            'text-xl cursor-pointer': true,
                                            'text-gray-100': !data.modal.product.quantity || data.modal.product.variant.some(v => v.quantity > 0),
                                            'text-blue-400': data.modal.product.quantity && !data.modal.product.variant.some(v => v.quantity > 0),
                                            'cursor-not-allowed': data.modal.product.variant.some(v => v.quantity > 0)
                                        }">
                                        <span class="mdi mdi-minus-circle"></span>
                                    </div>
                                    <input type="text" :value="data.modal.product.quantity" readonly
                                        :class="{
                                            'border-b text-center w-[60px]': true,
                                            'opacity-50': data.modal.product.variant.some(v => v.quantity > 0)
                                        }" />
                                    <div @click="!data.modal.product.variant.some(v => v.quantity > 0) ? methods.changeQuantityPerItemOnModalVariant('+') : null"
                                        :class="{
                                            'text-xl cursor-pointer': true,
                                            'text-blue-400': !data.modal.product.variant.some(v => v.quantity > 0),
                                            'text-gray-100': data.modal.product.variant.some(v => v.quantity > 0),
                                            'cursor-not-allowed': data.modal.product.variant.some(v => v.quantity > 0)
                                        }">
                                        <span class="mdi mdi-plus-circle"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center ms-auto">
                                    <button type="button" class="btn btn-outline-danger p-3 me-3"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" @click="methods.onProsesModalVarian"
                                        class="btn btn-success p-3 btn-proses-varian">Proses</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Add Note For Variant --}}
            <div v-if="data.modal.product && data.modal.variantActive" class="modal fade" id="addNoteForVariantModal"
                tabindex="-1">
                <form @submit.prevent="methods.onSubmitVariantAddNote" class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">
                                @{{ data.modal.product.name }} | @{{ data.modal.variantActive.varian_name }}
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <textarea class="form-control" id="variant-note" placeholder="Contoh catatan: Pedas level 5"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Proses</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Add Note For Product --}}
            <div v-if="data.modal.product" class="modal fade" id="addNoteForProductModal" tabindex="-1">
                <form @submit.prevent="methods.onSubmitProductAddNote" class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">
                                @{{ data.modal.product.name }}
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <textarea class="form-control" id="product-note" placeholder="Contoh catatan: Pedas level 5"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Proses</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Shipping Modal --}}
            <div class="modal fade" id="shippingModal" tabindex="-1">
                <form @submit.prevent="methods.onSubmitShippingCost" class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">
                                Tambahan / Ongkir
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="text" required class="form-control" id="data-value-shipping"
                                    placeholder="Masukan nominal biaya kirim" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Add New Customer Modal --}}
            <div class="modal fade" id="addNewCustomer" tabindex="-1">
                <form @submit.prevent="methods.onSubmitAddNewCustomer" class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">
                                Tambah Pelanggan Baru
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="grid grid-cols-12 gap-[6px]">
                                <div class="col-span-4 mb-3">
                                    <label for="customer-name" class="col-form-label">Nama Pelanggan Baru:<span
                                            class="text-red-500">*</span></label>
                                    <input required type="text" class="form-control" id="customer-name">
                                </div>
                                <div class="col-span-4 mb-3">
                                    <label for="customer-phone" class="col-form-label">Nomor Telpon:<span
                                            class="text-red-500">*</span></label>
                                    <input required type="number" class="form-control" id="customer-phone">
                                </div>
                                <div class="col-span-4 mb-3">
                                    <label for="customer-email" class="col-form-label">Alamat Email (Opsional):</label>
                                    <input type="email" class="form-control" id="customer-email">
                                </div>
                                <div class="col-span-4 mb-3">
                                    <label for="customer-province" class="col-form-label">Provinsi:</label>
                                    <select id="customer-province" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-span-4 mb-3">
                                    <label for="customer-city" class="col-form-label">Kota:</label>
                                    <select id="customer-city" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-span-4 mb-3">
                                    <label for="customer-district" class="col-form-label">Kecamatan:</label>
                                    <select id="customer-district" class="form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-span-12 mb-3">
                                    <label for="customer-address" class="col-form-label">Alamat Rumah (Opsional):</label>
                                    <textarea class="form-control" id="customer-address"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Voucher Modal --}}
            <div class="modal fade" id="addVoucher">
                <form @submit.prevent="methods.onSubmitVoucher" class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">
                                Pilih Voucher
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="select-voucher" class="col-form-label">Pilih Voucher:</label>
                                <select class="form-control" id="select-voucher">
                                    <option value="">Pilih Voucher</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="custom-voucher" class="col-form-label">Custom Voucher:</label>
                                <input v-model="data.value.customVoucherBeforeSubmit" type="text" class="form-control"
                                    id="custom-voucher">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger"
                                @click="methods.cancelAddVoucher">Batal</button>
                            <button v-if="data.value.tempVoucherId !== null || data.value.customVoucher !== null"
                                type="button" class="btn btn-warning" @click="methods.onResetAddVoucher">
                                Setel Ulang
                            </button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        const tax = {{ ($dataUser->role_code === 'staff' ? $tax : $dataUser->tax) ?? 0 }}

        var apiResults = [];
        $(document).ready(function() {
            $('#select2-pos').select2({
                theme: 'bootstrap-5',
                tags: true,
                ajax: {
                    url: '/v1/customer',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // search term
                        };
                    },
                    processResults: function(data) {
                        apiResults = data.data.data.map(function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            };
                        });

                        return {
                            results: apiResults
                        };
                    },
                    cache: false
                },
            })
        })

        function convertCurrencyToFloat(currencyStr) {
            if (!currencyStr) {
                return 0;
            }

            // Menghilangkan semua koma dari string
            const cleanedStr = currencyStr.replace(/,/g, '');

            // Mengonversi string yang sudah dibersihkan menjadi float
            const currencyFloat = parseFloat(cleanedStr);

            return currencyFloat;
        }

        function generateRandomString(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            const charactersLength = characters.length;

            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }

            return result;
        }

        const petty_cash = {{ $user->petty_cash === 1 ? 'true' : 'false' }}

        createApp({
            setup() {
                const searchInput = ref()
                const data = reactive({
                    bg_barcode: 'bg-gray-200',
                    petty_cash: petty_cash,
                    price_type: 'price',
                    tables: [],
                    products: [],
                    paymentProgress: false,
                    is_rounded: 0,
                    category_id: null,
                    pagination: {
                        current_page: 1,
                        total_pages: 1,
                        per_page: 10,
                        total_items: 0,
                    },
                    value: {
                        selisihPembulatan: 0,
                        customer: null,
                        tax: 0,
                        variantUpdated: false,
                        table: null,
                        products: [],
                        quantity: 0, // jumlah product ketika di tambahkan
                        subtotal: 0,
                        shipping: 0,
                        custom_date: null,
                        customVoucher: null,
                        customVoucherBeforeSubmit: null,
                        tempVoucher: null,
                        tempVoucherBeforeSubmit: null,
                        tempVoucherId: null,
                        tempVoucherPercent: null,
                        tempVoucherPercentToNominal: 0, // hasil akhir dari kalkulasi diskon tipe percent
                        tempVoucherBackup: [],
                    },
                    temps: {
                        scanMode: false,
                        quantityFocus: 0, // jumlah product ketika di klik (focusing)
                        addNewCust: {
                            prov_id: null,
                            city_id: null,
                            dist_id: null
                        }
                    },
                    modal: {
                        search: null,
                        product: null,
                        variantGroupActive: [], // tempat simpan sementara ketika user memiliki varian group
                        variantActive: null, // aktif ketika menambahkan catatan pada varian yang di mau di edit data nya
                    }
                })

                const sentinel = ref(null);

                const needOpenShift = ref(false)
                let searchTimeout = null;
                const methods = {
                    cancelAddVoucher: () => {
                        if (data.value.tempVoucherId !== null) {
                            $('#select-voucher').val(data.value.tempVoucherId).trigger('change');
                            $('#custom-voucher').attr('disabled', true)
                            data.value.tempVoucherBeforeSubmit = null
                        } else if (data.value.customVoucher !== null) {
                            data.value.customVoucherBeforeSubmit = data.value.customVoucher;
                            $('#custom-voucher').attr('disabled', false)
                        } else {
                            $('#select-voucher').val(null).trigger('change')
                            data.value.customVoucherBeforeSubmit = null;
                            $('#custom-voucher').attr('disabled', false)
                        }

                        $('#addVoucher').modal('hide');
                    },
                    focusSearchInput() {
                        data.temps.scanMode = true
                        searchInput.value.focus();
                        data.bg_barcode = 'bg-blue-500 text-white'
                    },
                    unfocusSearchInput() {
                        searchInput.value.blur(); // Menghilangkan fokus dari input
                        data.bg_barcode = 'bg-gray-200 text-black'
                    },
                    handleKeyDown(event) {
                        // Check for Ctrl+B combination
                        if (event.key === 'b' && event.ctrlKey) {
                            event.preventDefault(); // Prevent default browser actions
                            setTimeout(() => {
                                methods.focusSearchInput();
                            }, 500)
                        }

                        // Memeriksa apakah tombol Esc ditekan
                        if (event.key === 'Escape') {
                            data.temps.scanMode = false
                            methods.unfocusSearchInput();
                        }
                    },
                    onResetAddVoucher: () => {
                        $('#select-voucher').val(null).trigger('change')
                        data.value.tempVoucher = null;
                        data.value.tempVoucherBeforeSubmit = null;
                        data.value.tempVoucherId = null;
                        data.value.tempVoucherPercent = null;
                        data.value.tempVoucherPercentToNominal = null
                        $('#custom-voucher').attr('disabled', false)
                        // $('#addVoucher').modal('hide');
                        data.value.customVoucher = null
                        data.value.customVoucherBeforeSubmit = null
                    },
                    onShowingVoucherModal: () => {
                        $('#addVoucher').modal('show')
                        const myModalEl = document.getElementById('addVoucher')

                        myModalEl.addEventListener('hidden.bs.modal', event => {
                            // $('#select-voucher').val(null).trigger('change');
                            $('#custom-voucher').attr('disabled', false)
                        })

                        if (data.value.tempVoucherId) {
                            $('#select-voucher').val(data.value.tempVoucherId).trigger('change');
                        }

                        $('#select-voucher').select2({
                            theme: 'bootstrap-5',
                            ajax: {
                                url: '/v1/voucher-web',
                                dataType: 'json',
                                data: function() {
                                    return {
                                        subtotal: data.value.subtotal // Tambahkan subtotal
                                    };
                                },
                                processResults: function(datax) {
                                    data.value.tempVoucherBackup.length = 0
                                    apiResults = datax.data.data.map(function(item) {
                                        return {
                                            text: item.name,
                                            id: item.id,
                                            value: item.value,
                                            type: item.type
                                        };
                                    });
                                    data.value.tempVoucherBackup = apiResults

                                    return {
                                        results: apiResults
                                    };
                                },
                                cache: false
                            }
                        }).on('change', function() {
                            // const selectedCategory = $(this).val();
                            const selectedData = $(this).select2('data');
                            if (selectedData.length > 0) {
                                data.value.tempVoucherBeforeSubmit = data.value.tempVoucherBackup
                                    .filter((item) => item.id == selectedData[0].id)[0]

                                data.value.customVoucherBeforeSubmit = null;

                                $('#custom-voucher').attr('disabled', true)
                            }
                        });
                    },
                    onSubmitVoucher: () => {
                        data.value.tempVoucher = data.value.tempVoucherBeforeSubmit
                        const manual = convertCurrencyToFloat(data.value.customVoucherBeforeSubmit || '')
                        if (data.value.tempVoucher) {
                            data.value.customVoucherBeforeSubmit = null
                            data.value.customVoucher = null
                            let tempPrice = 0
                            let tempId = 0
                            data.value.tempVoucherId = data.value.tempVoucher.id;

                            if (data.value.tempVoucher.type === 'persen') {
                                data.value.tempVoucherPercent = data.value.tempVoucher.value
                            } else {
                                tempPrice = data.value.tempVoucher.value
                                data.value.tempVoucherPercent = null
                            }

                            data.value.voucher = {
                                "selected": false,
                                "disabled": false,
                                "text": data.value.tempVoucher.text,
                                "id": data.value.tempVoucher.id,
                                "price": tempPrice,
                            }
                            // data.value.voucher = JSON.parse(JSON.stringify(data.value.tempVoucher))
                        } else if (manual) {
                            data.value.customVoucher = data.value.customVoucherBeforeSubmit
                            data.value.voucher = {
                                "selected": false,
                                "disabled": false,
                                "text": "Custom Voucher",
                                "id": 0,
                                "price": manual,
                            }
                        }

                        $('#addVoucher').modal('hide')
                    },
                    onCalcTotalTransaction: (withRp = true) => {
                        function roundToNearestThousand(num) {
                            if (!data.is_rounded) {
                                return num
                            }

                            // Ambil sisa pembagian dengan 1000
                            let remainder = num % 1000;

                            // Jika sisanya lebih besar dari atau sama dengan 500, kita bulatkan ke atas
                            if (remainder >= 500) {
                                return num + (1000 - remainder);
                            }
                            // Jika kurang dari 500, kita bulatkan ke bawah
                            else {
                                return num - remainder;
                            }
                        }

                        // const total = new Intl.NumberFormat().format(data.value.subtotal + data.value.shipping + data.value.tax)
                        const {
                            is_rounded
                        } = data
                        let total = data.value.subtotal + data.value.shipping + data.value.tax
                        if (data.value.voucher) {
                            let totalDiscount = 0
                            const manual = convertCurrencyToFloat(data.value.customVoucher || '')

                            if (manual) {
                                totalDiscount = manual * 1
                            } else if (data.value.tempVoucher) {
                                if (data.value.tempVoucher.type === 'persen') {
                                    let subtotal = data.value.subtotal
                                    if (subtotal) {
                                        let discount = data.value.subtotal * (data.value
                                            .tempVoucherPercent / 100)
                                        totalDiscount = discount
                                    }
                                } else {
                                    totalDiscount = data.value.voucher.price
                                }
                            }

                            data.value.tempVoucherPercentToNominal = totalDiscount

                            if (total > totalDiscount) {
                                const fixAmount = total - totalDiscount
                                data.value.selisihPembulatan = `Rp ${new Intl.NumberFormat().format(fixAmount - roundToNearestThousand(fixAmount))}`
                                return withRp ?
                                    `Rp ${new Intl.NumberFormat().format(roundToNearestThousand(fixAmount))}` :
                                    roundToNearestThousand(fixAmount)
                            } else {
                                return withRp ? 'Rp 0' : 0
                            }
                        } else {
                            data.value.selisihPembulatan = `Rp ${new Intl.NumberFormat().format(total - roundToNearestThousand(total))}`
                            return withRp ?
                                `Rp ${new Intl.NumberFormat().format(roundToNearestThousand(total))}` :
                                roundToNearestThousand(total)
                        }
                    },
                    onPayment: () => {
                        // if (!data.value.customer) {
                        //     notyf.open({
                        //         type: 'warning',
                        //         message: 'Bidang customer wajib di isi'
                        //     });
                        //     return false
                        // }

                        const temp = {
                            customer: data.value.customer ?? 'Walk In Customer',
                            paid: methods.onCalcTotalTransaction(false),
                            order_total: data.value.subtotal,
                            tax: methods.calcTax(false),
                            payment_method: null,
                            price_type: data.price_type,
                            qr_code_id: data.value.table?.id || null,
                            branch_id: {{ $branch_id ?? 'null' }},
                            staff_id: {{ $staff_id ?? 'null' }},
                            shipping: data.value.shipping,
                            diskon: data.value.tempVoucherPercentToNominal !== 0 ? data.value
                                .tempVoucherPercentToNominal : (data.value.voucher?.price || 0),
                            voucher_id: data.value.tempVoucherId || null,
                            product: data.value.products,
                            return_url: '/pos/terima-kasih',
                            custom_date: data.value.custom_date
                        }

                        data.paymentProgress = true
                        axios({
                            method: 'POST',
                            url: '/v1/payment-validation',
                            data: temp
                        }).then((res) => {
                            data.paymentProgress = false
                            if (res.data.status) {
                                const tempString = JSON.stringify(temp);
                                localStorage.setItem('pos', tempString);
                                window.location.href = '/pos/metode-pembayaran'
                            } else {
                                res.data.message.forEach((msg) => {
                                    notyf.open({
                                        type: 'error',
                                        message: msg
                                    });
                                })
                            }
                        }).catch((error) => {
                            data.paymentProgress = false
                            if (error.response && error.response.data && error.response.data
                                .message) {
                                // Handle structured error responses
                                if (Array.isArray(error.response.data.message)) {
                                    error.response.data.message.forEach((msg) => {
                                        notyf.open({
                                            type: 'error',
                                            message: msg
                                        });
                                    });
                                } else {
                                    notyf.open({
                                        type: 'error',
                                        message: error.response.data.message
                                    });
                                }
                            } else {
                                // Handle network errors or other unexpected errors
                                notyf.open({
                                    type: 'error',
                                    message: 'Terjadi kesalahan saat memproses pembayaran. Silahkan coba lagi.'
                                });
                                console.error('Payment validation error:', error);
                            }
                        })
                    },
                    onCalcTotalVoucher: () => {
                        const manual = convertCurrencyToFloat(data.value.customVoucher || '')
                        if (manual) {
                            let subtotal = data.value.subtotal
                            if (subtotal) {
                                return `- Rp ${new Intl.NumberFormat().format(manual * 1)}`
                            } else {
                                return 'Rp 0'
                            }
                        } else if (data.value.tempVoucher) {
                            if (data.value.tempVoucher.type === 'persen') {
                                let subtotal = data.value.subtotal
                                if (subtotal) {
                                    let discount = data.value.subtotal * (data.value.tempVoucherPercent /
                                        100)
                                    return `- Rp ${new Intl.NumberFormat().format(discount)}`
                                } else {
                                    return 'Rp 0'
                                }
                            } else {
                                let totalDiscount = new Intl.NumberFormat().format(data.value.voucher.price)
                                return `- Rp ${totalDiscount}`
                            }
                        } else {
                            return 'Rp 0'
                        }
                    },
                    onGettingUserData: () => {
                        axios({
                            method: 'GET',
                            url: '/v1/user',
                        }).then((res) => {
                            data.is_rounded = res.data.data.ml_setting_user.is_rounded
                        })
                    },
                    onChangePriceType: () => {
                        data.products.length = 0;
                        methods.onRemoveAllItems()
                        methods.getProducts(1, '', data.price_type)
                    },
                    checkShift: (response) => {
                        if (response.data.status === false) {
                            $('.shift-open-kasir').html(response.data.data
                                .fullname)

                            $('#modalOpenShift').modal('show');

                            const inputShipping = document.getElementById('shift-open-kas-awal');

                            inputShipping.addEventListener('input', (event) => {
                                let value = inputShipping.value.replace(/\D/g, '');
                                value = new Intl.NumberFormat('en-US').format(value);
                                inputShipping.value = value;
                            });

                            inputShipping.addEventListener('blur', (event) => {
                                let value = inputShipping.value.replace(/\D/g, '');
                                if (value) {
                                    inputShipping.value = new Intl.NumberFormat('en-US', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    }).format(value);
                                }
                            });

                            needOpenShift.value = true
                        } else {
                            $('#btn-close-shift').removeClass('hidden')
                        }
                    },
                    getProducts: (page = 1, search = '', price_type = '') => {
                        return new Promise((resolve) => {
                            axios.get('/v1/product', {
                                params: {
                                    page,
                                    search,
                                    price_type: price_type || 'price',
                                    category_id: data.category_id
                                }
                            }).then(res => {
                                if (!data.temps.scanMode) {
                                    data.products = res.data.data.data;
                                    data.pagination.current_page = res.data.pagination
                                        .current_page;
                                    data.pagination.total_pages = res.data.pagination
                                        .total_pages;
                                    data.pagination.per_page = res.data.pagination.per_page;
                                    data.pagination.total_items = res.data.pagination
                                        .total_items;
                                    $('.product-content').removeClass('hidden');
                                    $('.checkout-section').removeClass('hidden');
                                } else {
                                    if (res.data.data.data.length > 0) {
                                        methods.onSelectProduct(res.data.data.data[0])
                                        data.searchQuery = ''
                                    } else if (data.searchQuery) {
                                        notyf.open({
                                            type: 'warning',
                                            message: 'Produk tidak ditemukan'
                                        });
                                        data.searchQuery = ''
                                    }
                                }
                                resolve();
                            });
                        });
                    },
                    init: () => {
                        methods.getProducts().then(() => {
                            const apiUrl = `/v1/check-status-cashier`;
                            if (petty_cash === true) {
                                axios.get(apiUrl)
                                    .then(response => {
                                        methods.checkShift(response);
                                    })
                                    .catch(error => {
                                        console.error('Error fetching data:', error);
                                    });
                            }
                        });
                    },
                    changePage: (page) => {
                        if (page < 1 || page > data.pagination.total_pages) {
                            return;
                        }
                        methods.getProducts(page, data.searchQuery, data.price_type);
                    },
                    searchProducts: () => {
                        methods.getProducts(1, data.searchQuery, data.price_type);
                    },
                    onSearchInput: () => {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            methods.searchProducts();
                        }, 1000); // 1 detik
                    },
                    onShowingModalCategory: () => {
                        $('#modalKategori').modal('show')

                        $('.category-products').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#modalKategori'),
                            ajax: {
                                url: '/v1/product-categories',
                                dataType: 'json',
                                processResults: function(data) {
                                    return {
                                        results: data.data.map(item => ({
                                            id: item.id,
                                            text: item.name
                                        }))
                                    };
                                }
                            },
                            placeholder: "Pilih Kategori",
                            allowClear: true
                        }).on('change', function() {
                            // const selectedCategory = $(this).val();
                            const selectedData = $(this).select2('data');
                            if (selectedData.length > 0) {
                                const {
                                    text,
                                    id
                                } = selectedData[0];
                                $('.kategoriText').text(text)

                                data.category_id = id
                                axios.get('/v1/product', {
                                    params: {
                                        category_id: id,
                                        price_type: data.price_type
                                    }
                                }).then(res => {
                                    data.products = res.data.data.data;
                                    data.pagination.current_page = res.data.pagination
                                        .current_page;
                                    data.pagination.total_pages = res.data.pagination
                                        .total_pages;
                                    data.pagination.per_page = res.data.pagination.per_page;
                                    data.pagination.total_items = res.data.pagination
                                        .total_items;

                                    $('#modalKategori').modal('hide');
                                })
                            }
                        });
                    },
                    onCloseShiftClick: () => {
                        Swal.fire({
                            title: "Konfirmasi",
                            text: "Shift Kasir Selesai dan Kasir Akan Tutup?",
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonColor: "#375ed1",
                            cancelButtonColor: "#d33",
                            cancelButtonText: "Batalkan",
                            confirmButtonText: "Iya, Tutup Kasir!"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                axios.post('/v1/close-cashier')
                                    .then(response => {
                                        if (response.data.status) {
                                            Swal.fire({
                                                title: 'Success!',
                                                text: response.data.message,
                                                icon: 'success',
                                                confirmButtonText: 'Ok'
                                            }).then((sresult) => {
                                                if (sresult.isConfirmed) {
                                                    if (!modalOpenShift) {
                                                        modalOpenShift = new bootstrap
                                                            .Modal(
                                                                document.getElementById(
                                                                    'modalOpenShift'));
                                                    }

                                                    modalOpenShift.show();
                                                    $('#btn-close-shift').addClass(
                                                        'hidden')
                                                }
                                            })
                                        } else {
                                            Swal.fire({
                                                title: 'Fail!',
                                                text: response.data.message,
                                                icon: 'error'
                                            })
                                        }
                                    })
                                    .catch(error => {
                                        Swal.fire({
                                            title: 'Fail!',
                                            text: error.response.data.message,
                                            icon: 'error'
                                        })
                                    });
                            }
                        });
                    },
                    onSubmitOpenShiftForm: () => {
                        const initialCashAmount = $('#shift-open-kas-awal').val(); // Ambil nilai kas awal

                        // API URL untuk membuka shift
                        const openCashierApiUrl = `/v1/open-cashier`;

                        // Data yang akan dikirim ke API
                        const data = {
                            initial_cash_amount: convertCurrencyToFloat(initialCashAmount)
                        };


                        // Request ke API menggunakan axios
                        axios.post(openCashierApiUrl, data)
                            .then(response => {
                                if (response.data.status) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.data.message,
                                        icon: 'success',
                                        confirmButtonText: 'Ok'
                                    })
                                    $('#modalOpenShift').modal('hide')
                                    $('#btn-close-shift').removeClass('hidden')
                                } else {
                                    Swal.fire({
                                        title: 'Fail!',
                                        text: response.data.message,
                                        icon: 'error'
                                    })
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Fail!',
                                    text: error.response.data.message,
                                    icon: 'error'
                                })
                            });
                    },
                    onShowingModalSelectTable: () => {
                        data.tables.length = 0
                        axios.get('/v1/qr-code-meja').then(response => {
                            data.tables = response.data.data.data
                            $('#modalSelectTable').modal('show')

                            // Menghapus inisialisasi select2 dan menggantinya dengan AJAX untuk input
                            $('#input-search-meja').on('input', function() {
                                const query = $(this).val();
                                if (query.length > 0) {
                                    axios.get(`/v1/qr-code-meja?search=${query}`).then(
                                        response => {
                                            data.tables = response.data.data
                                                .data; // Update data.tables dengan hasil pencarian
                                        });
                                } else {
                                    axios.get(`/v1/qr-code-meja`).then(
                                        response => {
                                            data.tables = response.data.data
                                                .data; // Update data.tables dengan hasil pencarian
                                        });
                                }
                            });
                        })
                    },
                    onSelectTable: (no_meja_data) => {
                        data.value.table = no_meja_data
                        $('#modalSelectTable').modal('hide')
                    },
                    onResetTable: () => {
                        data.value.table = null
                        $('#modalSelectTable').modal('hide')
                    },
                    onSelectProduct: (item) => {
                        // data.value.quantity++

                        let needUpdateGlobalQuantity = false
                        let match = 0
                        let isBuffer = false // perlu pengecekan stok atau tidak
                        data.value.products.forEach((pro, index) => {
                            if (pro.id === item.id) {
                                if (pro.is_variant === 2) {
                                    //
                                } else {
                                    if (!pro.note) {
                                        if (pro.buffered_stock) {
                                            if (data.value.products[index].quantity < pro
                                                .qty_allowed_to_sell) {
                                                match++
                                                data.value.products[index].quantity++
                                                data.value.subtotal += item.price
                                            } else {
                                                isBuffer = true
                                            }
                                        } else {
                                            match++
                                            data.value.products[index].quantity++
                                            data.value.subtotal += item.price
                                        }
                                    }
                                }

                                $('.total-subtotal').text(
                                    `Rp ${new Intl.NumberFormat().format(data.value.subtotal)}`)
                            }
                        })

                        if (isBuffer === false) {
                            if (!match) {
                                let variant = JSON.parse(JSON.stringify(item.variant))
                                variant.forEach((item, index) => {
                                    variant[index].quantity = 0
                                    variant[index].note = null
                                })

                                item = {
                                    ...item,
                                    backupPrice: item.price,
                                    unicode: generateRandomString(24),
                                    quantity: 1,
                                    variant,
                                    note: null
                                }

                                data.value.subtotal += item.price
                                $('.total-subtotal').text(
                                    `Rp ${new Intl.NumberFormat().format(data.value.subtotal)}`)

                                // jika belum ada
                                data.value.products.push(JSON.parse(JSON.stringify(item)))

                                // tambahkan quantity global
                                data.value.quantity++
                            } else {
                                data.value.quantity++
                                data.value.products.forEach((pro) => {
                                    if (pro.id === item.id) {
                                        item = pro
                                    }
                                })
                            }

                            if (item.is_variant) {
                                data.modal.product = JSON.parse(JSON.stringify(item))
                            }

                            setTimeout(() => {
                                if (item.is_variant >= 2) {
                                    data.modal.variantGroupActive = item.variant_groups
                                    $('#modalVarian').modal('show')

                                    const myModalEl = document.getElementById('modalVarian')
                                    myModalEl.addEventListener('hidden.bs.modal', event => {
                                        Object.entries(data.modal).forEach(([key, val]) => {
                                            data.modal[key] = null
                                        })
                                        if (!data.value.variantUpdated) {
                                            data.modal.search = null
                                            data.modal.product = null
                                            data.modal.variantActive = null
                                            data.modal.variantGroupActive = []
                                        }
                                    })
                                }
                            }, 300);
                        }
                    },
                    onFocusPerItem: (pro) => {
                        data.temps.quantityFocus = pro.quantity
                    },
                    autoAddQuantityForVariant: (mode, variant) => {
                        if (variant.length > 0) {
                            variant.forEach((item, index) => {
                                if (mode === '+') {
                                    if (!item.single_pick && variant[index].quantity > 0) {
                                        variant[index].quantity++;
                                        data.value.subtotal += item.varian_price
                                    }
                                } else if (mode === '-') {
                                    if (!item.single_pick && variant[index].quantity > 1) {
                                        variant[index].quantity--;
                                        data.value.subtotal -= item.varian_price
                                    }
                                }
                            })
                        }
                    },
                    changeQuantityPerItem: (mode = '', pro) => {
                        if (mode === '+') {
                            if (pro.buffered_stock) {
                                if (pro.quantity < pro.qty_allowed_to_sell) {
                                    pro.quantity++
                                    data.value.quantity++
                                    data.value.subtotal += pro.price
                                    methods.autoAddQuantityForVariant(mode, pro.variant)
                                }
                            } else {
                                pro.quantity++
                                data.value.quantity++
                                data.value.subtotal += pro.price
                                methods.autoAddQuantityForVariant(mode, pro.variant)
                            }
                        } else if (mode === '-') {
                            if (pro.quantity > 1) {
                                pro.quantity--
                                data.value.quantity--
                                data.value.subtotal -= pro.price
                                methods.autoAddQuantityForVariant(mode, pro.variant)
                            }
                        } else if (mode == '?') {
                            pro.quantity = pro.quantity.replace(/[^0-9]/g, '')
                            if (typeof pro.quantity === 'string' && /^\d+$/.test(pro.quantity)) {
                                pro.quantity = pro.quantity * 1
                            }
                            if (pro.quantity > data.temps.quantityFocus) {
                                // + PLUS
                                if (pro.buffered_stock) {
                                    if (pro.quantity < pro.qty_allowed_to_sell) {
                                        // pro.quantity++
                                        data.value.quantity -= data.temps.quantityFocus
                                        data.value.quantity += pro.quantity
                                        data.value.subtotal -= pro.price * data.temps.quantityFocus
                                        data.value.subtotal += pro.price * pro.quantity

                                        data.temps.quantityFocus = pro.quantity
                                    } else {
                                        data.value.quantity -= data.temps.quantityFocus
                                        data.value.quantity += pro.qty_allowed_to_sell

                                        data.value.subtotal -= pro.price * data.temps.quantityFocus
                                        data.value.subtotal += pro.price * pro.qty_allowed_to_sell

                                        pro.quantity = pro.qty_allowed_to_sell
                                        data.temps.quantityFocus = pro.qty_allowed_to_sell
                                    }
                                } else {
                                    // pro.quantity++
                                    data.value.quantity -= data.temps.quantityFocus
                                    data.value.quantity += pro.quantity
                                    data.value.subtotal -= pro.price * data.temps.quantityFocus
                                    data.value.subtotal += pro.price * pro.quantity
                                    data.temps.quantityFocus = pro.quantity
                                }
                            } else {
                                // - MINUS
                                if (pro.quantity >= 1) {
                                    // pro.quantity--
                                    data.value.quantity -= data.temps.quantityFocus
                                    data.value.quantity += pro.quantity
                                    data.value.subtotal -= pro.price * data.temps.quantityFocus
                                    data.value.subtotal += pro.price * pro.quantity
                                    data.temps.quantityFocus = pro.quantity
                                }
                            }
                        }

                        $('.total-subtotal').text(
                            `Rp ${new Intl.NumberFormat().format(data.value.subtotal)}`)
                    },
                    changeQuantityPerItemOnModalVariant: (mode) => {
                        if (mode === '+') {
                            data.modal.product.quantity++
                            methods.autoAddQuantityForVariant('+', data.modal.product.variant)
                        } else if (mode === '-') {
                            if (data.modal.product.quantity > 0) {
                                data.modal.product.quantity--
                                methods.autoAddQuantityForVariant('-', data.modal.product.variant)
                            }
                        }
                    },
                    addNoteForVariant: (item) => {
                        data.modal.variantActive = item
                        $('#variant-note').val(item.note)
                        setTimeout(() => {
                            $('#addNoteForVariantModal').modal('show')
                            $('#variant-note').val(item.note)
                        }, 100)
                    },
                    onSubmitVariantAddNote: () => {
                        const currVar = data.modal.variantActive

                        data.modal.product.variant.forEach((variant, varIndex) => {
                            if (variant.id === currVar.id) {
                                data.modal.product.variant[varIndex].note = $('#variant-note').val()
                                data.modal.variantActive.note = $('#variant-note').val()
                            }
                        })
                        $('#addNoteForVariantModal').modal('hide')
                    },
                    onChangeVariantQuantity: (item, type) => {
                        if (type === '+') {
                            if (item.max_quantity === 0 || item.quantity < item.max_quantity) {
                                item.quantity++
                            }
                        } else if (type === '-') {
                            if (item.quantity > 0) {
                                item.quantity--
                            }
                        } else if (type == '?') {
                            // Input manual
                            let val = parseInt(item.quantity) || 0
                            if (val < 0) val = 0
                            if (item.max_quantity !== 0 && val > item.max_quantity) val = item.max_quantity
                            item.quantity = val
                        }
                        // Update subtotal jika perlu, atau trigger update lain
                    },
                    onAddNoteForProduct: () => {
                        $('#product-note').val(data.modal.product.note)
                        $('#addNoteForProductModal').modal('show')
                    },
                    onClearProductAddNote: () => {
                        $('#product-note').val('')
                    },
                    onSubmitProductAddNote: (fromCheckoutAction = false) => {
                        if (fromCheckoutAction) {
                            data.value.products.forEach((pro, index) => {
                                if (pro.id === data.modal.product.id) {
                                    data.value.products[index].note = $('#product-note').val()
                                }
                            })
                        }
                        data.modal.product.note = $('#product-note').val()

                        $('#addNoteForProductModal').modal('hide')
                    },
                    onProsesModalVarian: () => {
                        data.value.variantUpdated = true

                        // reset dulu harga product dan variant di sebelumnya
                        data.value.products.forEach((item, index) => {
                            if (item.unicode === data.modal.product.unicode) {
                                data.value.quantity -= item.quantity
                                data.value.subtotal -= item.quantity * item.price

                                item.variant.forEach((variant) => {
                                    if (variant.quantity) {
                                        data.value.subtotal -= variant.quantity * variant
                                            .varian_price
                                    }
                                })
                                setTimeout(() => {
                                    $('.total-subtotal').text(
                                        `Rp ${new Intl.NumberFormat().format(data.value.subtotal)}`
                                    )
                                }, 500)
                            }
                        })

                        data.value.products.forEach((item, index) => {
                            if (item.unicode === data.modal.product.unicode) {
                                data.value.products[index] = data.modal.product
                                data.value.quantity += data.modal.product.quantity
                                data.value.subtotal += data.modal.product.quantity * data.modal
                                    .product.price

                                data.modal.product.variant.forEach((variant) => {
                                    if (variant.quantity) {
                                        data.value.subtotal += variant.quantity * variant
                                            .varian_price
                                    }
                                })
                                setTimeout(() => {
                                    $('.total-subtotal').text(
                                        `Rp ${new Intl.NumberFormat().format(data.value.subtotal)}`
                                    )
                                }, 500)
                            }
                        })

                        data.products.forEach((pro, index) => {
                            if (pro.id === data.modal.product.id) {
                                data.products[index] = data.modal.product
                            }
                        })

                        setTimeout(() => {
                            $('#modalVarian').modal('hide')
                            setTimeout(() => {
                                data.value.variantUpdated = false
                                data.modal.search = null
                                data.modal.product = null
                                data.modal.variantActive = null
                                data.modal.variantGroupActive = []
                            }, 500)
                        }, 500)
                    },
                    onShowingShippingModal: () => {
                        $('#shippingModal').modal('show')
                    },
                    onSubmitShippingCost: () => {
                        const shipping = convertCurrencyToFloat($('#data-value-shipping').val())
                        data.value.shipping = shipping
                        setTimeout(() => {

                            $('.total-shipping').text(
                                `Rp ${new Intl.NumberFormat().format(shipping)}`)
                            setTimeout(() => {
                                $('#shippingModal').modal('hide')
                            }, 100)
                        }, 100)
                    },
                    onRemoveItem: (item) => {
                        data.value.quantity -= item.quantity
                        data.value.subtotal -= item.quantity * item.price

                        if (item.variant && item.variant.length) {
                            item.variant.forEach((variant) => {
                                if (item.quantity > 0) {
                                    data.value.subtotal -= variant.quantity * variant.varian_price
                                }
                            })
                        }

                        $('.total-subtotal').text(
                            `Rp ${new Intl.NumberFormat().format(data.value.subtotal)}`)

                        data.value.products.forEach((pro, index) => {
                            if (pro.id === item.id) {
                                data.value.products.splice(index, 1)
                            }
                        })
                    },
                    onRemoveAllItems: () => {
                        data.value.quantity = 0
                        data.value.subtotal = 0
                        data.value.products.length = 0
                        $('.total-subtotal').text(`Rp 0`)
                    },
                    onShowingAddNewCustomerModal: () => {
                        $('#addNewCustomer').modal('show')

                        $('#customer-province').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $("#addNewCustomer"),
                            ajax: {
                                url: '/v1/administrative/provinces',
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    return {
                                        search: params.term // search term
                                    };
                                },
                                processResults: function(data) {
                                    apiResults = data.data.data.map(function(item) {
                                        return {
                                            text: item.province_name,
                                            id: item.province_id
                                        };
                                    });

                                    return {
                                        results: apiResults
                                    };
                                },
                                cache: false
                            },
                        })
                        $('#customer-province').on('change', function(e) {
                            var selectedValue = $(this).val();
                            // var selectedText = $(this).find("option:selected").text();

                            data.temps.addNewCust.prov_id = selectedValue
                            methods.onSelectCity()
                        });

                        methods.onSelectCity()
                    },
                    onSubmitAddNewCustomer: () => {
                        const name = $('#customer-name').val()
                        const phone = $('#customer-phone').val()
                        const email = $('#customer-email').val()
                        const address = $('#customer-address').val()
                        const province_id = data.temps.addNewCust.prov_id
                        const city_id = data.temps.addNewCust.city_id
                        const district_id = data.temps.addNewCust.dist_id

                        axios({
                                method: 'POST',
                                url: '/v1/customer',
                                data: {
                                    name,
                                    phone,
                                    email,
                                    address,
                                    province_id,
                                    city_id,
                                    district_id
                                }
                            }).then((res) => {
                                const id = res.data.id

                                notyf.open({
                                    type: 'success',
                                    message: res.data.message
                                });
                                $('#addNewCustomer').modal('hide')

                                $('#customer-name').val('')
                                $('#customer-phone').val('')
                                $('#customer-email').val('')
                                $('#customer-address').val('')
                                $('#customer-province').val(null).trigger('change');
                                $('#customer-city').val(null).trigger('change');
                                $('#customer-district').val(null).trigger('change');
                            })
                            .catch(error => {
                                notyf.open({
                                    type: 'error',
                                    message: error.response.data.message
                                });
                            });
                    },
                    calcTax: (withRp = true) => {
                        // Hitung diskon terlebih dahulu
                        let diskonValue = 0;
                        if (data.value.tempVoucher) {
                            if (data.value.tempVoucher.type === 'persen') {
                                diskonValue = (data.value.tempVoucher.value / 100) * data.value.subtotal;
                            } else {
                                // kondisi ketika diskon berupa nominal - langsung gunakan nilai diskon
                                diskonValue = data.value.tempVoucher.value;
                            }
                        } else {
                            diskonValue = convertCurrencyToFloat(data.value.customVoucherBeforeSubmit || 0);
                        }

                        // console.log='+diskonValue, 'subtotal='+data.value.subtotal, 'shipping='+data.value.shipping)

                        // Subtotal setelah diskon = subtotal - diskon + ongkir
                        let subtotalAfterDiscount = data.value.subtotal - diskonValue + data.value.shipping;
                        // console.log('subtotalAfterDiscount=' + subtotalAfterDiscount)

                        // Pajak dihitung dari subtotal setelah diskon
                        const pajak = (tax / 100) * subtotalAfterDiscount;
                        const totalHarusDibayar = subtotalAfterDiscount + pajak;

                        let finalTax = pajak

                        data.value.tax = finalTax
                        return withRp ? `Rp ${new Intl.NumberFormat().format(finalTax)}` : finalTax
                    },
                    onSelectCity: () => {
                        $('#customer-city').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $("#addNewCustomer"),
                            ajax: {
                                url: '/v1/administrative/cities?province_id=' + data.temps
                                    .addNewCust.prov_id,
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    return {
                                        search: params.term // search term
                                    };
                                },
                                processResults: function(data) {
                                    apiResults = data.data.data.map(function(item) {
                                        return {
                                            text: item.city_name,
                                            id: item.city_id
                                        };
                                    });

                                    return {
                                        results: apiResults
                                    };
                                },
                                cache: false
                            },
                        })
                        $('#customer-city').on('change', function(e) {
                            var selectedValue = $(this).val();
                            data.temps.addNewCust.city_id = selectedValue
                            methods.onSelectDistrict()
                        });

                        methods.onSelectDistrict()
                    },
                    onSelectDistrict: () => {
                        $('#customer-district').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $("#addNewCustomer"),
                            ajax: {
                                url: '/v1/administrative/districts?city_id=' + data.temps.addNewCust
                                    .city_id,
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    return {
                                        search: params.term // search term
                                    };
                                },
                                processResults: function(data) {
                                    apiResults = data.data.data.map(function(item) {
                                        return {
                                            text: item.subdistrict_name,
                                            id: item.subdistrict_id
                                        };
                                    });

                                    return {
                                        results: apiResults
                                    };
                                },
                                cache: false
                            },
                        })
                        $('#customer-district').on('change', function(e) {
                            var selectedValue = $(this).val();
                            data.temps.addNewCust.dist_id = selectedValue
                        });
                    },

                    onChangePricePerItem: (pro) => {
                        const prevPrice = pro.backupPrice
                        let newPrice = pro.price

                        newPrice = String(newPrice).replace(/[^0-9]/g, '')
                        if (/^\d+$/.test(newPrice)) {
                            newPrice = parseInt(newPrice)
                        } else {
                            newPrice = 0
                        }

                        // Kurangi subtotal dengan harga lama * quantity
                        if (prevPrice > 0) {
                            data.value.subtotal -= prevPrice * pro.quantity
                        }

                        // Tambahkan subtotal dengan harga baru * quantity
                        if (newPrice > 0) {
                            data.value.subtotal += newPrice * pro.quantity
                        }

                        // Update harga produk dan backupPrice
                        pro.price = newPrice
                        pro.backupPrice = newPrice

                        $('.total-subtotal').text(
                            `Rp ${new Intl.NumberFormat().format(data.value.subtotal)}`
                        )
                    },
                    onShowingAddProductNoteFromCheckout: (pro) => {
                        data.modal.product = JSON.parse(JSON.stringify(pro))
                        data.modal.variantGroupActive = pro.variant_groups
                        if (pro.is_variant === 2) {
                            $('#modalVarian').modal('show')
                        } else {
                            methods.onAddNoteForProduct()
                        }
                    }
                }

                methods.init()

                onMounted(() => {
                    document.addEventListener('keydown', methods.handleKeyDown);
                    methods.onGettingUserData()
                    $('.nxl-navigation .navbar-content .nxl-micon').css('min-width', '45px')
                    $('#select2-pos').on('change', function(e) {
                        var selectedValue = $(this).val();
                        var selectedText = $(this).find("option:selected").text();

                        data.value.customer = selectedValue
                    });

                    const inputShipping = document.getElementById('data-value-shipping');

                    inputShipping.addEventListener('input', (event) => {
                        let value = inputShipping.value.replace(/\D/g, '');
                        value = new Intl.NumberFormat('en-US').format(value);
                        inputShipping.value = value;
                    });

                    inputShipping.addEventListener('blur', (event) => {
                        let value = inputShipping.value.replace(/\D/g, '');
                        if (value) {
                            inputShipping.value = new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    });

                    const inputVoucher = document.getElementById('custom-voucher');

                    inputVoucher.addEventListener('input', (event) => {
                        let value = inputVoucher.value.replace(/\D/g, '');
                        value = new Intl.NumberFormat('en-US').format(value);
                        inputVoucher.value = value;
                    });

                    inputVoucher.addEventListener('blur', (event) => {
                        let value = inputVoucher.value.replace(/\D/g, '');
                        if (value) {
                            inputVoucher.value = new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        }
                    });
                })

                return {
                    data,
                    methods,
                    searchInput
                };
            }
        }).mount('#app');
    </script>
@endsection
