@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
       
            <div style="background: whitesmoke;" class="content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Reset Akun</h5>


                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <table class="table table-hover">
                                        
                                        <tr onclick="alert_reset_account()">
                                            <td class="menu-report-row">
                                                <strong><span class="report-menu-title">Reset Account Sekarang</span></strong><br>
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
          

        
    </div>


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
