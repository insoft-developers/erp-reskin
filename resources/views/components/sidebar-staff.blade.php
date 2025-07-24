<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="#" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{ asset('template/main') }}/images/logo.png" alt="" class="logo logo-lg logo-besar" />
                <span class="brand-title">{{ $cname }}</span>
                <img src="{{ asset('template/main') }}/images/logo.png" alt="" class="logo logo-sm" />
            </a>
        </div>

        <div class="navbar-content">
            <ul class="nxl-navbar">
                @can('view menu journal')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ url('/') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-jurnal.png') }}"></span>
                            <span class="nxl-mtext menu-text">Jurnal</span><br>
                            <span class="nxl-mtext menu-subtitle">Jurnal Akuntansi</span>
                        </a>
                    </li>
                @endcan
                @can('view menu pos')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="/pos/index" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-pos.png') }}"></span>
                            <span class="nxl-mtext menu-text">POS (Point of Sales)</span><br>
                            <span class="nxl-mtext menu-subtitle">Kasir & Penjualan</span>
                        </a>
                    </li>
                @endcan
                @can('view menu manajemen pesanan')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="/manajemen-pesanan" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-manajemen-pesanan.png') }}"></span>
                            <span class="nxl-mtext menu-text">Manajemen Pesanan</span><br>
                            <span class="nxl-mtext menu-subtitle">Data Penjualan Bisnis</span>
                        </a>
                    </li>
                @endcan

                @can('view menu rekapitulasi harian')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-rekapitulasi harian.png') }}"></span>
                            <span class="nxl-mtext menu-text">Rekapitulasi Harian</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Transaksi Harian Outlet</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('pengeluaran.index') }}">Data
                                    Pengeluaran</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ url('rekapitulasi-v2-harian') }}">Rekapitulasi Harian</a></li>
                        </ul>
                    </li>
                @endcan
                @can('view menu invoice generator')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-invoice-generator.png') }}"></span>
                            <span class="nxl-mtext menu-text">Invoice Generator</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Buat Invoice Online</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('invoice.invoice.create') }}">Buat
                                    Invoice</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('invoice.invoice.index') }}">Daftar
                                    Invoice</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('invoice.client.index') }}">Daftar
                                    Klien</a></li>
                        </ul>
                    </li>
                @endcan
                @can('view menu laporan')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ url('report') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-laporan.png') }}"></span>
                            <span class="nxl-mtext menu-text">Laporan</span><br>
                            <span class="nxl-mtext menu-subtitle">Lihat Laporan</span>
                        </a>
                    </li>
                @endcan

                @can('view menu manajemen produk')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-produk.png') }}"></span>
                            <span class="nxl-mtext menu-text">Manajemen Produk</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Data Produk / Barang</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('product/create') }}">Tambah Produk &
                                    Barang </a>
                            </li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('product') }}">Daftar Produk & Barang</a>
                            </li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('product_category') }}">Kategori
                                    Produk</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu cogs manufaktur')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0)" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-cogs-manufaktur.png') }}"></span>
                            <span class="nxl-mtext menu-text">COGS Manufaktur</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Harga Pokok Penjualan</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a href="{{ url('main_material') }}" class="nxl-link">Daftar Bahan Baku</a>
                            </li>
                            <li class="nxl-item">
                                <a href="{{ url('inter_product') }}" class="nxl-link">Daftar Barang Setengah Jadi</a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('view menu pembelian dan produksi')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-pembelian.png') }}"></span>
                            <span class="nxl-mtext menu-text">Pembelian & Produksi</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Transaksi Pembelian</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('product_purchase') }}">Transaksi Beli
                                    Produk (Beli Jadi)</a>
                            </li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('product_manufacture') }}">Transaksi
                                    Buat Produk
                                    (Manufaktur)</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('inter_purchase') }}">Transaksi Buat Barang
                                    Setengah Jadi</a>
                            </li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('material_purchase') }}">Transaksi
                                    Beli Bahan Baku</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('transfer-stock.product.index') }}">Transfer Stok Barang Jadi</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('transfer-stock.material.index') }}">Transfer Stok Bahan Baku</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('main_supplier') }}">Daftar
                                    Supplier</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu penyesuaian')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-penyesuaian.png') }}"></span>
                            <span class="nxl-mtext menu-text">Penyesuaian</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Retur & Penyesuaian Stok</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('adjustment.index') }}">Daftar
                                    Penyesuaian</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('adjustment.create') }}">Penyesuaian
                                    Barang Dagang</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('adjustment.createInterProduct') }}">Penyesuaian Barang ½ Jadi</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('adjustment.createMaterial') }}">Penyesuaian Bahan Baku</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('adjustment.category.index') }}">Kategori Penyesuaian</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu utang dan piutang')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-utang & Piutang.png') }}"></span>
                            <span class="nxl-mtext menu-text">Utang & Piutang</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Data Utang / Piutang</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('utang.index') }}">Daftar Utang</a>
                            </li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('piutang.index') }}">Daftar
                                    Piutang</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu penyusutan')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('penyusutan.index') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-penyusutan.png') }}"></span>
                            <span class="nxl-mtext menu-text">Penyusutan</span><br>
                            <span class="nxl-mtext menu-subtitle">Data Aset & Peralatan</span>
                        </a>
                    </li>
                @endcan


                @can('view menu biaya-biaya')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-biaya-biaya.png') }}"></span>
                            <span class="nxl-mtext menu-text">Biaya - biaya</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Pengeluaran Bisnis</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('expense.index') }}">Daftar
                                    Pengeluaran</a></li>
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('expense.category.index') }}">Kategori Pengeluaran</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu qr meja')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-qr-pesan.png') }}"></span>
                            <span class="nxl-mtext menu-text">QR Meja / Pesan</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">QR Code Meja & Pesanan</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/qr-code') }}">QR Meja &
                                    Reservasi</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/print-qr-code') }}">Cetak QR
                                    Meja</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu randu wallet')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ url('wallet-logs') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-wallet.png') }}"></span>
                            <span class="nxl-mtext menu-text">Randu Wallet</span><br>
                            <span class="nxl-mtext menu-subtitle">Dompet Bisnis</span>
                        </a>
                    </li>
                @endcan


                @can('view menu landing page')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('landing-page.index') }}" class="nxl-link">
                            <span class="nxl-micon">
                                <img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-landingpage.png') }}">
                            </span>
                            <span class="nxl-mtext menu-text">Landing Page</span><br>
                            <span class="nxl-mtext menu-subtitle">Halaman Landas Iklan</span>
                        </a>
                    </li>
                @endcan

                @can('view menu storefront')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-storefront.png') }}"></span>
                            <span class="nxl-mtext menu-text">Storefront</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Toko Online Ecommerce</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link"
                                    href="{{ route('storefront', session('username')) }}" target="_blank">Lihat Toko
                                    Online</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('storefront-setting') }}">Pengaturan
                                    Toko Online</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu crm pelanggan')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-crm-pelanggan.png') }}"></span>
                            <span class="nxl-mtext menu-text">CRM Pelanggan</span><span class="nxl-arrow"><i
                                    class="feather-chevron-right"></i></span><br>
                            <span class="nxl-mtext menu-subtitle">Data Pelanggan & Promosi</span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('crm.customer.index') }}">Data
                                    Pembeli</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('crm.followup.index') }}">Text
                                    FollowUp & Upselling</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('crm.discount.index') }}">List
                                    Diskon</a></li>
                        </ul>
                    </li>
                @endcan

                @can('view menu cabang dan staff')
                    @if (session('is_upgraded') == 1)
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><img class="img-menu"
                                        src="{{ asset('template/main/images/menu/menu-cabang-staff.png') }}"></span>
                                <span class="nxl-mtext menu-text">Cabang & Staff</span><span class="nxl-arrow"><i
                                        class="feather-chevron-right"></i></span><br>
                                <span class="nxl-mtext menu-subtitle">Data Cabang Bisnis</span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('branch.index') }}">Data
                                        Cabang</a></li>
                                <li class="nxl-item"><a class="nxl-link" href="{{ route('staff.index') }}">Data Staff &
                                        Karyawan</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="nxl-item nxl-hasmenu">
                            <a href="#" class="nxl-link" onclick="alertPremiumMenu()">
                                <span class="nxl-micon"><img class="img-menu"
                                        src="{{ asset('template/main/images/menu/menu-cabang-staff.png') }}"></span>
                                <span class="nxl-mtext menu-text">Cabang & Staff</span><br>
                                <span class="nxl-mtext menu-subtitle">Data Cabang Bisnis</span>
                            </a>
                        </li>
                    @endif
                @endcan

                @can('view menu randu ai')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ url('report') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-randu-AI.png') }}"></span>
                            <span class="nxl-mtext menu-text">Randu AI</span><br>
                            <span class="nxl-mtext menu-subtitle">Randu AI</span>
                        </a>
                    </li>
                @endcan

                @can('view menu pengaturan aplikasi')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ url('setting') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-pengaturan.png') }}"></span>
                            <span class="nxl-mtext menu-text">Pengaturan Aplikasi</span><br>
                            <span class="nxl-mtext menu-subtitle">Data Bisnis & Aplikasi</span>
                        </a>
                    </li>
                @endcan

                @can('view menu go premium')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ url('premium') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-go-premium.png') }}"></span>
                            <span class="nxl-mtext menu-text">Go Premium</span><br>
                            <span class="nxl-mtext menu-subtitle">Profit Dulu, Upgrade Kemudian</span>
                        </a>
                    </li>
                @endcan


                @can('view menu bantuan')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="https://help.randu.co.id" class="nxl-link" target="_blank" rel="noopener noreferrer">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-bantuan.png') }}"></span>
                            <span class="nxl-mtext menu-text">Bantuan</span><br>
                            <span class="nxl-mtext menu-subtitle">Chat CS & Tutorial</span>
                        </a>
                    </li>
                @endcan

                @can('view menu notifikasi')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('notification.index') }}" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-notification.png') }}"></span>
                            <span class="nxl-mtext menu-text">Notification</span><br>
                            <span class="nxl-mtext menu-subtitle">Notifikasi</span>
                        </a>
                    </li>
                @endcan
                @can('view menu randu academy')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><img class="img-menu"
                                    src="{{ asset('template/main/images/menu/menu-randu-academy.png') }}"></span>
                            <span class="nxl-mtext menu-text">Randu Academy</span><br>
                            <span class="nxl-mtext menu-subtitle">Randu Akademi</span>
                        </a>
                    </li>
                @endcan
            </ul>

        </div>
    </div>
</nav>
