@extends('master')

@section('style')
    <style>
        .tengah {
            width: 50%
        }

        @media (max-width: 600px) {
            .tengah {
                width: 100%
            }
        }
    </style>
@endsection
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
                        <li class="breadcrumb-item"><a href="{{ url('pos/index') }}">POS</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('pos/metode-pembayaran') }}">Metode Pembayaran</a></li>
                        </li>
                        <li class="breadcrumb-item">Terima Kasih</li>
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
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 bg-white rounded-3 p-3 d-flex justify-content-center align-items-center"
                                style="height: calc(100vh - 180px)">
                                <div class="d-flex flex-column text-center tengah">
                                    @if ($data->payment_status === 1)
                                        <i class="bi bi-check-circle-fill text-success mb-3" style="font-size: 80px"></i>
                                        <h3>Pembayaran Berhasil</h3>
                                    @else
                                        @if ($data->qris_code)
                                            <div style="display: flex; flex-direction: column; align-items: center;"
                                                class="x2">
                                                <div style="width: 200px;">
                                                    <canvas id="qrcode" class="mt-4"></canvas>
                                                </div>
                                                <div id="countdown"></div>
                                            </div>
                                            <div class="x2 text-lg text-center mt-3">
                                                Silahkan scan barcode ini di device Anda<br />
                                                menggunakan <b>QRIS</b> untuk melakukan pembayaran
                                            </div>
                                            <i class="x1 bi bi-check-circle-fill text-success mb-3"
                                                style="font-size: 80px; display: none"></i>
                                            <h3 style="display: none" class="x1">Pembayaran Berhasil</h3>
                                        @else
                                            <i class="x2 bi bi-clock-fill text-warning mb-3" style="font-size: 80px"></i>
                                            <h3 class="x2">Menunggu Proses Pembayaran</h3>
                                            <i class="x1 bi bi-check-circle-fill text-success mb-3"
                                                style="font-size: 80px; display: none"></i>
                                            <h3 style="display: none" class="x1">Pembayaran Berhasil</h3>
                                        @endif
                                    @endif
                                    <span class="fs-6 mb-5">No. Nota {{ $reference }}</span>
                                    <span class="fs-6 mb-2 d-none">Uang Kembalian</span>
                                    <h3 style="color: #EB7302" class="d-none">Rp 5.000</h3>
                                    <form action="{{ route('pos.send-receipt') }}" method="POST" class="w-100 mb-3 mt-5">
                                        @csrf
                                        <div class="input-group">
                                            <input type="hidden" name="reference" value="{{ $reference }}">
                                            <input disabled type="text" class="form-control border border-secondary"
                                                placeholder="Kirim Struk Melalui Email" name="inputText">
                                            <button disabled class="btn btn-outline-secondary" type="submit"><i
                                                    class="bi bi-caret-right-fill text-primary fs-3"></i></button>
                                        </div>
                                    </form>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="/pos/print-receipt?reference={{ $reference }}" target="_blank"
                                            class="btn btn-outline-info text-dark rounded-3 me-2 p-3">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <a href="/pos/index" class="btn w-100 p-3 text-white"
                                            style="background-color: #EB7302"><i class="bi bi-plus mse-2"></i>Mulai
                                            Transaksi</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script>
        $(document).ready(function() {
            const qrcode = '{{ $data->qris_code }}'
            if (qrcode) {
                const expiredAt = new Date("{{ $data->qris_expired_at }}").getTime();
                const qr = new QRious({
                    element: document.getElementById('qrcode'),
                    value: qrcode,
                    size: 200
                });

                const countdownElement = document.getElementById('countdown');

                const interval = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = expiredAt - now;

                    // Waktu mundur dalam menit dan detik
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    countdownElement.innerHTML = minutes + " menit " + seconds + " detik";

                    // Jika waktu habis
                    if (distance < 0) {
                        clearInterval(interval);
                        countdownElement.innerHTML = "<span style='color: red;'><b>Waktu Habis</b></span>";
                    }
                }, 1000);
            }

            // Cek status pembayaran setiap 10 detik
            const checkPaymentInterval = setInterval(function() {
                axios.get('/v1/payment-check?reference={{ $reference }}')
                    .then(function(response) {
                        if (response.data.status) {
                            clearInterval(checkPaymentInterval);
                            $('.x2').css('display', 'none')
                            $('.x1').css('display', 'block')
                        }
                    })
                    .catch(function(error) {
                        console.error('Error checking payment status:', error);
                    });
            }, 5000); // 10 detik
        });
    </script>
@endsection
