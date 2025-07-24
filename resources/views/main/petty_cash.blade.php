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
                        <li class="breadcrumb-item">Pengaturan Kas Kecil</li>
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
    </main>
@endsection
