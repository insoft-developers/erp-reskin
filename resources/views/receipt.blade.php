<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('template/main') }}/images/logo.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Force mobile-like width even on desktop */
        .mobile-container {
            max-width: 420px;
            margin: 0 auto;
            min-height: 100vh;
            background-color: #f9fafb;
            position: relative;
        }
        
        /* Hide desktop scrollbar but keep functionality */
        body {
            background: #e5e7eb;
            overflow-x: hidden;
        }
        
        /* Responsive untuk layar sangat kecil */
        @media (max-width: 390px) {
            .mobile-container {
                max-width: 100%;
            }
        }

        /* Styling untuk item receipt yang lebih baik */
        .receipt-item {
            transition: background-color 0.2s ease;
        }
        
        .receipt-item:hover {
            background-color: #f9fafb;
        }
        
        /* Styling untuk varian produk */
        .variant-item {
            background-color: #f8fafc;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-300">
    <div class="mobile-container">
        <div id="app" class="min-h-screen bg-gray-50">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-4 py-3">
                <div class="flex items-center justify-center">
                    {{-- <button @click="goBack" class="p-2">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </button> --}}
                    <h1 class="text-lg font-medium text-gray-900">Struk Pembayaran</h1>
                    {{-- <button class="p-2">
                        <i class="fas fa-ellipsis-h text-gray-600"></i>
                    </button> --}}
                </div>
            </div>

            <!-- Hero Section - Dinamis berdasarkan status -->
            <div class="bg-white px-4 py-8 text-center">
                <div v-if="receiptData.paymentStatus === 1" class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-white text-3xl"></i>
                </div>
                <div v-else class="w-20 h-20 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-clock text-white text-3xl"></i>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-900 mb-3">@{{ formatRupiah(receiptData.totalBayar) }}</h2>
                
                <p v-if="receiptData.paymentStatus === 1" class="text-gray-600 text-lg mb-6">
                    Terima kasih telah belanja di @{{ receiptData.storeName }}
                </p>
                <p v-else class="text-gray-600 text-lg mb-6">
                    Pesanan Anda di @{{ receiptData.storeName }}
                </p>
                
                <div v-if="receiptData.paymentStatus === 1" class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-green-700 text-sm font-medium">
                        <i class="fas fa-check-circle mr-2"></i>
                        Pesanan Anda telah selesai. Terimakasih sudah berbelanja di tempat kami. 🙏
                    </p>
                </div>
                <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-700 text-sm font-medium">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Pesanan Anda sedang menunggu pembayaran. Silakan lakukan pembayaran untuk melanjutkan pesanan. ⏰
                    </p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white mx-4 mt-4 rounded-lg shadow-sm">
                <!-- Receipt Header -->
                <div class="p-6 text-center border-b border-gray-100">
                    <div class="space-y-3">
                        <div class="text-sm text-gray-500">
                            <div>Tanggal</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.tanggal }}</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <div>Nama Pembeli</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.namaPembeli }}</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <div>No. Telepon</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.noTelepon }}</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <div>Metode Pembayaran</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.metodePembayaran }}</div>
                        </div>
                        <div v-if="receiptData.noMeja !== '-'" class="text-sm text-gray-500">
                            <div>No. Meja</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.noMeja }}</div>
                        </div>
                    </div>
                </div>

                <!-- Order Details Section -->
                <div class="p-6">
                    <div :class="['border-l-4 pl-4 mb-6', receiptData.paymentStatus === 1 ? 'border-emerald-500' : 'border-yellow-500']">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Rincian Pesanan</h2>
                        <div v-for="item in receiptData.orderItems" :key="item.id" class="py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 pr-3">
                                    <div class="flex items-start">
                                        <span class="text-sm text-gray-600 mr-2 font-medium">@{{ item.quantity }}x</span>
                                        <div class="text-sm text-gray-900 leading-tight flex-1">
                                            <div class="font-medium">@{{ item.name }}</div>
                                            <!-- Catatan produk -->
                                            <div v-if="item.note" class="text-xs text-gray-500 mt-1 italic">@{{ item.note }}</div>
                                            <!-- Detail varian -->
                                            <div v-if="item.variants && item.variants.length > 0" class="mt-2 space-y-1">
                                                <div v-for="variant in item.variants" :key="variant.id" class="flex justify-between items-start text-xs ml-2">
                                                    <div class="flex items-start flex-1">
                                                        <span class="w-1 h-1 bg-gray-400 rounded-full mr-2 mt-1.5"></span>
                                                        <div class="flex-1">
                                                            <div class="text-gray-600">@{{ variant.name }} (@{{ variant.quantity }}x)</div>
                                                            <div v-if="variant.note" class="text-gray-500 italic">@{{ variant.note }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right ml-2">
                                                        <div class="text-gray-500">@{{ formatRupiah(variant.price) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900 flex-shrink-0 text-right min-w-[80px]">
                                    <!-- Harga produk utama -->
                                    <div class="mb-1">@{{ formatRupiah(item.price * item.quantity) }}</div>
                                    <!-- Total varian -->
                                    <div v-if="item.variants && item.variants.length > 0" class="space-y-1">
                                        <div v-for="variant in item.variants" :key="variant.id" class="text-xs text-gray-500">
                                            @{{ formatRupiah(variant.price * variant.quantity) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary Section -->
                    <div :class="['border-l-4 pl-4', receiptData.paymentStatus === 1 ? 'border-emerald-500' : 'border-yellow-500']">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Pembayaran</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">
                                    Subtotal <span class="text-xs text-gray-500">(@{{ receiptData.totalProduct }} PRODUK)</span>
                                </span>
                                <span class="text-sm text-gray-900">@{{ formatRupiah(receiptData.subtotal) }}</span>
                            </div>
                            <div v-if="receiptData.diskon > 0" class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Diskon (-)</span>
                                <span class="text-sm text-gray-900">@{{ formatRupiah(receiptData.diskon) }}</span>
                            </div>
                            <div v-if="receiptData.shipping > 0" class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Order Fee</span>
                                <span class="text-sm text-gray-900">@{{ formatRupiah(receiptData.shipping) }}</span>
                            </div>
                            <div v-if="receiptData.pajak > 0" class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Pajak</span>
                                <span class="text-sm text-gray-900">@{{ formatRupiah(receiptData.pajak) }}</span>
                            </div>
                            <div v-if="receiptData.pembulatan !== 0" class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Pembulatan</span>
                                <span class="text-sm text-gray-900">@{{ formatRupiah(receiptData.pembulatan) }}</span>
                            </div>
                            <hr class="border-gray-200">
                            <div class="flex justify-between items-center font-medium">
                                <span class="text-sm text-gray-900">
                                    @{{ receiptData.paymentStatus === 1 ? 'Total' : 'Total yang Harus Dibayar' }}
                                </span>
                                <span :class="['text-sm font-bold', receiptData.paymentStatus === 1 ? 'text-gray-900' : 'text-yellow-600']">
                                    @{{ formatRupiah(receiptData.totalBayar) }}
                                </span>
                            </div>
                            <div v-if="receiptData.paymentStatus === 1" class="space-y-1 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Total Bayar</span>
                                    <span>@{{ formatRupiah(receiptData.uangDiterima) }}</span>
                                </div>
                                <div v-if="receiptData.kembalian > 0" class="flex justify-between">
                                    <span>Kembalian</span>
                                    <span>@{{ formatRupiah(receiptData.kembalian) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Section (appears when scrolled down) -->
            <div v-show="showAdditionalSection" class="bg-white mx-4 mt-4 rounded-lg shadow-sm p-6 text-center">
                <!-- Payment Instructions for Pending -->
                <div v-if="receiptData.paymentStatus === 0" class="border border-yellow-200 rounded-lg p-4 mb-6 bg-yellow-50">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h3>
                    <div class="text-sm space-y-3 text-left">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-500 mr-3"></i>
                            <span class="text-gray-700">Batas waktu pembayaran: 30 menit</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-credit-card text-yellow-500 mr-3"></i>
                            <span class="text-gray-700">Silakan lakukan pembayaran sesuai metode yang dipilih</span>
                        </div>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pesanan</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Status</div>
                            <div :class="['font-medium flex items-center', receiptData.paymentStatus === 1 ? 'text-green-600' : 'text-yellow-600']">
                                <i :class="['mr-1', receiptData.paymentStatus === 1 ? 'fas fa-check-circle' : 'fas fa-clock']"></i>
                                @{{ receiptData.status }}
                            </div>
                        </div>
                        <div>
                            <div class="text-gray-600">ID Pesanan</div>
                            <div class="font-medium text-gray-900 text-xs">@{{ receiptData.idOrder }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">No. Pesanan</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.noOrder }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Tipe Pesanan</div>
                            <div class="font-medium text-gray-900 flex items-center justify-center text-xs">
                                <i class="fas fa-utensils mr-1 text-red-500"></i>
                                @{{ receiptData.tipePesanan }}
                            </div>
                        </div>
                        <div>
                            <div class="text-gray-600">Waktu Pesan</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.waktuPesan }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Kasir</div>
                            <div class="font-medium text-gray-900">@{{ receiptData.kasir }}</div>
                        </div>
                        <div v-if="receiptData.catatan" class="col-span-2">
                            <div class="text-gray-600">Catatan</div>
                            <div class="font-medium text-gray-900 text-left">@{{ receiptData.catatan }}</div>
                        </div>
                        <div v-if="receiptData.flag" class="col-span-2">
                            <div class="text-gray-600">Flag</div>
                            <div class="font-medium text-gray-900 text-left">@{{ receiptData.flag }}</div>
                        </div>
                    </div>
                </div>

                <!-- Store Information -->
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Toko</h3>
                    <div class="text-sm space-y-2">
                        <div class="font-medium">@{{ receiptData.storeName }}</div>
                        <div class="text-gray-600 text-xs leading-relaxed">@{{ receiptData.alamatToko }}</div>
                        <div class="text-gray-600">Telp: @{{ receiptData.noTeleponToko }}</div>
                    </div>
                </div>

                <!-- Custom Footer -->
                <div v-if="receiptData.footer" class="border border-gray-200 rounded-lg p-4 mb-6 text-center">
                    <div class="text-sm text-gray-600" v-html="receiptData.footer"></div>
                </div>
            </div>

            <!-- Fixed Bottom Buttons -->
            <div class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-[420px] p-4 bg-white border-t border-gray-200">
                <div class="space-y-2">
                    <button v-if="receiptData.paymentStatus === 0" @click="makePayment" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-4 rounded-lg">
                        Lakukan Pembayaran
                    </button>
                    <button @click="contactSeller" :class="['w-full font-medium py-3 px-4 rounded-lg', receiptData.paymentStatus === 1 ? 'bg-emerald-500 hover:bg-emerald-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50']">
                        Hubungi Penjual
                    </button>
                </div>
            </div>

            <div class="h-32"></div> <!-- Spacer for fixed buttons -->
        </div>
    </div>

    <script>
        const { createApp, ref, reactive, onMounted, onUnmounted } = Vue;

        createApp({
            setup() {
                const showAdditionalSection = ref(false);
                
                // Data dari backend Laravel
                const receiptData = reactive(@json($receiptData));

                const formatRupiah = (amount) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                };

                const handleScroll = () => {
                    const scrollPosition = window.scrollY;
                    showAdditionalSection.value = scrollPosition > 300;
                };

                const goBack = () => {
                    window.history.back();
                };

                const contactSeller = () => {
                    let phoneNumber = receiptData.noTeleponToko.replace(/[^0-9]/g, '');
                    if (phoneNumber.startsWith('0')) {
                        phoneNumber = '62' + phoneNumber.slice(1);
                    }
                    const message = receiptData.paymentStatus === 1 
                        ? `Halo, saya ingin menanyakan tentang pesanan ${receiptData.idOrder}`
                        : `Halo, saya ingin menanyakan tentang pesanan pending ${receiptData.idOrder}. Status pembayaran saya bagaimana ya?`;
                    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
                    window.open(whatsappUrl, '_blank');
                };

                const makePayment = () => {
                    alert('Mengarahkan ke halaman pembayaran...');
                };

                onMounted(() => {
                    window.addEventListener('scroll', handleScroll);
                });

                onUnmounted(() => {
                    window.removeEventListener('scroll', handleScroll);
                });

                return {
                    showAdditionalSection,
                    receiptData,
                    formatRupiah,
                    goBack,
                    contactSeller,
                    makePayment
                };
            }
        }).mount('#app');
    </script>
</body>
</html>
