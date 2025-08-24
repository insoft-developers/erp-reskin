<!-- Sidenav Menu Start -->
@php
   $segment = request()->segment(1); 
   $segment2 = request()->segment(2);
   $segment3 = request()->segment(3);   
@endphp

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
            <a href="{{ url('/') }}" class="logo logo-normal">
                <img class="logo-pakai" src="{{ asset('reskin') }}/assets/img/logo.png" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="logo-small">
                <img src="{{ asset('reskin') }}/assets/img/logo-icon.png" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-logo">
                <img src="{{ asset('reskin') }}/assets/img/logo.png" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-small">
                <img src="{{ asset('reskin') }}/assets/img/logo-icon.png" alt="Logo">
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
                                <a href="javascript:void(0);" class="{{ ($segment === 'journal_add' || $segment === 'journal_list' || $segment === 'journal_edit') ? 'active subdrop' : '' }}">
                                    <i class="isax isax-coin-15"></i><span>Jurnal</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a class="{{ $segment === 'journal_add' ? 'active' : '' }}" href="{{ url('journal_add') }}">Buat Jurnal Baru</a></li>
                                    <li><a class="{{ $segment === 'journal_list' ? 'active' : '' }}" href="{{ url('journal_list') }}">Daftar Jurnal</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>




                    <li>
                        <ul>

                            <li class="menu-title"><span>Penjualan dan Pelanggan</span></li>
                            <li>
                                <a href="{{ url('pos/index') }}">
                                    <i class="isax isax-bag-tick-25"></i><span>Kasir (Point Of Sales)</span>

                                </a>

                            </li>

                            <li>
                                <a href="{{ url('manajemen-pesanan') }}">
                                    <i class="isax isax-element-45"></i><span>Kelola Pesanan</span>

                                </a>

                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ ($segment === 'storefront' || $segment === 'landing-page') ? 'active subdrop' : '' }}">
                                    <i class="isax isax-shop5"></i><span>Toko Online</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ route('storefront', session('username')) }}">Lihat Toko Online</a>
                                    </li>
                                    <li><a class="{{ $segment === 'storefront' ? 'active': '' }}" href="{{ route('storefront-setting') }}">Pengaturan Toko Online</a></li>
                                    <li><a class="{{ $segment === 'landing-page' ? 'active': '' }}" href="{{ url('landing-page') }}">Landing Page</a></li>

                            </li>


                        </ul>
                    </li>

                    <li class="submenu">
                        <a href="javascript:void(0);" class="{{ $segment === 'invoice' ? 'active subdrop' : '' }}">
                            <i class="isax isax-receipt-item5"></i><span>Faktur dan Tagihan</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a class="{{ ($segment2 === 'invoice' && $segment3 === 'create') ? 'active' : '' }}" href="{{ url('invoice/invoice/create') }}">Buat Faktur</a></li>
                            <li><a class="{{ ($segment2 === 'invoice' && $segment3 === '') ? 'active' : '' }}" href="{{ url('invoice/invoice') }}">Daftar Faktur</a></li>
                            <li><a class="{{ ($segment2 === 'invoice' && $segment3 === 'client') ? 'active' : '' }}" href="{{ url('invoice/client') }}">Daftar Klien</a></li>
                        </ul>
                    </li>

                    <li class="submenu">
                        <a href="javascript:void(0);" class="{{ $segment === 'crm' ? 'active subdrop' : '' }}">
                            <i class="isax isax-profile-2user5"></i><span>Pelanggan</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a class="{{ ($segment === 'crm' && $segment === 'customer' ) ? 'active' : ''  }}" href="{{ url('crm/customer') }}">Data Pembeli</a></li>
                            <li><a class="{{ ($segment === 'crm' && $segment === 'followup' ) ? 'active' : ''  }}" href="{{ url('crm/followup') }}">Followup Upselling</a>
                            </li>
                            <li><a class="{{ ($segment === 'crm' && $segment === 'discount' ) ? 'active' : ''  }}" href="{{ url('crm/discount') }}">List Diskon</a></li>
                        </ul>
                    </li>






                    <li class="submenu">
                        <a href="javascript:void(0);" class="{{ ($segment === 'laporan' && $segment2 === 'penjualan-advance') ? 'active subdrop' : '' }}">
                            <i class="isax isax-chart-35"></i><span>Analisa Penjualan</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a class="{{ ($segment === 'laporan' && $segment2 === 'penjualan-advance') ? 'active' : '' }}" href="{{ route('laporan.penjualan.advance.index') }}">Laporan Penjualan
                                    Produk</a></li>
                        </ul>
                    </li>
                </ul>
                </li>



                <li>
                    <ul>

                        <li class="menu-title"><span>Produk dan Stok</span></li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'product' || $segment === 'product_category' ) ? 'active subdrop' : '' }}">
                                <i class="isax isax-password-check5"></i><span>Daftar Produk</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ ($segment === 'product' && $segment2 === 'create' ) ? 'active' : '' }}" onclick="add_product_module()" href="javascript:void(0);">Tambah Produk &
                                        Jasa</a></li>
                                <li><a class="{{ ( $segment === 'product' && $segment2 === '') ? 'active' : '' }}" href="{{ url('product') }}">Daftar Produk & Jasa</a>
                                </li>
                                <li><a class="{{ ($segment === 'product_category') ? 'active' : '' }}" href="{{ url('product_category') }}">Kategori Produk & Jasa</a></li>

                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'adjustment' && $segment2 === 'stock-opname') ? 'active subdrop' : '' }}">
                                <i class="isax isax-building5"></i><span>Manajemen Stok</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ ($segment === 'adjustment' && $segment2 === 'stock-opname') ? 'active' : '' }}" href="{{ url('adjustment/stock-opname') }}">Stock Opname</a>
                                </li>
                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ $segment === 'adjustment' ? 'active subdrop' : '' }}">
                                <i class="isax isax-convert-card5"></i><span>Koreksi Stok</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                               
                                <li><a class="{{ ($segment === 'adjustment' && is_null($segment2) && is_null($segment3)) ? 'active' : '' }}" href="{{ url('adjustment') }}">Daftar Penyesuaian</a></li>
                                <li><a class="{{ ($segment === 'adjustment' && $segment2 ===  'create' && is_null($segment3)) ? 'active' : '' }}" href="{{ url('adjustment/create') }}">Penyesuaian Barang Jadi</a>
                                </li>
                                <li><a class="{{ ($segment === 'adjustment' && $segment2 ===  'create' && $segment3 === 'inter-product') ? 'active' : '' }}" href="{{ url('adjustment/create/inter-product') }}">Penyesuaian Barang 1/2
                                        Jadi</a>
                                </li>
                                <li><a class="{{ ($segment === 'adjustment' && $segment2 ===  'create' && $segment3 === 'material') ? 'active' : '' }}" href="{{ url('adjustment/create/material') }}">Penyesuaian Bahan Baku</a>
                                </li>
                                <li><a class="{{ ($segment === 'adjustment' && $segment2 ===  'category' && is_null($segment3)) ? 'active' : '' }}" href="{{ url('adjustment/category') }}">Kategori Penyesuaian</a>
                                </li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'transfer-stock' || $segment === 'converse') ? 'active subdrop' : '' }}">
                                <i class="isax isax-lifebuoy5"></i><span>Transfer Stok</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ ($segment === 'transfer-stock' && $segment2 === 'product') ? 'active' : '' }}" href="{{ url('transfer-stock/product') }}">Transfer Stok <br> Barang Jadi</a>
                                </li>
                                <li><a class="{{ ($segment === 'transfer-stock' && $segment2 === 'material') ? 'active' : '' }}" href="{{ url('transfer-stock/material') }}">Transfer Stok <br> Bahan Baku</a>
                                </li>
                                <li><a class="{{ ($segment === 'converse' && is_null($segment2)) ? 'active' : '' }}" href="{{ url('converse') }}">Konversi Bahan/Produksi</a>
                                </li>

                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'product_purchase' || $segment === 'material_purchase' || $segment === 'main_supplier' ) ? 'active subdrop' : '' }}">
                                <i class="isax isax-moneys5"></i><span>Pembelian</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ $segment === 'product_purchase' ? 'active' : '' }}" href="{{ url('product_purchase') }}">Pembelian Barang Jadi</a></li>
                                <li><a class="{{ $segment === 'material_purchase' ? 'active' : '' }}" href="{{ url('material_purchase') }}">Pembelian Bahan Baku</a>
                                </li>
                                <li><a class="{{ $segment === 'main_supplier' ? 'active' : '' }}" href="{{ url('main_supplier') }}">Daftar Supplier</a>
                                </li>
                            </ul>
                        </li>

                        

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'inter_product' || $segment === 'main_material' || $segment === 'product_manufacture' || $segment === 'inter_purchase' ) ? 'active subdrop' : '' }}">
                                <i class="isax isax-card-tick-15"></i><span>Aktifitas Produksi</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ $segment === 'main_material' ? 'active' : '' }}" href="{{ url('main_material') }}">Daftar Bahan Baku</a></li>
                                <li><a class="{{ $segment === 'inter_product' ? 'active' : '' }}" href="{{ url('inter_product') }}">Daftar Bahan 1/2 Jadi</a>
                                </li>
                                <li><a class="{{ $segment === 'product_manufacture' ? 'active' : '' }}" href="{{ url('product_manufacture') }}">Buat Produk Manufaktur</a>
                                </li>
                                <li><a class="{{ $segment === 'inter_purchase' ? 'active' : '' }}" href="{{ url('inter_purchase') }}">Buat Produk 1/2 Jadi</a>
                                </li>
                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'laporan' && $segment2 === 'stock') ? 'active subdrop':'' }}">
                                <i class="isax isax-chart-35"></i><span>Analisa Stok</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ ($segment === 'laporan' && $segment2 === 'stock') ? 'active subdrop':'' }}" href="{{ route('laporan.stock.index') }}">Laporan Stok</a></li>

                            </ul>
                        </li>
                    </ul>
                </li>



                <li>
                    <ul>

                        <li class="menu-title"><span>Keuangan</span></li>
                        
                        <li>
                            <a class="{{ $segment === 'payment-method-setting' ? 'active' : '' }}" href="{{ url('payment-method-setting') }}">
                                <i class="isax isax-money5"></i><span>Kas dan Bank</span>
                            </a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);">
                                <i class="isax isax-money-send5"></i><span>Pengeluaran</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ ($segment === 'expense' && is_null($segment2) ) ? 'active' : '' }}" href="{{ url('expense') }}">Daftar Pengeluaran</a></li>
                                <li><a class="{{ ($segment === 'expense' && $segment === 'category' ) ? 'active' : '' }}" href="{{ url('expense/category') }}">Kategori Pengeluaran</a>
                                </li>
                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'utang' || $segment === 'piutang') ? 'active subdrop' : '' }}">
                                <i class="isax isax-document-text5"></i><span>Utang dan Piutang</span>
                                <span class="menu-arrow"></span>

                            </a>
                            <ul>
                                <li><a class="{{ $segment === 'utang' ? 'active' : '' }}" href="{{ url('utang') }}">Daftar Utang</a></li>
                                <li><a class="{{ $segment === 'piutang' ? 'active' : '' }}" href="{{ url('piutang') }}">Daftar Piutang</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ url('penyusutan') }}">
                                <i class="isax isax-book5"></i><span>Penyusutan</span>
                            </a>

                        </li>




                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ 
                                    $segment === 'profit_loss' || 
                                    $segment === 'balance' ||
                                    $segment === 'trial_balance'||
                                    $segment === 'rekapitulasi-v2-harian' ||
                                    $segment === 'pengeluaran' ||
                                    $segment === 'general_ledger' ||
                                    ($segment === 'laporan' && $segment2 === 'pajak') ? 'active subdrop' : ''

                                }}">
                                <i class="isax isax-chart-35"></i><span>Laporan Keuangan</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                {{-- <li><a href="{{ url('journal_report') }}">Rekapitulasi Jurnal</a>
                                    </li> --}}


                                <li><a class="{{ $segment === 'profit_loss' ? 'active' : '' }}" href="{{ url('profit_loss') }}">Laporan Laba Rugi</a></li>
                                <li><a class="{{ $segment === 'balance' ? 'active' : '' }}" href="{{ url('balance') }}">Laporan Neraca</a></li>
                                <li><a class="{{ $segment === 'general_ledger' ? 'active' : '' }}" href="{{ url('general_ledger') }}">Buku Besar</a></li>
                                <li><a class="{{ $segment === 'trial_balance' ? 'active' : '' }}" href="{{ url('trial_balance') }}">Neraca Saldo</a></li>


                                <li><a class="{{ ($segment === 'laporan' && $segment2 === 'pajak') ? 'active' : '' }}" href="{{ route('laporan.pajak.index') }}">Laporan Pajak</a></li>
                                <li><a class="{{ $segment === 'rekapitulasi-v2-harian' ? 'active' : '' }}" href="{{ url('rekapitulasi-v2-harian') }}">Rekapitulasi Harian</a></li>
                                <li><a class="{{ $segment === 'pengeluaran' ? 'active' : '' }}" href="{{ url('pengeluaran') }}">Pengeluaran Outlet</a></li>
                                {{-- <li><a href="{{ route('laporan.stock.index') }}">Laporan Stok</a></li> --}}
                                {{-- <li><a href="{{ route('laporan.absensi.index') }}">Laporan Absensi</a></li> --}}
                                {{-- <li><a href="{{ route('visit.index') }}">Laporan Kunjungan</a></li> --}}
                            </ul>
                        </li>
                    </ul>
                </li>


                <li>
                    <ul>

                        <li class="menu-title"><span>Operasional & Tim</span></li>


                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ $segment === 'staff' ? 'active subdrop' : '' }}">
                                <i class="isax isax-profile-2user5"></i><span>Cabang dan Staff</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ $segment === 'staff' ? 'active' : '' }}" href="{{ url('staff') }}">Data Staff & Karyawan</a></li>

                            </ul>
                        </li>



                        <li class="submenu">
                            <a href="javascript:void(0);" class="
                            {{
                                (($segment === 'report' && $segment2 === 'visit' ) ||
                                ($segment === 'laporan' && $segment2 === 'absensi') ||
                                ($segment === 'laporan' && $segment2 === 'absensi-by-date')) ? 'active subdrop' : ''
                            }}
                            ">
                                <i class="isax isax-finger-scan5"></i><span>Absensi dan Tim</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ ($segment ==='report' && $segment2 === 'visit') ? 'active' : '' }}" href="{{ url('report/visit') }}">Laporan Kunjungan</a></li>
                                <li><a class="{{ ($segment ==='laporan' && $segment2 === 'absensi') ? 'active' : '' }}" href="{{ url('laporan/absensi') }}">Laporan Absensi Bulanan</a>
                                </li>
                                <li><a class="{{ ($segment ==='laporan' && $segment2 === 'absensi-by-date') ? 'active' : '' }}" href="{{ url('laporan/absensi-by-date') }}">Laporan Absensi Harian</a></li>
                            </ul>
                        </li>




                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ $segment === 'qr-code' || $segment === 'print-qr-code' ? 'active subdrop' : ''  }}">
                                <i class="isax isax-scan-barcode5"></i><span>QR Code Meja</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ $segment === 'qr-code' ? 'active' : '' }}" href="{{ url('qr-code') }}">QR Meja Reservasi</a></li>
                                <li><a class="{{ $segment === 'print-qr-code' ? 'active' : '' }}" href="{{ url('print-qr-code') }}">Cetak QR Meja</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ url('wallet-logs') }}">
                                <i class="isax isax-empty-wallet5"></i><span>Dompet Digital</span>
                            </a>

                        </li>


                    </ul>
                </li>



                <li>
                    <ul>

                        <li class="menu-title"><span>Pengaturan & Bantuan</span></li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{
                                (
                                    $segment === 'company_setting' ||
                                    $segment === 'account_setting' ||
                                    $segment === 'petty_cash' ||
                                    $segment === 'generate_opening_balance'||
                                    $segment === 'initial_delete' ||
                                    $segment === 'payment-method-setting' ||
                                    $segment === 'printer-setting' ||
                                    $segment === 'account-profile-settings' ||
                                    $segment === 'setting' ||
                                    ($segment === 'storefront' && $segment2 === 'setting')
                                
                                ) ? 'active subdrop' : ''
                            
                            }}">
                                <i class="isax isax-setting5"></i><span>Pengaturan Aplikasi</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ $segment === 'company_settting' ? 'active' : '' }}" href="{{ url('company_setting') }}">Pengaturan Perusahaan</a></li>
                                <li><a class="{{ $segment === 'account_setting' ? 'active' : '' }}" href="{{ url('account_setting') }}">Pengaturan Kode Rekening</a>
                                </li>
                                <li><a class="{{ $segment === 'petty_cash' ? 'active' : '' }}" href="{{ url('petty_cash') }}">Pengaturan Kas Kecil</a></li>
                                <li><a class="{{ $segment === 'generate_opening_balance' ? 'active' : '' }}" href="{{ url('generate_opening_balance') }}">Generate Saldo Awal</a></li>
                                <li><a class="{{ $segment === 'initial_delete' ? 'active' : '' }}" href="{{ url('initial_delete') }}">Hapus Saldo Awal</a></li>
                                <li><a class="{{ $segment === 'payment-method-setting' ? 'active' : '' }}" href="{{ url('payment-method-setting') }}">Pengaturan Pembayaran</a></li>
                                <li><a class="{{ $segment === 'printer-setting' ? 'active' : '' }}" href="{{ url('printer-setting') }}">Pengaturan Printer</a></li>
                                <li><a class="{{ $segment === 'account-profile-settings' ? 'active' : '' }}" href="{{ url('account-profile-settings') }}">Pengaturan Akun</a></li>
                                <li><a class="{{ ($segment === 'storefront' && $segment2 === 'setting') ? 'active' : '' }}" href="{{ url('storefront/setting') }}">Pengaturan Toko Online</a></li>
                                <li><a class="{{ $segment === 'settting' ? 'active' : '' }}" href="{{ url('setting') }}">Reset Akun</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ ($segment === 'premium' || $segment === 'feature-request' || $segment === 'notification') ? 'active subdrop' : '' }}">
                                <i class="isax isax-message-question5"></i><span>Pusat Bantuan</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="https://help.lihai.id" target="_blank">Bantuan dan Tutorial</a></li>
                                <li class="{{ $segment ==='premium' ? 'active' : '' }}"><a href="{{ url('premium') }}">Paket Langganan</a>
                                </li>
                                <li><a class="{{ $segment === 'feature-request' ? 'active' : '' }}" href="{{ url('feature-request') }}">Permintaan Fitur Baru</a>
                                </li>
                                <li><a class="{{ $segment ==='notification' ? 'active': '' }}" href="{{ route('notification.index') }}">Notifikasi</a>
                                </li>



                            </ul>
                        </li>


                    </ul>
                </li>




                {{-- <li>
                        <ul>

                            <li class="menu-title"><span>Laporan dan Analisa</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-chart-35"></i><span>Laporan</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ route('laporan.penjualan.advance.index') }}">Laporan Penjualan</a></li>
                                    <li><a href="{{ url('journal_report') }}">Rekapitulasi Jurnal</a>
                                    </li>
                                    <li><a href="{{ url('general_ledger') }}">Buku Besar</a></li>
                                    <li><a href="{{ url('trial_balance') }}">Neraca Saldo</a></li>
                                    <li><a href="{{ url('profit_loss') }}">Laporan Laba Rugi</a></li>
                                    <li><a href="{{ url('balance') }}">Laporan Neraca</a></li>
                                    
                                    <li><a href="{{ url('rekapitulasi-v2-harian') }}">Rekapitulasi Harian</a></li>
                                    <li><a href="{{ route('laporan.pajak.index') }}">Laporan Pajak</a></li>
                                    <li><a href="{{ route('laporan.stock.index') }}">Laporan Stok</a></li>
                                    <li><a href="{{ route('laporan.absensi.index') }}">Laporan Absensi</a></li>
                                    <li><a href="{{ route('visit.index') }}">Laporan Kunjungan</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li> --}}



                {{-- <li>
                        <ul>
                            <li>
                                <a href="{{ url('feature-request') }}">
                                    <i class="isax isax-star-15"></i><span>Permintaan Fitur Baru</span>

                                </a>

                            </li>
                        </ul>
                    </li> --}}


                {{-- <li>
                        <ul>

                            <li class="menu-title"><span>Produk dan Transaksi</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-password-check5"></i><span>Manajemen Produk</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a onclick="add_product_module()" href="javascript:void(0);">Tambah Produk</a></li>
                                    <li><a href="{{ url('product') }}">Daftar Produk</a>
                                    </li>
                                    <li><a href="{{ url('product_category') }}">Kategori Produk</a></li>

                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-card-tick-15"></i><span>Produk Manufaktur</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('main_material') }}">Daftar Bahan Baku</a></li>
                                    <li><a href="{{ url('inter_product') }}">Daftar Bahan 1/2 Jadi</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-moneys5"></i><span>Pembelian &<br>Produksi</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('product_purchase') }}">Pembelian Barang Jadi</a></li>
                                    <li><a href="{{ url('product_manufacture') }}">Buat Produk Manufaktur</a>
                                    </li>
                                    <li><a href="{{ url('inter_purchase') }}">Buat Produk 1/2 Jadi</a>
                                    </li>
                                    <li><a href="{{ url('material_purchase') }}">Pembelian Bahan Baku</a>
                                    </li>
                                    <li><a href="{{ url('main_supplier') }}">Daftar Supplier</a>
                                    </li>
                                </ul>
                            </li>

                            


                            

                        </ul>
                    </li> --}}

                {{-- <li>
                        <ul>

                            <li class="menu-title"><span>Utang dan Asset</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-document-text5"></i><span>Utang dan Piutang</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('utang') }}">Daftar Utang</a></li>
                                    <li><a href="{{ url('piutang') }}">Daftar Piutang</a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="{{ url('penyusutan') }}">
                                    <i class="isax isax-book5"></i><span>Penyusutan</span>
                                </a>

                            </li>


                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-money-send5"></i><span>Pengeluaran</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('expense') }}">Daftar Pengeluaran</a></li>
                                    <li><a href="{{ url('expense/category') }}">Kategori Pengeluaran</a>
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
                                    <i class="isax isax-barcode5"></i><span>QR Meja</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('qr-code') }}">QR Meja Reservasi</a></li>
                                    <li><a href="{{ url('print-qr-code') }}">Cetak QR Meja</a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="{{ url('wallet-logs') }}">
                                    <i class="isax isax-empty-wallet5"></i><span>Dompet Digital</span>
                                </a>

                            </li>
                            <li>
                                <a href="{{ url('landing-page') }}">
                                    <i class="isax isax-designtools5"></i><span>Landing Page</span>
                                </a>

                            </li>


                            
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-finger-scan5"></i><span>Absensi Staff</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('report/visit') }}">Laporan Kunjungan</a></li>
                                    <li><a href="{{ url('laporan/absensi') }}">Laporan Absensi Bulanan</a>
                                    </li>
                                    <li><a href="{{ url('laporan/absensi-by-date') }}">Laporan Absensi Harian</a></li>
                                </ul>
                            </li>


                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-profile-2user5"></i><span>Manajemen Staff</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('staff') }}">Data Staff & Karyawan</a></li>

                                </ul>
                            </li>



                        </ul>
                    </li>

                    <li>
                        <ul>

                            <li class="menu-title"><span>Pengaturan</span></li>

                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-setting5"></i><span>Pengaturan Aplikasi</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('company_setting') }}">Pengaturan Perusahaan</a></li>
                                    <li><a href="{{ url('account_setting') }}">Pengaturan Kode Rekening</a>
                                    </li>
                                    <li><a href="{{ url('petty_cash') }}">Pengaturan Kas Kecil</a></li>
                                    <li><a href="{{ url('generate_opening_balance') }}">Generate Saldo Awal</a></li>
                                    <li><a href="{{ url('initial_delete') }}">Hapus Saldo Awal</a></li>
                                    <li><a href="{{ url('payment-method-setting') }}">Pengaturan Pembayaran</a></li>
                                    <li><a href="{{ url('printer-setting') }}">Pengaturan Printer</a></li>
                                    <li><a href="{{ url('account-profile-settings') }}">Pengaturan Akun</a></li>
                                    <li><a href="{{ url('storefront/setting') }}">Pengaturan Toko Online</a></li>
                                    <li><a href="{{ url('setting') }}">Reset Akun</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <ul>
                            <li>
                                <a href="{{ url('premium') }}">
                                    <i class="isax isax-ticket-25"></i><span>Go Premium</span>

                                </a>

                            </li>
                        </ul>
                    </li>

                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);">
                                    <i class="isax isax-card5"></i><span>Katalog Saya</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    <li><a href="{{ url('katalog-randu') }}">Lihat Katalog</a></li>
                                    <li><a href="{{ url('katalog-randu/transaction') }}">History Transaksi</a>
                                    </li>

                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <ul>
                            <li>
                                <a href="{{ url('dashboard') }}">
                                    <i class="isax isax-message-question5"></i><span>Bantuan</span>

                                </a>

                            </li>
                        </ul>
                    </li>

                    <li>
                        <ul>
                            <li>
                                <a href="{{ route('notification.index') }}">
                                    <i class="isax isax-message-notif5"></i><span>Notifikasi</span>

                                </a>

                            </li>
                        </ul>
                    </li> --}}


                </ul>

            </div>
        </div>
    </div>
</div>
<!-- Sidenav Menu End -->
