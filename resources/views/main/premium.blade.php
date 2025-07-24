@extends('master')

@section('style')
    <style>
        .premium-container {
            font-family: "Questrial", sans-serif;
            color: #4f4f4f;
        }

        .custom-list li {
            display: flex;
            align-items: center;
        }

        @keyframes move-up {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            50% {
                transform: translateY(-50%);
                opacity: 0;
            }

            51% {
                transform: translateY(50%);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes move-down {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            50% {
                transform: translateY(50%);
                opacity: 0;
            }

            51% {
                transform: translateY(-50%);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .hover\:animate-move-up span {
            animation: move-down 0.3s ease-in-out;
        }

        .hover\:animate-move-up:hover span {
            animation: move-up 0.3s ease-in-out;
        }

        .no-effect.btn-outline-warning:hover,
        .no-effect.btn-outline-warning:active,
        .no-effect.btn-outline-warning:focus,
        .no-effect.btn-outline-warning:focus-visible {
            color: #ffa21d !important;
            background-color: transparent;
            border-color: #ffa21d !important;
            box-shadow: none;
        }
    </style>
@endsection

@section('content')
    <div class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('premium') }}">Premium</a></li>
                        <li class="breadcrumb-item">Upgrade Account</li>
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
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">


                        </div>
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content bg-white">
                <div class="premium-container container mx-auto p-3">
                    <h5 class="fw-semibold mb-3" style="font-size: 1.875rem;">Profit Dulu, Upgrade Kemudian</h1>
                        <div class="text-muted mb-4" style="font-size: 0.95rem;">
                            <p class="mb-3"><strong>Terima Kasih Telah Menjadi Pengguna Setia Aplikasi&nbsp;
                                    Randu</strong><br>Jika bisnis Anda belum menghasilkan profit (keuntungan) Anda dapat terus menggunakan aplikasi ini
                                secara gratis tanpa biaya sepeser pun, tanpa ada batasan transaksi berapapun.</p>
                        </div>

                        <div class="row mt-8">
                            @if ($data['versi gratis']->is_active)
                                <div class="col-lg-4 mb-3">
                                    <div class="border rounded-4 p-4 bg-white shadow">
                                        <div class="d-flex pt-2 pb-3">
                                            <h6 class="border px-4 py-1 rounded-5 text-uppercase"
                                                style="color: {{ $data['versi gratis']->color_title_text }}; background-color: {{ $data['versi gratis']->color_title_card }}">
                                                {{ $data['versi gratis']->name }}</h6>
                                        </div>
                                        <p class="mb-2"><span
                                                class="h1 fw-semibold">Rp{{ $data['versi gratis']->format_price }}</span>  Selamanya</p>
                                        <p class="mb-4">Untuk UMKM & Baru Mulai Bisnis</p>
                                        <hr class="border-secondary my-4">
                                        <ul class="list-unstyled mb-4 custom-list">
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu POS & Kasir</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Inventory</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Jurnal Akuntansi</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu QR Meja (Table)</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Manufaktur</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu CRM</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Landingpage & Toko
                                                Online</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Invoice & Pembayaran
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu AI (Kecerdasan
                                                Buatan)
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Training via Video Tutorial
                                                & Live Chat</li>
                                        </ul>
                                        <hr class="border-secondary my-4">
                                    </div>
                                </div>
                            @endif


                    <h5 class="fw-semibold mb-3" style="font-size: 1.875rem;"></h1>
                        <div class="text-muted mb-4" style="font-size: 0.95rem;">
                            <p class="mb-3">Namun, apabila bisnis Anda sudah menghasilkan keuntungan kami mengundang Anda untuk meng-upgrade
                                ke versi premium dengan biaya hanya Rp819 per hari.</p>
                            <p class="mb-3">Jika Anda menjalankan bisnis nirlaba (yayasan, panti asuhan, atau organisasi)
                                atau menggunakan Randu untuk keperluan pendidikan baik sebagai pengajar maupun siswa,
                                silakan hubungi kami melalui menu ticketing untuk mendapatkan akses premium secara gratis.
                            </p>
                            <p><strong>Tim Randu,</strong> Sayangku Padamu Selalu</span></p>
                        </div>
            <div class="row mt-8">
                            @if ($data['premium 3 bulan']->is_active)
                                <div class="col-lg-4 mb-3">
                                    <div class="border rounded-4 p-4 bg-white shadow">
                                        <div class="d-flex pt-2 pb-3">
                                            <h6 class="border px-4 py-1 rounded-5 text-uppercase"
                                                style="color: {{ $data['premium 3 bulan']->color_title_text }}; background-color: {{ $data['premium 3 bulan']->color_title_card }}">
                                                {{ $data['premium 3 bulan']->name }}</h6>
                                        </div>
                                        <p class="mb-2"><span
                                                class="h1 fw-semibold">Rp{{ $data['premium 3 bulan']->format_price }}</span>
                                            / 3 Bulan
                                            </p>
                                        <p class="mb-4">Untuk Bisnis Yang Bertumbuh</p>
                                        <hr class="border-secondary my-4">
                                        <ul class="list-unstyled mb-4 custom-list">
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu POS & Kasir</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Inventory</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Jurnal Akuntansi</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu QR Meja (Table)</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Manufaktur</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu CRM</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Landingpage & Toko
                                                Online</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Invoice & Pembayaran
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu AI (Kecerdasan
                                                Buatan)
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Training Online via
                                                Zoom/Google Meet</li>
                                        </ul>
                                        <hr class="border-secondary my-4">
                                        <p class="mb-2">Fitur Tambahan Premium Bulanan</p>
                                        <ul class="list-unstyled mb-4 custom-list">
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Aplikasi Randu - Appsensi
                                                Untuk Absensi & Kunjungan Online Staff/Sales</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Menambahkan Role Staff
                                                dalam
                                                Aplikasi (Kasir, Keuangan, Admin, dan lainnya)</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Menambahkan 1 Cabang
                                                / Bisnis Lain
                                            </li>
                                            <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i> Dashboard
                                                Randu Owner (Kontrol banyak Bisnis dalam 1 Dashboard)</li>
                                            <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i> Upload Produk, Bahan Baku, Barang Setengah Jadi + Setting Modal + Setting Akun (Tinggal Pakai Doang)</li>
                                        </ul>
                                        <hr class="border-secondary my-4">
                                        <div class="d-flex justify-content-center">
                                            <form
                                                action="{{ route('premium.store', ['id' => $data['premium 3 bulan']->id]) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-warning fw-semibold hover:animate-move-up rounded"><span
                                                        class="text-white">UPGRADE 3 BULAN</span></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($data['premium tahunan']->is_active)
                                <div class="col-lg-4 mb-3">
                                    <div class="border rounded-4 p-4 bg-white shadow">
                                        <div class="d-flex pt-2 pb-3">
                                            <h6 class="border px-4 py-1 rounded-5 text-uppercase"
                                                style="color: {{ $data['premium tahunan']->color_title_text }}; background-color: {{ $data['premium tahunan']->color_title_card }}">
                                                {{ $data['premium tahunan']->name }}</h6>
                                        </div>
                                        <p class="mb-2"><span
                                            class="h1 fw-semibold">Rp{{ $data['premium tahunan']->format_price }}</span>
                                            /
                                            {{ $data['premium tahunan']->format_frequency }}</p>
                                        <p class="mb-4">Premium Tahunan Tanpa Cabang</p>
                                        <hr class="border-secondary my-4">
                                        <ul class="list-unstyled mb-4 custom-list">
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu POS & Kasir</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Inventory</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Jurnal Akuntansi
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu QR Meja (Table)</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Manufaktur</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu CRM</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Landingpage & Web
                                                Toko
                                                Online</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Invoice & Pembayaran
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu AI (Kecerdasan
                                                Buatan)
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Training Offline (Visit) &
                                                Online</li>
                                        </ul>
                                        <hr class="border-secondary my-4">
                                        <p class="mb-2">Fitur Tambahan Premium Tahunan</p>
                                        <ul class="list-unstyled mb-4 custom-list">



                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Aplikasi Randu - Appsensi
                                                Untuk Absensi & Kunjungan Online Staff/Sales</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Menambahkan Role Staff
                                                dalam
                                                Aplikasi (Kasir, Keuangan, Admin, dan lainnya)</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Menambahkan 1 Cabang
                                                / Bisnis Lain
                                            </li>
                                            <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i> Dashboard
                                                Randu Owner (Kontrol banyak Bisnis dalam 1 Dashboard)</li>
                                            <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i> Upload Produk, Bahan Baku, Barang Setengah Jadi + Setting Modal + Setting Akun (Tinggal Pakai Doang)</li>
                                                                                        <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i>Bisa Request Tambahan Fitur / Modifikasi / Integrasi</li>
                                        </ul>
                                        <hr class="border-secondary my-4">
                                        <div class="d-flex justify-content-center">
                                            <form
                                                action="{{ route('premium.store', ['id' => $data['premium tahunan']->id]) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-warning fw-semibold hover:animate-move-up rounded"><span
                                                        class="text-white">UPGRADE TAHUNAN</span></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($data['premium tahunan plus']->is_active)
                                <div class="col-lg-4 mb-3">
                                    <div class="border rounded-4 p-4 bg-white shadow">
                                        <div class="d-flex pt-2 pb-3">
                                            <h6 class="border px-4 py-1 rounded-5 text-uppercase"
                                                style="color: {{ $data['premium tahunan plus']->color_title_text }}; background-color: {{ $data['premium tahunan plus']->color_title_card }}">
                                                {{ $data['premium tahunan plus']->name }}</h6>
                                        </div>
                                        <p class="mb-2"><span
                                            class="h1 fw-semibold">Rp{{ $data['premium tahunan plus']->format_price }}</span>
                                            /
                                            {{ $data['premium tahunan']->format_frequency }}</p>
                                        <p class="mb-4">Premium Tahunan Plus 5 Cabang Baru</p>
                                        <hr class="border-secondary my-4">
                                        <ul class="list-unstyled mb-4 custom-list">
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu POS & Kasir</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Inventory</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Jurnal Akuntansi
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu QR Meja (Table)</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Manufaktur</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu CRM</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Landingpage & Web
                                                Toko
                                                Online</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu Invoice & Pembayaran
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Randu AI (Kecerdasan
                                                Buatan)
                                            </li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-check-square text-success me-3"
                                                    style="line-height: 1.20em !important;"></i> Training Offline (Visit) &
                                                Online</li>
                                        </ul>
                                        <hr class="border-secondary my-4">
                                        <p class="mb-2">Fitur Tambahan Premium Tahunan + Setting Setting Aplikasi</p>
                                        <ul class="list-unstyled mb-4 custom-list">



                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Aplikasi Randu - Appsensi
                                                Untuk Absensi & Kunjungan Online Staff/Sales</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Menambahkan Role Staff
                                                dalam
                                                Aplikasi (Kasir, Keuangan, Admin, dan lainnya)</li>
                                            <li class="fw-semibold text-muted"><i
                                                    class="fs-3 fas fa-star text-warning me-3"
                                                    style="line-height: 1.20em !important;"></i> Menambahkan 5 Cabang
                                                Bisnis
                                            </li>
                                            <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i> Dashboard
                                                Randu Owner (Kontrol banyak Bisnis dalam 1 Dashboard)</li>
                                            <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i> Upload Produk, Bahan Baku, Barang Setengah Jadi + Setting Modal + Setting Akun (Tinggal Pakai Doang)</li>
                                            
                                            <li class="fw-semibold text-muted pt-3"><i class="fs-3 fas fa-crown me-3"
                                                    style="line-height: 1.20em !important; color: #00A6F6;"></i>Bisa Request Tambahan Fitur 
                                                / Modifikasi / Integrasi</li>
                                             </ul>
                                        <hr class="border-secondary my-4">
                                        <div class="d-flex justify-content-center">
                                            <form
                                                action="{{ route('premium.store', ['id' => $data['premium tahunan plus']->id]) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-warning fw-semibold hover:animate-move-up rounded"><span
                                                        class="text-white">AMBIL RANDU PLUS</span></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        history.pushState(null, "", location.href.split("?")[0]);

        @if (session('error'))
            $(document).ready(function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Keuntungan bisnismu sudah lebih dari 10 Juta Rupiah, atau Omset bisnis mu sudah lebih dari 50 Juta Rupiah. Silakan upgrade ke versi Premium terlebih dahulu.'
                });
            })
        @endif
    </script>
@endsection
