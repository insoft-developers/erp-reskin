<div class="row">
    <div class="col-12">
        @if (session()->has('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success">
                        {!! session('success') !!}
                    </div>
                </div>
            </div>
        @elseif (session()->has('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        {!! session('error') !!}
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="row">
                <div class="col-12">
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <!-- [Leads] start -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 style="color:white">Flag Pembayaran</h5>
            </div>
            <div class="card-body">
                <form
                    action="{{ isset($paymentMethodFlag) ? route('payment-method-flag.update', $paymentMethodFlag->id) : route('payment-method-flag.store') }}"
                    method="post">
                    @csrf
                    @if (isset($paymentMethodFlag))
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <!--<option value="Cash|null"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Cash|null' ? 'selected' : '' }}>
                                Cash</option>-->
                            <option value="Transfer|bank-bca"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Transfer|bank-bca' ? 'selected' : '' }}>
                                Transfer - Bank BCA</option>
                            <option value="Transfer|bank-mandiri"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Transfer|bank-mandiri' ? 'selected' : '' }}>
                                Transfer - Bank Mandiri</option>
                            <option value="Transfer|bank-bni"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Transfer|bank-bni' ? 'selected' : '' }}>
                                Transfer - Bank BNI</option>
                            <option value="Transfer|bank-bri"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Transfer|bank-bri' ? 'selected' : '' }}>
                                Transfer - Bank BRI</option>
                            <option value="Transfer|bank-lain"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Transfer|bank-lain' ? 'selected' : '' }}>
                                Transfer - Bank Lain</option>
                            <option value="COD|null"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'COD|null' ? 'selected' : '' }}>
                                COD</option>
                            <option value="Marketplace|null"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Marketplace|null' ? 'selected' : '' }}>
                                Marketplace</option>
                            <option value="Piutang|null"
                                {{ isset($paymentMethodFlag) && $paymentMethodFlag->payment_method == 'Piutang|null' ? 'selected' : '' }}>
                                Piutang</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="flag" class="form-label">Flag</label>
                        <input type="text" class="form-control" id="flag" name="flag"
                            value="{{ isset($paymentMethodFlag) ? $paymentMethodFlag->flag : '' }}" required>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: end">
                        <a href="{{ url('payment-method-setting') }}" class="btn btn-danger">Kembali</a>
                        <button type="submit"
                            class="btn btn-primary">{{ isset($paymentMethodFlag) ? 'Update' : 'Submit' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
