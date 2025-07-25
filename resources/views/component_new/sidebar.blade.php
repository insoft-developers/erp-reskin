<!-- Sidenav Menu Start -->
<div class="two-col-sidebar" id="two-col-sidebar">
    <div class="twocol-mini">

        <!-- Add -->
        <div class="dropdown">
            <a class="btn btn-primary bg-gradient btn-sm btn-icon rounded-circle d-flex align-items-center justify-content-center"
                data-bs-toggle="dropdown" href="javascript:void(0);" role="button" data-bs-display="static"
                data-bs-reference="parent">
                <i class="isax isax-add"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-start">
                <li>
                    <a href="add-invoice.html" class="dropdown-item d-flex align-items-center">
                        <i class="isax isax-document-text-1 me-2"></i>Invoice
                    </a>
                </li>
                <li>
                    <a href="expenses.html" class="dropdown-item d-flex align-items-center">
                        <i class="isax isax-money-send me-2"></i>Expense
                    </a>
                </li>
                <li>
                    <a href="add-credit-notes.html" class="dropdown-item d-flex align-items-center">
                        <i class="isax isax-money-add me-2"></i>Credit Notes
                    </a>
                </li>
                <li>
                    <a href="add-debit-notes.html" class="dropdown-item d-flex align-items-center">
                        <i class="isax isax-money-recive me-2"></i>Debit Notes
                    </a>
                </li>
                <li>
                    <a href="add-purchases-orders.html" class="dropdown-item d-flex align-items-center">
                        <i class="isax isax-document me-2"></i>Purchase Order
                    </a>
                </li>
                <li>
                    <a href="add-quotation.html" class="dropdown-item d-flex align-items-center">
                        <i class="isax isax-document-download me-2"></i>Quotation
                    </a>
                </li>
                <li>
                    <a href="add-delivery-challan.html" class="dropdown-item d-flex align-items-center">
                        <i class="isax isax-document-forward me-2"></i>Delivery Challan
                    </a>
                </li>
            </ul>
        </div>
        <!-- /Add -->

        <ul class="menu-list">
            <li>
                <a href="account-settings.html" data-bs-toggle="tooltip" data-bs-placement="right"
                    data-bs-title="Settings"><i class="isax isax-setting-25"></i></a>
            </li>
            <li>
                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="right"
                    data-bs-title="Documentation"><i class="isax isax-document-normal4"></i></a>
            </li>
            <li>
                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="right"
                    data-bs-title="Changelog"><i class="isax isax-cloud-change5"></i></a>
            </li>
            <li>
                <a href="login.html"><i class="isax isax-login-15"></i></a>
            </li>
        </ul>
    </div>
    <div class="sidebar" id="sidebar-two">

        <!-- Start Logo -->
        <div class="sidebar-logo">
            <a href="index.html" class="logo logo-normal">
                <img src="{{ asset('reskin') }}/assets/img/logo.svg" alt="Logo">
            </a>
            <a href="index.html" class="logo-small">
                <img src="{{ asset('reskin') }}/assets/img/logo-small.svg" alt="Logo">
            </a>
            <a href="index.html" class="dark-logo">
                <img src="{{ asset('reskin') }}/assets/img/logo-white.svg" alt="Logo">
            </a>
            <a href="index.html" class="dark-small">
                <img src="{{ asset('reskin') }}/assets/img/logo-small-white.svg" alt="Logo">
            </a>

            <!-- Sidebar Hover Menu Toggle Button -->
            <a id="toggle_btn" href="javascript:void(0);">
                <i class="isax isax-menu-1"></i>
            </a>
        </div>
        <!-- End Logo -->

        <!-- Search -->
        <div class="sidebar-search">
            <div class="input-icon-end position-relative">
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-icon-addon">
                    <i class="isax isax-search-normal"></i>
                </span>
            </div>
        </div>
        <!-- /Search -->

        <!--- Sidenav Menu -->
        <div class="sidebar-inner" data-simplebar>
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>

                    <li>
                        <ul>
                            <li>
                                <a href="{{ url('/') }}">
                                    <i class="isax isax-element-45"></i><span>Dashboard</span>

                                </a>

                            </li>
                        </ul>
                    </li>


                    <li>
                        <ul>

                            <li class="menu-title"><span>Jurnal Akuntansi</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Jurnal</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Buat Jurnal Baru</a></li>
                                    <li><a href="{{ url('journal_list') }}">Daftar Jurnal</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>




                    <li>
                        <ul>

                            <li class="menu-title"><span>Purchases</span></li>
                            <li>
                                <a href="{{ url('pos/index') }}">
                                    <i class="isax isax-element-45"></i><span>POS</span>

                                </a>

                            </li>

                            <li>
                                <a href="{{ url('manajemen-pesanan') }}">
                                    <i class="isax isax-element-45"></i><span>Manajemen Pesanan</span>

                                </a>

                            </li>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Rekapitulasi Harian</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('pengeluaran') }}">Data Pengeluaran</a></li>
                                    <li><a href="{{ url('rekapitulasi-v2-harian') }}">Rekapitulasi Harian</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-category-25"></i><span>Invoice Generator</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('invoice/invoice/create') }}">Buat Invoice</a></li>
                                    <li><a href="{{ url('invoice/invoice') }}">Daftar Invoice</a></li>
                                    <li><a href="{{ url('invoice/client') }}">Daftar Klien</a></li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>CRM Pelanggan</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Data Pembeli</a></li>
                                    <li><a href="{{ url('/') }}">Followup Upselling</a>
                                    </li>
                                    <li><a href="{{ url('journal_add') }}">List Diskon</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <ul>

                            <li class="menu-title"><span>Laporan dan Analisa</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Laporan</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Laporan Penjualan</a></li>
                                    <li><a href="{{ url('/') }}">Rekapitulasi Jurnal</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Neraca Saldo</a></li>
                                    <li><a href="{{ url('/') }}">Laporan Laba Rugi</a></li>
                                    <li><a href="{{ url('/') }}">Laporan Neraca</a></li>
                                    <li><a href="{{ url('/') }}">Rekapitulasi Harian</a></li>
                                    <li><a href="{{ url('/') }}">Laporan Pajak</a></li>
                                    <li><a href="{{ url('/') }}">Laporan Stok</a></li>
                                    <li><a href="{{ url('/') }}">Laporan Absensi</a></li>
                                    <li><a href="{{ url('/') }}">Laporan Kunjungan</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <ul>

                            <li class="menu-title"><span>Produk dan Transaksi</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Manajemen Produk</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Tambah Produk</a></li>
                                    <li><a href="{{ url('/') }}">Daftar Produk</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Kategori Produk</a></li>

                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Produk Manufaktur</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Daftar Bahan Baku</a></li>
                                    <li><a href="{{ url('/') }}">Daftar Bahan 1/2 Jadi</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Pembelian &<br>Produksi</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Pembelian Barang Jadi</a></li>
                                    <li><a href="{{ url('/') }}">Buat Produk Manufaktur</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Buat Produk 1/2 Jadi</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Pembelian Bahan Baku</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Daftar Supplier</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Transfer Stok</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Transfer Stok <br> Barang Jadi</a></li>
                                    <li><a href="{{ url('/') }}">Transfer Stok <br> Bahan Baku</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Konversi Bahan/Produksi</a>
                                    </li>

                                </ul>
                            </li>


                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Penyesuaian</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Daftar Penyesuaian</a></li>
                                    <li><a href="{{ url('/') }}">Penyesuaian Barang Jadi</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Penyesuaian Barang 1/2 Jadi</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Penyesuaian Bahan Baku</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Kategori Penyesuaian</a>
                                    </li>
                                    <li><a href="{{ url('/') }}">Stock Opname</a>
                                    </li>

                                </ul>
                            </li>




                        </ul>
                    </li>



                    <li>
                        <ul>

                            <li class="menu-title"><span>Utang dan Asset</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Utang dan Piutang</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Daftar Utang</a></li>
                                    <li><a href="{{ url('/') }}">Daftar Piutang</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Penyusutan</span>
                                </a>

                            </li>


                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Pengeluaran</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Daftar Pengeluaran</a></li>
                                    <li><a href="{{ url('/') }}">Kategori Pengeluaran</a>
                                    </li>
                                </ul>
                            </li>



                        </ul>
                    </li>


                    <li>
                        <ul>

                            <li class="menu-title"><span>Extra</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>QR Meja</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">QR Meja Reservasi</a></li>
                                    <li><a href="{{ url('/') }}">Cetak QR Meja</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Dompet Digital</span>
                                </a>

                            </li>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Landing Page</span>
                                </a>

                            </li>


                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Toko Online</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Lihat Toko Online</a></li>
                                    <li><a href="{{ url('/') }}">Pengaturan Toko Online</a>
                                    </li>
                                    <li><a href="{{ url('journal_add') }}">Pengaturan Pembayaran</a></li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Absensi Staff</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Laporan Kunjungan</a></li>
                                    <li><a href="{{ url('/') }}">Laporan Absensi Bulanan</a>
                                    </li>
                                    <li><a href="{{ url('journal_add') }}">Laporan Absensi Harian</a></li>
                                </ul>
                            </li>


                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Manajemen Staff</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Data Staff & Karyawan</a></li>

                                </ul>
                            </li>



                        </ul>
                    </li>

                    <li>
                        <ul>

                            <li class="menu-title"><span>Pengaturan</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Pengaturan Aplikasi</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Pengaturan Perusahaan</a></li>
                                    <li><a href="{{ url('/') }}">Pengaturan Kode Rekening</a>
                                    </li>
                                    <li><a href="{{ url('journal_add') }}">Pengaturan Kas Kecil</a></li>
                                    <li><a href="{{ url('journal_add') }}">Generate Saldo Awal</a></li>
                                    <li><a href="{{ url('journal_add') }}">Hapus Saldo Awal</a></li>
                                    <li><a href="{{ url('journal_add') }}">Pengaturan Pembayaran</a></li>
                                    <li><a href="{{ url('journal_add') }}">Pengaturan Printer</a></li>
                                    <li><a href="{{ url('journal_add') }}">Pengaturan Akun</a></li>
                                    <li><a href="{{ url('journal_add') }}">Pengaturan Toko Online</a></li>
                                    <li><a href="{{ url('journal_add') }}">Reset Akun</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <ul>
                            <li>
                                <a href="{{ url('dashboard') }}">
                                    <i class="isax isax-element-45"></i><span>Go Premium</span>

                                </a>

                            </li>
                        </ul>
                    </li>

                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-shapes5"></i><span>Katalog Saya</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('journal_add') }}">Lihat Katalog</a></li>
                                    <li><a href="{{ url('/') }}">History Transaksi</a>
                                    </li>

                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <ul>
                            <li>
                                <a href="{{ url('dashboard') }}">
                                    <i class="isax isax-element-45"></i><span>Bantuan</span>

                                </a>

                            </li>
                        </ul>
                    </li>

                    <li>
                        <ul>
                            <li>
                                <a href="{{ url('dashboard') }}">
                                    <i class="isax isax-element-45"></i><span>Notifikasi</span>

                                </a>

                            </li>
                        </ul>
                    </li>


                </ul>

            </div>
        </div>
    </div>
</div>
<!-- Sidenav Menu End -->
