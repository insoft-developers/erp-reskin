@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        
            <div style="background: whitesmoke;" class="content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Pengaturan Kas Kecil</h5>


                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    @if (session()->has('error'))
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="alert alert-danger">
                                                    {!! session('error') !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="alert alert-success">
                                                    {!! session('success') !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <form method="POST" action="{{ url('petycash_update') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="pc-title"><strong>Apakah Anda Perlu Mengaktifkan Kas Kecil / Petty
                                                        Cash?</strong><br><br>

                                                    Mengaktifkan kas kecil dalam sistem POS sangat berguna jika bisnis Anda
                                                    sering melakukan transaksi tunai dan membutuhkan uang kembalian, serta
                                                    memiliki pengeluaran kecil rutin seperti bahan baku tambahan atau biaya
                                                    transportasi. Fitur ini juga memudahkan pemantauan dan rekonsiliasi kas
                                                    setiap akhir shift.
                                                    <br>
                                                    <br>
                                                    Jika bisnis Anda lebih banyak menggunakan pembayaran non-tunai dan
                                                    jarang membutuhkan uang kembalian, atau jika pengeluaran kecil tidak
                                                    rutin, Anda mungkin tidak perlu mengaktifkan kas kecil. Menonaktifkan
                                                    fitur ini cocok untuk pencatatan keuangan yang lebih sederhana. Pilihlah
                                                    pengaturan sesuai kebutuhan operasional bisnis Anda untuk efisiensi dan
                                                    akurasi dalam pengelolaan keuangan.
                                                    <br>
                                                    <br>
                                                    <strong>PENTING!!! Jika PETTY CASH = OFF Maka Laporan Rekapitulasi Harian Akan jadi 0, Jika Anda membutuhkan Laporan Rekapitulasi Harian maka setting PETTY CASH wajib = ON</strong>
                                                <br>
                                                </p>
                                            </div>
                                            <div class="col-md-4">

                                                <div class="form-group">
                                                    <label>Petty Cash Setting:</label>
                                                    @if (!empty($data))
                                                        <select name="petty_cash" class="form-control cust-control">
                                                            <option <?php if ($data->petty_cash == '') {
                                                                echo 'selected';
                                                            } ?> value="">Pilih</option>
                                                            <option <?php if ($data->petty_cash == '1') {
                                                                echo 'selected';
                                                            } ?> value="1">ON (Gunakan Kembalian / Rekapitulasi Harian)</option>
                                                            <option <?php if ($data->petty_cash == '2') {
                                                                echo 'selected';
                                                            } ?> value="2">OFF (Tidak Pakai Kembalian / Rekapitulasi Harian)</option>
                                                        </select>
                                                    @else
                                                        <select name="petty_cash" class="form-control cust-control">
                                                            <option value="">Pilih</option>
                                                            <option value="1">ON</option>
                                                            <option value="2">OFF</option>
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mtop20">
                                            <div class="col-md-2">
                                                <button class="btn btn-primary">Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="mtop50"></div>

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
@endsection
