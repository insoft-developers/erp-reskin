@extends('master')

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('setting') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">Pengaturan Perusahaan</li>
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
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Pengaturan</h5>


                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <table class="table table-hover">
                                        <tr onclick="on_company_setting_click()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Pengaturan
                                                        Perusahaan</span></strong><br><span
                                                    class="report-menu-subtitle">Pengaturan Profil Perusahaan</span></td>
                                        </tr>
<tr onclick="on_initial_capital()" style="display: none;">
    <td class="menu-report-row"><strong><span class="report-menu-title">Pengaturan
                Modal Awal</span></strong><br><span
            class="report-menu-subtitle">Atur Modal Awal Perusahaan</span></td>
</tr>

                                        <tr onclick="on_account_setting_click()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Pengaturan
                                                        Kode Rekening</span></strong><br><span
                                                    class="report-menu-subtitle">Daftar Kode Rekening Akuntansi</span></td>
                                        </tr>
                                        <tr onclick="on_petty_cash_click()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Pengaturan
                                                        Kas Kecil</span></strong><br><span
                                                    class="report-menu-subtitle">Pengaturan Kas Kecil/Kembalian Kasir</span>
                                            </td></a>
                                        </tr>
                                        <tr onclick="on_opening_balance_click()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Generate
                                                        Saldo Awal</span></strong><br><span
                                                    class="report-menu-subtitle">Buat Saldo Awal Akuntansi</span></td></a>
                                        </tr>
                                        <tr onclick="on_delete_initial_click()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Hapus Saldo
                                                        Awal</span></strong><br><span class="report-menu-subtitle">Hapus
                                                    Saldo Awal Akuntansi</span></td></a>
                                        </tr>
                                        <tr onclick="on_payment_method_setting()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Pengaturan
                                                        Pembayaran
                                                    </span></strong><br><span class="report-menu-subtitle">Pengaturan
                                                    Pembayaran</span></td></a>
                                        </tr>
                                        <tr onclick="on_printer_setting_setting()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Pengaturan
                                                        Printer Kasir
                                                    </span></strong><br><span class="report-menu-subtitle">Pengaturan Cetak
                                                    Struk Untuk Kasir POS</span></td></a>
                                        </tr>
                                        <tr onclick="redirect_to_account_settings_page()">
                                            <td class="menu-report-row"><strong><span class="report-menu-title">Pengaturan
                                                        Akun</span></strong><br>
                                                <span class="report-menu-subtitle">Ganti Nama & Password Akun</span>
                                            </td></a>
                                        </tr>
                                        <tr onclick="redirect_to_storefront_settings()">
                                            <td class="menu-report-row">
                                                <strong><span class="report-menu-title">Pengaturan Toko
                                                        Online</span></strong><br>
                                                <span class="report-menu-subtitle">Setting Thema, Banner, Pengiriman</span>
                                            </td>
                                        </tr>
                                        <tr onclick="alert_reset_account()">
                                            <td class="menu-report-row">
                                                <strong><span class="report-menu-title">Reset Account</span></strong><br>
                                                <span class="report-menu-subtitle">Melakukan reset akun, mulai dari
                                                    penghapusan semua data transaksi</span>
                                            </td>
                                        </tr>

                                    </table>

                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- [Recent Orders] end -->
                    <!-- [] start -->
                </div>

            </div>
            <!-- [ Main Content ] end -->

        </div>
    </main>


    <div class="modal fade" id="resetAccountModal" tabindex="-1" aria-labelledby="resetAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetAccountModalLabel">Syarat dan Ketentuan Reset Akun Randu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Dengan menggunakan fitur reset akun pada platform Randu, Anda memahami dan menyetujui hal-hal
                        berikut:</p>
                    <h6>Tujuan Reset Akun</h6>
                    <p>Fitur reset akun bertujuan untuk memberikan opsi kepada pengguna untuk menghapus seluruh data yang
                        terkait dengan akun mereka, termasuk tetapi tidak terbatas pada data transaksi, inventori, dan
                        laporan keuangan.</p>
                    <h6>Dampak Penghapusan Data</h6>
                    <p>Semua data dalam akun akan dihapus secara permanen dan tidak dapat dipulihkan. Data yang dihapus
                        meliputi namun tidak terbatas pada: catatan transaksi, jurnal akuntansi, stok produk, bahan baku,
                        data penjualan, riwayat pembelian, utang/piutang, faktur, dan data lainnya yang terkait dengan
                        operasional bisnis pengguna.</p>
                    <h6>Data yang Tidak Terhapus</h6>
                    <p>Data berikut tidak akan terhapus meskipun proses reset akun dilakukan: Produk (barang jadi) beserta
                        kategori dan komposisinya, Produk setengah jadi beserta kategori dan komposisinya, Bahan baku yang
                        telah tercatat dalam sistem, Randu Wallet Log beserta saldo yang belum dicairkan.</p>
                    <h6>Tanggung Jawab Pengguna</h6>
                    <p>Pengguna sepenuhnya bertanggung jawab untuk memastikan bahwa tindakan reset akun sesuai dengan
                        kebutuhan dan kehendak mereka. Randu tidak bertanggung jawab atas kehilangan data atau kerugian yang
                        timbul akibat penggunaan fitur reset akun.</p>
                    <h6>Proses Reset Akun</h6>
                    <p>Reset akun dilakukan berdasarkan permintaan langsung dari pengguna melalui sistem yang telah
                        disediakan. Pengguna diwajibkan membaca dan menyetujui Syarat dan Ketentuan ini sebelum melanjutkan
                        proses reset akun.</p>
                    <h6>Pengecualian Tanggung Jawab Randu</h6>
                    <p>Randu tidak bertanggung jawab atas dampak hilangnya data, termasuk namun tidak terbatas pada kerugian
                        finansial, kehilangan pelanggan, atau gangguan operasional.</p>
                    <h6>Persetujuan</h6>
                    <p>Dengan melanjutkan proses reset akun, pengguna dianggap telah membaca, memahami, dan menyetujui
                        Syarat dan Ketentuan ini tanpa paksaan dari pihak manapun.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeCheckbox">
                        <label class="form-check-label" for="agreeCheckbox"
                            style="font-weight: bold; font-size: 14px; color: black">
                            Saya setuju dengan syarat dan ketentuan
                        </label>
                    </div>
                    <!-- Kotak OTP dengan default d-none -->
                    <div class="mt-3 d-none" id="otpContainer">
                        <label for="otpCode" class="form-label"
                            style="font-weight: bold; font-size: 14px; color: black">Masukkan Kode OTP</label>
                        <input type="text" id="otpCode" style="max-width: 250px" class="form-control"
                            maxlength="6" placeholder="Masukkan kode OTP">
                        <p id="resendOtpText" class="mt-2" style="cursor: pointer; color: gray;">Kirim ulang kode OTP
                            (30)</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Tombol default disembunyikan setelah OTP -->
                    <button type="button" class="btn btn-secondary" id="cancelReset"
                        data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-danger" id="confirmReset" disabled>Iya Setel Ulang</button>
                    <!-- Tombol konfirmasi OTP -->
                    <button type="button" class="btn btn-primary d-none" id="confirmOtp">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        let resendInterval; // Variabel untuk interval hitungan mundur
        let resendCounter = 30; // Hitungan awal mundur

        // Fungsi untuk memulai hitungan mundur kirim ulang OTP
        function startResendCountdown() {
            const resendText = $('#resendOtpText');
            resendText.css({
                color: 'gray',
                pointerEvents: 'none'
            }); // Set warna menjadi abu-abu

            resendCounter = 30; // Reset hitungan mundur
            resendText.text(`Kirim ulang kode OTP (${resendCounter})`);

            // Mulai hitungan mundur
            resendInterval = setInterval(() => {
                resendCounter--;
                resendText.text(`Kirim ulang kode OTP (${resendCounter})`);

                if (resendCounter <= 0) {
                    clearInterval(resendInterval); // Hentikan interval jika hitungan mencapai nol
                    resendText.css({
                        color: 'black',
                        pointerEvents: 'auto'
                    }); // Aktifkan kembali dengan warna hitam
                    resendText.text('Kirim ulang kode OTP');
                }
            }, 1000); // Interval 1 detik
        }

        // Event listener untuk checkbox persetujuan
        $('#agreeCheckbox').on('change', function() {
            $('#confirmReset').prop('disabled', !this.checked);
        });

        // Event listener untuk tombol "Iya Setel Ulang"
        $('#confirmReset').on('click', function() {
            fetch('/v1/account-reset', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (response.ok) {
                        notyf.open({
                            type: 'success',
                            message: 'Silahkan tunggu, kode OTP sudah kami kirim ke nomor whatsapp Anda',
                        });

                        // Sukses, sembunyikan checkbox dan ganti dengan kotak OTP
                        $('.form-check').addClass('d-none'); // Sembunyikan checkbox
                        $('#otpContainer').removeClass('d-none'); // Tampilkan input OTP
                        $('#cancelReset, #confirmReset').addClass('d-none');
                        $('#confirmOtp').removeClass('d-none');
                        startResendCountdown(); // Mulai hitungan mundur kirim ulang
                    } else {
                        throw new Error('Gagal memanggil API');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // Event listener untuk tombol konfirmasi OTP
        $('#confirmOtp').on('click', function() {
            const otpCode = $('#otpCode').val();

            if (!otpCode) {
                alert('Kode OTP tidak boleh kosong!');
                return;
            }

            axios(`/v1/account-reset-by-otp?otp=${otpCode}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (response.status) {
                        $('#resetAccountModal').modal('hide'); // Tutup modal dengan jQuery
                        setTimeout(() => {
                            notyf.open({
                                type: 'success',
                                message: response.data.message,
                            });
                            $('.form-check').removeClass('d-none'); // Tampilkan kembali checkbox
                            $('#agreeCheckbox').prop('checked', false);
                            $('#otpContainer').addClass('d-none'); // Sembunyikan input OTP
                            $('#otpCode').val(''); // Reset nilai input OTP
                            $('#cancelReset, #confirmReset').removeClass('d-none');
                            $('#confirmReset').prop('disabled', true);
                            $('#confirmOtp').addClass('d-none');
                        }, 1000);
                    } else {
                        throw new Error('Kode OTP salah atau API gagal');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    notyf.open({
                        type: 'error',
                        message: error.response?.data?.message || 'Terjadi kesalahan.',
                    });
                });
        });

        // Event listener untuk teks kirim ulang kode OTP
        $('#resendOtpText').on('click', function() {
            if (resendCounter > 0) return; // Jangan kirim ulang jika hitungan mundur belum selesai

            fetch('/v1/account-reset', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (response.ok) {
                        notyf.open({
                            type: 'success',
                            message: 'Kode OTP telah dikirim ulang!',
                        });
                        startResendCountdown(); // Mulai hitungan mundur lagi
                    } else {
                        throw new Error('Gagal mengirim ulang kode OTP');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // Fungsi untuk menampilkan modal reset akun
        function alert_reset_account() {
            $('#resetAccountModal').modal('show');
        }
    </script>
@endsection
