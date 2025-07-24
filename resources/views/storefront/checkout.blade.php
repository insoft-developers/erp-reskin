@extends('storefront.template.layouts.app')

@section('content')
    <!-- Header -->
    <header class="header">
        <div class="main-bar">
            <div class="container">
                <div class="header-content">
                    <div class="left-content">
                        <a href="{{ $previousUrl }}" class="back-btn">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <h4 class="title mb-0 text-nowrap">Pilih Metode Pembayaran</h4>
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
            <!-- <form action="{{ route('checkout.process', $username) }}" method="post"> -->
            @if ($cart)
                <div class="title-bar">
                    <h5 class="mb-2">Halo {{ $order['customer_name'] }}</h5>
                    <small class="mb-0">Silahkan pilih metode pembayaran</small>
                </div>
                @php
                    $cash = false;
                    $transfer = false;
                    $online = false;
                    $payment = json_decode($store->payment_method, true);

                    foreach ($payment as $pay) {
                        if ($pay['method'] == 'Cash' && $pay['selected'] == 'true') {
                            $cash = true;
                        }
                        if ($pay['method'] == 'Transfer' && $pay['selected'] == 'true') {
                            $transfer = true;
                            $banks = $pay['banks'];
                        }
                        if ($pay['method'] == 'Online-Payment' && $pay['selected'] == 'true') {
                            $online = true;
                        }
                    }
                @endphp
                <div class="accordion style-2 circle-radio" id="accordionExample">
                    @if ($cash)
                        <label
                            class="accordion-header mb-2 radio-label w-100 {{ $order['payment_type'] == 'kas' ? 'bg-primary-subtle' : '' }}"
                            id="tunai">
                            <i class="fa-solid fa-money-bill me-2"></i> Pembayaran Tunai <span class="text-soft font-10">(Bayar Langsung di Kasir)</span><input type="radio" name="payment_type" value="kas" class="float-end"
                                {{ $order['payment_type'] == 'kas' ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                        </label>
                    @endif
@if ($transfer)
<label
    class="accordion-header mb-2 radio-label w-100 {{ Str::startsWith($order['payment_type'], 'bank-') ? 'bg-primary-subtle' : '' }}"
    id="transfer" data-bs-toggle="collapse" data-bs-target="#collapseBank" aria-expanded="false"
    aria-controls="collapseBank">
    <i class="fa-solid fa-building-columns me-2"></i> Transfer Bank 
    <span class="text-soft font-10">(Pengecekan Manual)</span>
    <span class="checkmark float-end"></span>
</label>


                        <div class="collapse" id="collapseBank">
                            <div class="card-body">
                                @foreach ($banks as $b)
                                    @if ($b['selected'] == 'true')
                                        <label class="accordion-header mb-2 radio-label w-100" id="{{ $b['id'] }}">
                                            <i class="fa-solid fa-building-columns me-2"></i> {{ $b['bank'] }}<input
                                                type="radio" name="bank-selected" value="{{ $b['remark'] }}"
                                                class="float-end"
                                                {{ $order['payment_type'] == 'bank-bca' ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($online)
                        <label
                            class="accordion-header mb-2 radio-label w-100 {{ $order['payment_type'] == 'online' ? 'bg-primary-subtle' : '' }}"
                            id="online">
                            <i class="fa-solid fa-wallet me-2"></i> Pembayaran Online <span class="text-soft font-10">(QRIS,
                                OVO, Shopeepay, VA)</span> <input type="radio" name="payment_type" value="randu-wallet"
                                class="float-end" {{ $order['payment_type'] == 'online' ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                        </label>
                    @endif
                </div>
                <div class="bill-detail">
                    <ul>
                        <li>
                            <div class="row">
                                <div class="col-7">
                                    <p class="mb-0">Subtotal</p>
                                </div>
                                <div class="col-1">
                                    <span></span>
                                </div>
                                <div class="col-1">
                                    <span><i class="fa-solid fa-rupiah-sign"></i></span>
                                </div>
                                <div class="col-2">
                                    <span id="subtotal">{{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <p class="mb-0">Diskon</p>
                                </div>
                                <div class="col-1">
                                    <span>-</span>
                                </div>
                                <div class="col-1">
                                    <span><i class="fa-solid fa-rupiah-sign"></i></span>
                                </div>
                                <div class="col-2">
                                    <span id="discount">{{ number_format($totals['discount'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <p class="mb-0">Pajak</p>
                                </div>
                                <div class="col-1">
                                    <span>+</span>
                                </div>
                                <div class="col-1">
                                    <span><i class="fa-solid fa-rupiah-sign"></i></span>
                                </div>
                                <div class="col-2">
                                    <span id="tax">{{ number_format($totals['tax'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <p class="mb-0">Ongkir</p>
                                </div>
                                <div class="col-1">
                                    <span>+</span>
                                </div>
                                <div class="col-1">
                                    <span><i class="fa-solid fa-rupiah-sign"></i></span>
                                </div>
                                <div class="col-2">
                                    <span id="shipping">{{ number_format($totals['shipping'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="divider divider-dotted border-light"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-7">
                                    <h6 class="mb-0">Total Yang Harus Dibayar</h6>
                                </div>
                                <div class="col-1">
                                    <span></span>
                                </div>
                                <div class="col-1">
                                    <h6 class="text-danger"><i class="fa-solid fa-rupiah-sign"></i></h6>
                                </div>
                                <div class="col-2">
                                    <h6 class="text-danger" id="cart-total">
                                        {{ number_format($totals['total'], 0, ',', '.') }}</h6>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            @else
                <div class="bill-detail text-center">
                    <h4>Keranjang kosong...</h4>
                    <p>Silahkan melanjutkan belanja dengan memilih produk terlebih dahulu</p>
                </div>
            @endif
        </div>
        <!-- FOOTER -->
        @if ($cart)
            <div class="footer fixed">
                <div class="container">
                    <button type="submit" class="btn btn-primary btn-block d-none" id="submitOrder">Bayar
                        Sekarang</button>
                </div>
            </div>
        @endif
        <!-- </form> -->
    </div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        // === Handle: Klik pilihan Tunai atau Online ===
        $('input[name="payment_type"]').on('change', function () {
            // Uncheck semua bank
            $('input[name="bank-selected"]').prop('checked', false);

            // Update visual
            $('#tunai, #online, #transfer').removeClass('bg-primary-subtle');
            $(this).closest('label').addClass('bg-primary-subtle');

            // Kirim payment_type
            const paymentType = $(this).val();
            const customerDetails = {
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
                        console.log('Payment details updated:', response);
                    }
                }
            });
        });

        // === Handle: Klik Transfer Bank ===
        $('#transfer').on('click', function () {
            $('#tunai, #online').removeClass('bg-primary-subtle');
            $('#transfer').addClass('bg-primary-subtle');
            $('#collapseBank').collapse('show');

            // Jika belum ada yang dipilih, pilih otomatis radio bank pertama
            if (!$('input[name="bank-selected"]:checked').length) {
                const firstBank = $('input[name="bank-selected"]').first();
                firstBank.prop('checked', true).trigger('change');
            }
        });

        // === Handle: Pilih salah satu bank di Transfer ===
        $('input[name="bank-selected"]').on('change', function () {
            // Uncheck tunai & online
            $('input[name="payment_type"]').prop('checked', false);
            $('#tunai, #online').removeClass('bg-primary-subtle');
            $('#transfer').addClass('bg-primary-subtle');

            // Tambah visual ke bank yang dipilih
            $('label[for="bank-selected"]').removeClass('bg-primary-subtle');
            $(this).closest('label').addClass('bg-primary-subtle');

            const paymentType = $(this).val();
            const customerDetails = {
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
                        console.log('Payment details updated:', response);
                    }
                }
            });
        });

        // === Handle: Klik tombol Bayar Sekarang ===
        $('#submitOrder').on('click', function () {
            const details = {
                _token: '{{ csrf_token() }}',
                username: "{{ $username }}",
                customer_name: "{{ $order['customer_name'] }}",
                phone_number: "{{ $order['phone_number'] }}",
                order_type: "{{ $order['order_type'] }}",
                payment_type: "{{ $order['payment_type'] }}"
            };

            $.ajax({
                url: '{{ route('checkout.process', $username) }}',
                type: 'POST',
                data: details,
                success: function (response) {
                    if (response.success) {
                        const order = response.orderid;
                        if (response.payment_type == "randu-wallet") {
                            window.location.href = `/{{ $username }}/order/payment/${order}`;
                        } else {
                            window.location.href = `/{{ $username }}/order/confirmation/${order}`;
                        }
                    } else {
                        console.log('Failed to update customer details.');
                    }
                }
            });
        });
    });
</script>
@endsection


