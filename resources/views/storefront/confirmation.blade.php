@extends('storefront.template.layouts.app')
@section('style')
    <style>
        ul.a {
            list-style-type: circle;
            margin-left: 15px
        }

        li {
            list-style: outside
        }
    </style>
@endsection
@section('content')
    <!-- Header -->
    <header class="header">
        <div class="main-bar">
            <div class="container">
                <div class="header-content">
                    <div class="left-content">
                        <a href="/{{ $username }}" class="back-btn">
                            <i class="fa-solid fa-home"></i>
                        </a>
                        <h4 class="title mb-0 text-nowrap">Pesanan Sudah Disimpan</h4>
                        <input type="hidden" value="{{ session('kirim_wa') }}" id="kirim_wa">
                        <input type="hidden" value="{{ session('whatsapp_number') }}" id="whatsapp_number">
                        <input type="hidden" value="{{ session('cw') }}" id="cw">
                    </div>
                    <div class="mid-content"></div>
                    <div class="right-content"></div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header -->
    <!-- Page Content -->
    <div class="page-content">
        <div class="container fb">
            @php
                $banks = [];
                $payments = json_decode($store->payment_method, true);
                foreach ($payments as $pay) {
                    if ($pay['method'] == 'Transfer' && $pay['selected'] == 'true') {
                        $banks = $pay['banks'];
                    }
                }
            @endphp
            @if ($order)
                @if ($order->status != '0')
                    <div class="title-bar">
                        <h5 class="mb-2">Halo {{ $order->customer->name }}</h5>
                        <small class="mb-0">Pembayaranmu telah selesai</small>
                    </div>
                    <div>
                        <h5 class="text-center">Pembayaran telah berhasil!</h5>
                    </div>
                @else
                    <div class="title-bar">
                        <h5 class="mb-2">Halo {{ $order->customer->name }}</h5>
                        <small class="mb-0">Selesaikan pembayaran</small>
                    </div>
                    <div>
                        <p>Metode Pembayaran yang dipilih: {{ $payment }}</p>
                        @if ($order->payment_method == 'kas')
                            <p>Silahkan menuju ke kasir dan melakukan pembayaran degan menyertakan kode pemesanan berikut:
                            </p>
                            <h3>{{ $order->reference }}</h3>
                            <div class="bill-detail">
                                <h4 class="text-center">Terima Kasih</h4>
                                <ul class="a">
                                    <li>Jika ingin melakukan pesanan selanjutnya, silahkan scan QR Order atau buka link order ulang.</li>
                                    <li>Mohon untuk tidak berpindah meja sebelum semua pesanan keluar.</li>
                                    <li>Apabila ada kendala, mohon untuk disampaikan kepada staff terkait.</li>
                                </ul>
                            </div>
                        @elseif($order->payment_method == 'randu-wallet')
                            <p>Silahkan lakukan pembayaran online dengan menekan tombol berikut</p>
                            <button class="btn btn-primary" id="onlinePayment" data-order="{{ $order->id }}">Bayar
                                Online</button>
                        @else
                            <p>Silahkan melakukan pembayaran ke rekening berikut ini:</p>
                            <div class="accordion accordion-primary" id="accordion-one">
                                @foreach ($banks as $b)
                                    @if ($b['selected'] == 'true')
                                        <div class="accordion-item">
                                            <div class="accordion-header collapsed" id="heading{{ $b['id'] }}"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $b['id'] }}"
                                                aria-controls="collapse{{ $b['id'] }}" aria-expanded="true"
                                                role="button">
                                                <span class="accordion-header-icon"></span>
                                                <span class="accordion-header-text">{{ $b['bank'] }}</span>
                                                <span class="accordion-header-indicator"></span>
                                            </div>
                                            <div id="collapse{{ $b['id'] }}" class="collapse"
                                                aria-labelledby="headingOne" data-bs-parent="#accordion-one">
                                                <div class="accordion-body-text">
                                                    <p>Nama Bank: {{ $b['bank'] }}</p>
                                                    <p>Nama Pemilik: {{ $b['bankOwner'] }}</p>
                                                    <p>Nomor Rekening: {{ $b['bankAccountNumber'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <p><em>Jika telah melakukan pembayaran, silahkan tunjukan bukti transfer ke Customer Service agar pesanan
                                    segera diproses</em></p>
                            <a href="#" class="btn btn-primary mt-5" id="orderCheck">Konfirmasi Pembayaran</a>

                        @endif

                    </div>
                @endif
            @else
                <div class="bill-detail text-center">
                    <h4>Pesanan tidak ditemukan...</h4>
                    <p>Silahkan melanjutkan belanja dengan memilih produk terlebih dahulu</p>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        $('input[name="payment_type"]').on('change', function () {
            var paymentType = $(this).val();
            var customerDetails = {
                _token: '{{ csrf_token() }}',
                payment_type: paymentType
            };
            $('#submitOrder').removeClass('d-none');
            $.ajax({
                url: '{{ route('order.updatePaymentDetails', $username) }}',
                type: 'POST',
                data: customerDetails,
                success: function (response) {
                    if (response.success) {
                        console.log('Payment details updated successfully!', response);
                    } else {
                        console.log('Failed to update payment details.');
                    }
                }
            });
        });

        $('#tunai').on('click', function () {
            $('#tunai').addClass('bg-primary-subtle');
            $('#transfer').removeClass('bg-primary-subtle');
            $('#online').removeClass('bg-primary-subtle');
        });

        $('#transfer').on('click', function () {
            $('#tunai').removeClass('bg-primary-subtle');
            $('#transfer').addClass('bg-primary-subtle');
            $('#online').removeClass('bg-primary-subtle');
        });

        $('#online').on('click', function () {
            $('#tunai').removeClass('bg-primary-subtle');
            $('#transfer').removeClass('bg-primary-subtle');
            $('#online').addClass('bg-primary-subtle');
        });

        function updateCustomerDetails(details) {
            $.ajax({
                url: '{{ route('order.updateCustomerDetails', $username) }}',
                type: 'POST',
                data: details,
                success: function (response) {
                    if (response.success) {
                        console.log('Customer details updated successfully!');
                    } else {
                        console.log('Failed to update customer details.');
                    }
                }
            });
        }

        $('#submitOrder').on('click', function () {
            var details = {
                _token: '{{ csrf_token() }}',
                username: "{{ $username }}",
                customer_name: "{{ $order['customer_name'] ?? '' }}",
                phone_number: "{{ $order['phone_number'] ?? '' }}",
                order_type: "{{ $order['order_type'] ?? '' }}",
                payment_type: "{{ $order['payment_type'] ?? '' }}"
            };
            $.ajax({
                url: '{{ route('checkout.process', $username) }}',
                type: 'POST',
                data: details,
                success: function (response) {
                    if (response.success) {
                        const order = response.orderid;
                        console.log(order);
                        window.location.href = `/{{ $username }}/order/confirmation/${order}`;
                    } else {
                        console.log('Failed to update customer details.');
                    }
                }
            });
        });

        $('#onlinePayment').on('click', function () {
            let order = $(this).data('order');
            window.location.href = `/{{ $username }}/order/payment/${order}`;
        });
    });

    // Dijalankan setelah semua resource halaman selesai dimuat
    window.onload = function () {
        const cwElem = document.getElementById("cw");
        const waElem = document.getElementById("kirim_wa");
        const phoneElem = document.getElementById("whatsapp_number");

        if (cwElem && waElem && phoneElem) {
            const cw = cwElem.value;
            if (cw === "1") {
                const kirim_wa = waElem.value;
                const whatsapp_number = phoneElem.value;
                const encodedMessage = encodeURIComponent(kirim_wa);
                const isMobile = /iPhone|Android/i.test(navigator.userAgent);
                const link = isMobile
                    ? `whatsapp://send?phone=${whatsapp_number}&text=${encodedMessage}`
                    : `https://wa.me/${whatsapp_number}?text=${encodedMessage}`;
                window.location.href = link;
            }
        }
    };

$('#orderCheck').on('click', function (e) {
    e.preventDefault();
    const whatsapp_number = $('#whatsapp_number').val();
    const message = 'Saya sudah melakukan pembayaran, pesananya mohon diproses';
    const encodedMessage = encodeURIComponent(message);
    const isMobile = /iPhone|Android/i.test(navigator.userAgent);
    const link = isMobile
        ? `whatsapp://send?phone=${whatsapp_number}&text=${encodedMessage}`
        : `https://wa.me/${whatsapp_number}?text=${encodedMessage}`;
    window.location.href = link;
});

</script>


@endsection

