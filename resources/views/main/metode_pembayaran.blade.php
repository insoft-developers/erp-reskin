@extends('master')
@section('topstyle')
    <script src="https://cdn.tailwindcss.com"></script>
@endsection
@section('style')
    <style>
        .selected {
            background-color: #007bff;
            color: white;
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
@endsection
@section('content')
    <div id="app">
        <main class="nxl-container init-check hidden">
            <div class="nxl-content">
                <!-- [ page-header ] start -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('pos/index') }}">POS</a></li>
                            <li class="breadcrumb-item">Metode Pembayaran</li>
                        </ul>
                    </div>
                    <div class="page-header-right ms-auto">
                        <div class="page-header-right-items">
                            <div class="d-flex d-md-none">
                                <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                    <i class="feather-arrow-left me-2"></i>
                                    <span>Back</span>
                                </a>
                            </div>
                        </div>
                        <div class="d-md-none d-flex align-items-center">
                            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                <i class="feather-align-right fs-50"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="main-content">
                    <div class="grid grid-cols-3 gap-[10px]">
                        <div
                            class="col-span-1 px-3 rounded-[10px] h-[100px] text-white font-bold items-center flex flex-col lg:flex-row justify-center lg:justify-between bg-gradient-to-r from-amber-500 to-amber-800">
                            <div class="hidden lg:block md:text-[17px] xl:text-[20px] md:leading-[20px] xl:leading-[23px]">
                                Total<br /> Transaksi</div>
                            <div class="lg:hidden text-[12px] text-center">Total
                                Transaksi</div>
                            <div class="text-[15px]">@{{ `Rp ${new Intl.NumberFormat().format(data.storage.paid)}` }}</div>
                        </div>
                        <div
                            class="col-span-1 px-3 rounded-[10px] h-[100px] text-white font-bold items-center flex flex-col lg:flex-row justify-center lg:justify-between bg-gradient-to-r from-emerald-500 to-emerald-800">
                            <div class="hidden lg:block md:text-[17px] xl:text-[20px] md:leading-[20px] xl:leading-[23px]">
                                Total<br /> Pembayaran</div>
                            <div class="lg:hidden text-[12px] text-center">Total
                                Pembayaran</div>

                            <div class="text-[15px]">@{{ `Rp ${new Intl.NumberFormat().format(data.value.total_customer_payment)}` }}</div>
                        </div>
                        <div
                            class="col-span-1 px-3 rounded-[10px] h-[100px] text-white font-bold items-center flex flex-col lg:flex-row justify-center lg:justify-between bg-gradient-to-r from-rose-500 to-rose-800">
                            <div class="md:text-[17px] xl:text-[20px] md:leading-[20px] xl:leading-[23px]">Kembalian</div>
                            <div class="text-[15px]">@{{ `Rp ${new Intl.NumberFormat().format(data.value.total_customer_payment ? data.value.total_customer_payment - data.storage.paid : 0)}` }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-[10px] mt-[16px]">
                        <div class="hidden md:block col-span-1">
                            <div class="flex flex-col gap-[10px] bg-[#1086e3] p-4 rounded-[10px]">
                                <div v-for="(item, index) in data.payment_methods" :key="index"
                                    :class="{
                                        'w-full cursor-pointer transitions-all duration-200 h-[100px] flex flex-col justify-center items-center text-white px-3 rounded-[10px] text-[18px] hover:bg-[#0f6bb6]': true,
                                        'bg-[#4ba3ea]': data
                                            .selected_pm !== item.id,
                                        'bg-[#0f6bb6]': data.selected_pm === item.id,
                                        'hidden': item.selected === false
                                    }"
                                    @click="methods.selectSidebar(item)">
                                    <div class="md:text-[16px] xl:text-[23px] text-center font-bold">@{{ item.method }}
                                    </div>
                                    <div class="md:text-[12px] xl:text-[15px] text-center">@{{ item.description }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-3 md:col-span-2">
                            <div class="card h-full">
                                <div class="card-body relative">
                                    <div class="block md:hidden">
                                        <div class="font-bold text-[16px] text-black">
                                            Metode Pembayaran
                                        </div>
                                        <div class="grid grid-cols-4 gap-[16px] mt-[16px] mb-[25px]">
                                            <div v-for="(item, index) in data.payment_methods"
                                                @click="methods.selectSidebar(item)"
                                                :class="`col-span-2 cursor-pointer hover:bg-gray-100 ${item.selected ? '' : 'hidden'} ${data.selected_pm === item.id ? 'bg-emerald-100' : ''} transitions-all duration-200 h-[120px] px-3 shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black border rounded-[12px] flex flex-col items-center justify-center`">
                                                <div class="md:text-[16px] xl:text-[23px] text-center font-bold">
                                                    @{{ item.method }}
                                                </div>
                                                <div class="text-[12px] text-gray-600 text-center">@{{ item.description }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="data.selected_pm === 1">
                                        <div class="font-bold text-[16px] text-black">
                                            Pembayaran Tunai
                                        </div>
                                        <div class="flex items-center gap-[16px]">
                                            <div
                                                class="bg-[#d1e6f9] py-2 mt-3 px-3 rounded-[8px] text-[14px] font-medium relative w-[150px] text-center text-[#2877b5]">
                                                @{{ data.value.tunai.selected_type }}
                                            </div>
                                            <div v-if="data.value.total_customer_payment"
                                                class="bg-[#faf7af] py-2 mt-3 px-3 rounded-[8px] text-[14px] font-medium relative w-[150px] text-center text-[#2877b5]">
                                                @{{ `Rp ${new Intl.NumberFormat().format(data.value.total_customer_payment)}` }}
                                            </div>
                                        </div>
                                        <div class="font-bold text-[16px] text-black mt-4">
                                            Jenis Pembayaran Tunai
                                        </div>
                                        <div class="grid grid-cols-3 gap-[16px] mt-[16px]">
                                            <div @click="() => {
                                                data.value.tunai.selected_type = 'Uang Pas'
                                                data.value.total_customer_payment = JSON.parse(JSON.stringify(data.value.total_customer_payment_pas))
                                            }"
                                                :class="{
                                                    'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                    'border-blue-400': data.value.tunai.selected_type === 'Uang Pas'
                                                }"
                                            >
                                                Uang Pas
                                            </div>
                                            <div @click="() => {
                                                data.value.tunai.selected_type = 'Jumlah Lain'
                                                data.value.total_customer_payment = JSON.parse(JSON.stringify(data.value.total_customer_payment_select_other))
                                            }"
                                                :class="{
                                                    'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                    'border-blue-400': data.value.tunai.selected_type === 'Jumlah Lain'
                                                }"
                                            >
                                                Jumlah Lain
                                            </div>
                                            <div @click="() => {
                                                data.value.tunai.selected_type = 'Input Manual'
                                                data.value.total_customer_payment = JSON.parse(JSON.stringify(data.value.total_customer_payment_input_custom))
                                            }"
                                                :class="{
                                                    'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                    'border-blue-400': data.value.tunai.selected_type === 'Input Manual'
                                                }"
                                            >
                                                Input Manual
                                            </div>
                                            <div class="col-span-3" v-if="data.value.tunai.selected_type === 'Jumlah Lain'">
                                                <div class="grid grid-cols-3 gap-[16px]">
                                                    <div @click="() => {
                                                        data.value.total_customer_payment = item
                                                        data.value.total_customer_payment_select_other = item
                                                    }"
                                                        class="col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black border rounded-[12px] flex items-center justify-center"
                                                        v-for="item in data.value.tunai.suggestionPaymentAmounts">
                                                        @{{ `Rp ${new Intl.NumberFormat().format(item)}` }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                :class="{
                                                    'col-span-3': true,
                                                    'hidden': data.value.tunai
                                                        .selected_type !== 'Input Manual'
                                                }">
                                                <div class="grid grid-cols-3 gap-[16px]">
                                                    <input type="text" id="manual-input-payment"
                                                        class="col-span-3 px-4 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black border rounded-[12px] flex items-center justify-center" />
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            v-if="data.selected_pm_data && data.selected_pm_data.flags && data.selected_pm_data.flags.length">
                                            <div class="font-bold text-[16px] text-black mt-4">
                                                Flag
                                            </div>
                                            <div class="grid grid-cols-3 gap-[16px] mt-[16px]">
                                                <div @click="methods.selectFlag(item.flag, item.id)"
                                                    v-for="item in data.selected_pm_data.flags"
                                                    :class="{
                                                        'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                        'border-blue-400': item.flag === data.value.flag
                                                    }">
                                                    @{{ item.flag }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="data.selected_pm === 2">
                                        <div class="mx-auto bg-white p-6 shadow-md border-2 border-dashed border-gray-400">
                                            <h2 class="text-xl font-semibold mb-4">Pembayaran dengan metode Payment Gateway
                                                (Randu Wallet)</h2>
                                            <p class="mb-4">Akan langsung membuka halaman pembayaran setelah klik “PROSES
                                                TRANSAKSI“</p>
                                            <ul class="list-disc list-inside mb-4">
                                                <li>Pastikan koneksi dalam keadaan stabil</li>
                                                <li>Pastikan pembeli sudah siap untuk melakukan pembayaran</li>
                                                <li>Saldo akan masuk ke akun Randu Wallet maksimal 1 sd 2x24 jam (maksimal)
                                                    dan bisa langsung di cairkan ke rekening akun</li>
                                                <li>Potongan 0,7% dari nominal transaksi untuk biaya administrasi</li>
                                                <li>Pilihan metode pembayaran yang aktif:</li>
                                            </ul>
                                            <ul class="list-disc list-inside ml-4 mb-4">
                                                <li>Virtual Akun (BCA, Mandiri, BRI, BNI, ATM Bersama)</li>
                                                <li>E-Wallet (Dana, OVO, Shopeepay, Link Aja)</li>
                                                <li>QRIS</li>
                                                <li>Kartu Kredit</li>
                                                <li>Retail (Alfamart & Indomart)</li>
                                                <li>Kredit Payment</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div v-if="data.selected_pm === 3">
                                        <div class="font-bold text-[16px] text-black">
                                            Pembayaran Tranfer Ke Bank
                                        </div>
                                        <div class="flex items-center gap-[16px]">
                                            <div v-if="data.value.transfer.detail"
                                                class="bg-[#d1e6f9] py-2 mt-3 px-3 rounded-[8px] text-[14px] font-medium relative w-[150px] text-center text-[#2877b5]">
                                                @{{ data.value.transfer.detail.method }}
                                            </div>
                                            <div v-else class="py-2 mt-3 text-[14px] font-medium relative text-gray-500">
                                                -
                                            </div>
                                        </div>
                                        <div class="font-bold text-[16px] text-black mt-4">
                                            Pilih Bank <span class="font-normal text-orange-500">(Klik salah satu)</span>
                                        </div>

                                        <div class="grid grid-cols-3 gap-[16px] mt-[16px]">
                                            <div @click="() => {
                                                data.value.transfer.detail = item
                                                data.selected_sub_pm = item.id  
                                            }"
                                                v-for="item in data.temps.items"
                                                :class="{
                                                    'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                    'hidden': !item.selected,
                                                    'border-blue-400': data.selected_sub_pm === item.id
                                                }">
                                                @{{ item.method }}
                                            </div>
                                        </div>

                                        <div
                                            v-if="data.selected_sub_pm && data.value.transfer.detail && data.value.transfer.detail.flags && data.value.transfer.detail.flags.length">
                                            <div class="font-bold text-[16px] text-black mt-4">
                                                Flag
                                            </div>
                                            <div class="grid grid-cols-3 gap-[16px] mt-[16px]">
                                                <div @click="methods.selectFlag(item.flag, item.id)"
                                                    v-for="item in data.value.transfer.detail.flags"
                                                    :class="{
                                                        'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                        'border-blue-400': item.flag === data.value.flag
                                                    }">
                                                    @{{ item.flag }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="font-bold text-[16px] text-black mt-4">
                                            Detail Rekening
                                        </div>

                                        <div class="mt-[14px] text-black">
                                            <div class="flex items-center">
                                                <div class="min-w-[180px]">Nama Bank</div>
                                                <div>: @{{ data.value.transfer.detail?.method || '-' }}</div>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="min-w-[180px]">Nomor Rekening</div>
                                                <div>: @{{ data.value.transfer.detail?.bankAccountNumber || '-' }}</div>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="min-w-[180px]">Atas Nama</div>
                                                <div>: @{{ data.value.transfer.detail?.bankOwner || '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="data.selected_pm === 4">
                                        <div class="font-bold text-[16px] text-black">
                                            Jenis Kasbon/Piutang
                                        </div>

                                        <div class="grid grid-cols-3 gap-[16px] mt-[16px]">
                                            <div @click="() => {
                                                data.value.piutang.detail = item
                                                data.selected_sub_pm = item.id
                                            }"
                                                v-for="item in data.temps.items"
                                                :class="{
                                                    'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                    'hidden': !item.selected,
                                                    'border-blue-400': data.value.piutang.detail?.code === item
                                                        .code
                                                }">
                                                @{{ item.method }}
                                            </div>
                                        </div>

                                        <div
                                            v-if="data.value.piutang.detail && data.value.piutang.detail.flags && data.value.piutang.detail.flags.length">
                                            <div class="font-bold text-[16px] text-black mt-4">
                                                Flag
                                            </div>
                                            <div class="grid grid-cols-3 gap-[16px] mt-[16px]">
                                                <div @click="methods.selectFlag(item.flag, item.id)"
                                                    v-for="item in data.value.piutang.detail.flags"
                                                    :class="{
                                                        'border-2 col-span-1 cursor-pointer hover:bg-gray-100 transitions-all duration-200 h-[60px] shadow shadow-sm font-medium text-[16px] -tracking-[2%] text-black rounded-[12px] flex items-center justify-center': true,
                                                        'border-blue-400': item.flag === data.value.flag
                                                    }">
                                                    @{{ item.flag }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="data.selected_pm === 5">
                                        {{-- <div class="font-bold text-[16px] text-black">
                                            Instant QRIS
                                        </div>

                                        <div class="grid grid-cols-3 gap-[16px] mt-[16px]">
                                            <div class="col-span-3 flex flex-col items-center justify-center">
                                                <canvas id="qrcode" class="mt-4"></canvas>
                                                <div v-if="data.qris_is_ready" class="text-lg text-center mt-3 font-bold">
                                                    @{{ data.qris_ref }}
                                                </div>
                                                <div v-if="data.qris_is_ready" class="text-lg text-center mt-3">
                                                    Silahkan scan barcode ini di device Anda<br />
                                                    menggunakan <b>QRIS</b> untuk melakukan pembayaran
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="mx-auto bg-white p-6 shadow-md border-2 border-dashed border-gray-400">
                                            <h2 class="text-xl font-semibold mb-4">Pembayaran dengan metode Instant QRIS
                                            </h2>

                                            <ul class="list-disc list-inside mb-4">
                                                <li>Pastikan koneksi dalam keadaan stabil</li>
                                                <li>Pastikan pembeli sudah siap untuk melakukan pembayaran</li>
                                                <li>Saldo akan masuk ke akun Randu Wallet maksimal 1 sd 2x24 jam (maksimal)
                                                    dan bisa langsung di cairkan ke rekening akun</li>
                                                <li>Potongan 0,7% dari nominal transaksi untuk biaya administrasi</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer border-0">
                                    <div class="flex items-center gap-[16px]">
                                        <a href="/pos/index"
                                            class="flex-grow py-3 text-[16px] text-white font-bold rounded-[16px] bg-red-400 hover:bg-red-600 flex transitions-all duration-200 items-center justify-center">
                                            <i class="fas fa-chevron-left me-2"></i> Kembali Ke POS </a>
                                        <button :disabled="data.paymentProgress" type="button" @click="methods.onProcess"
                                            class="flex-grow py-3 text-[16px] text-white font-bold rounded-[16px] bg-emerald-400 hover:bg-emerald-600 flex transitions-all duration-200 items-center justify-center">
                                            Proses Transaksi <i v-if="data.paymentProgress"
                                                class="fas fa-spinner fa-spin ms-2"></i><i v-else
                                                class="fas fa-chevron-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
@section('js')
    {{-- <script src="https://unpkg.com/vue@3.2.47/dist/vue.global.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script>
        // const {
        //     createApp,
        //     ref,
        //     reactive,
        //     onMounted
        // } = Vue;

        function convertCurrencyToFloat(currencyStr) {
            // Menghilangkan semua koma dari string
            const cleanedStr = currencyStr.replace(/,/g, '');

            // Mengonversi string yang sudah dibersihkan menjadi float
            const currencyFloat = parseFloat(cleanedStr);

            return currencyFloat;
        }

        function suggestPaymentAmounts(total) {
            const denominations = [5000, 10000, 20000, 50000, 100000];

            // Fungsi untuk menemukan kelipatan terdekat dari sebuah angka berdasarkan denominasi yang tersedia
            function closestMultiple(value, multiple) {
                return Math.ceil(value / multiple) * multiple;
            }

            // Array untuk menyimpan saran pembayaran
            let suggestions = [];

            // Temukan nominal yang mendekati total pembayaran
            for (let i = 0; i < denominations.length; i++) {
                let suggestion = closestMultiple(total, denominations[i]);
                if (!suggestions.includes(suggestion)) {
                    suggestions.push(suggestion);
                }
            }

            // Tambahkan kelipatan 100000 jika belum ada
            if (!suggestions.includes(100000)) {
                suggestions.push(100000);
            }

            // Urutkan saran berdasarkan jarak terdekat dari total pembayaran
            suggestions.sort((a, b) => Math.abs(a - total) - Math.abs(b - total));

            // Pastikan hanya 3 saran yang dikembalikan, termasuk kelipatan 100000 jika ada
            const topSuggestions = suggestions.slice(0, 2);
            if (!topSuggestions.includes(100000)) {
                topSuggestions.push(100000);
            }

            return topSuggestions;
        }

        const storage = JSON.parse(localStorage.getItem('pos'))
        if (!storage) {
            window.location.href = '/pos/index'
        }
        createApp({
            setup() {
                console.log(storage)
                const data = reactive({
                    paymentProgress: false,
                    payment_methods: [],
                    selected_pm: 1,
                    selected_pm_data: null,
                    selected_sub_pm: null,
                    storage,
                    value: {
                        flag: null,
                        flag_id: null,
                        metode_code: null,
                        total_customer_payment: storage.paid,
                        total_customer_payment_pas: storage.paid,
                        total_customer_payment_select_other: 0,
                        total_customer_payment_input_custom: 0,
                        tunai: {
                            selected_type: 'Uang Pas',
                            suggestionPaymentAmounts: []
                        },
                        transfer: {
                            detail: null
                        },
                        piutang: {
                            detail: null
                        }
                    },
                    temps: {
                        items: []
                    },
                    qris_is_ready: false,
                    qris_ref: ''
                })

                const methods = {
                    init: () => {
                        axios({
                            method: 'GET',
                            url: '/v1/type-payment'
                        }).then((res) => {
                            if (!res.data.status) {
                                window.location.href = '/payment-method-setting'
                            }

                            data.payment_methods = res.data.data
                            console.log(data.payment_methods)
                            data.value.tunai.suggestionPaymentAmounts = suggestPaymentAmounts(data
                                .storage
                                .paid);

                            methods.selectSidebar(data.payment_methods[0])
                        })
                    },
                    selectSidebar: (item) => {
                        data.storage.instant_qris = false;
                        if (item.method === 'Instant QRIS') {
                            data.storage.instant_qris = true;
                            //     axios({
                            //         method: 'GET',
                            //         url: '/v1/instant-qris'
                            //     }).then((res) => {
                            //         data.qris_is_ready = true
                            //         data.qris_ref = res.data.data.reference
                            //         const qr = new QRious({
                            //             element: document.getElementById('qrcode'),
                            //             value: res.data.data
                            //                 .qrString,
                            //             size: 200
                            //         });
                            //     }).catch((err) => {
                            //         console.log(err.response.data)
                            //     })
                        }

                        console.log(item)
                        data.selected_pm = item.id
                        data.selected_pm_data = item
                        data.temps.items = item.items
                    },
                    selectFlag: (flag, id) => {
                        data.value.flag = flag
                        data.value.flag_id = id
                    },
                    onProcess: () => {
                        data.payment_methods.forEach((item) => {
                            if (item.id === data.selected_pm) {
                                if (!item.items) {
                                    data.storage.payment_method = item.code
                                } else {
                                    item.items.forEach((subItem) => {
                                        if (subItem.id === data.selected_sub_pm) {
                                            data.storage.payment_method = subItem.code
                                        }
                                    })
                                }
                            }
                        })

                        let payment_amount = data.storage.paid
                        if (data.selected_pm === 1) {
                            payment_amount = data.value.total_customer_payment
                        }

                        data.paymentProgress = true
                        axios({
                            method: 'POST',
                            url: '/v1/payment',
                            data: {
                                ...data.storage,
                                flag_id: data.value.flag_id,
                                payment_amount,
                            }
                        }).then((res) => {
                            data.paymentProgress = false
                            localStorage.removeItem('pos')
                            if (res.data.data.payment_method !== 'randu-wallet') {
                                window.location.href = '/pos/terima-kasih?reference=' + res.data
                                    .data.reference
                            } else {
                                if (res.data.data.paymentUrl) {
                                    window.open(res.data.data.paymentUrl, '_blank');

                                    setTimeout(() => {
                                        window.location.href = res.data.returnUrl
                                    }, 1000);
                                } else {
                                    window.location.href = res.data.returnUrl
                                }

                            }
                        }).catch((err) => {
                            data.paymentProgress = false
                            console.log(err.response.data);
                        })
                    }
                }

                methods.init()

                onMounted(() => {
                    $('.init-check').removeClass('hidden')
                    const manualInputPayment = document.getElementById('manual-input-payment');

                    manualInputPayment.addEventListener('input', (event) => {
                        let value = manualInputPayment.value.replace(/\D/g, '');
                        value = new Intl.NumberFormat('en-US').format(value);
                        manualInputPayment.value = value;
                        data.value.total_customer_payment = convertCurrencyToFloat(
                            manualInputPayment.value)
                        data.value.total_customer_payment_input_custom = convertCurrencyToFloat(
                            manualInputPayment.value)
                    });

                    manualInputPayment.addEventListener('blur', (event) => {
                        let value = manualInputPayment.value.replace(/\D/g, '');
                        if (value) {
                            manualInputPayment.value = new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                            data.value.total_customer_payment = convertCurrencyToFloat(
                                manualInputPayment.value)
                            data.value.total_customer_payment_input_custom = convertCurrencyToFloat(
                                manualInputPayment.value)
                        }
                    });
                })

                return {
                    data,
                    methods,
                }
            }
        }).mount('#app');
    </script>
@endsection
