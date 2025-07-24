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
                        <li class="breadcrumb-item"><a href="{{ url('report') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">Hapus Saldo Awal</li>
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
                                <h5 class="card-title">Hapus Saldo Awal</h5>
                                @php
                                    $bulan_ini = date('F');
                                    $tahun_ini = date('Y');

                                    $awal = strtotime(date('Y-m-01'));
                                    $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                                    $end = date('Y') . '-' . date('m') . '-' . $tanggal_akhir;

                                    $akhir = strtotime($end);
                                @endphp

                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">

                                    <div class="row">
                                        <div class="col-md-12" style="display: inline-flex">
                                            <div class="form-group">
                                                <select style="width:200px;" class="form-control cust-control"
                                                    id="month" name="month">
                                                    <option value="">Pilih bulan</option>
                                                    <option <?php if ($bulan_ini == 'January') {
                                                        echo 'selected';
                                                    } ?> value="01">January</option>
                                                    <option <?php if ($bulan_ini == 'February') {
                                                        echo 'selected';
                                                    } ?> value="02">February</option>
                                                    <option <?php if ($bulan_ini == 'March') {
                                                        echo 'selected';
                                                    } ?> value="03">March</option>
                                                    <option <?php if ($bulan_ini == 'April') {
                                                        echo 'selected';
                                                    } ?> value="04">April</option>
                                                    <option <?php if ($bulan_ini == 'May') {
                                                        echo 'selected';
                                                    } ?> value="05">May</option>
                                                    <option <?php if ($bulan_ini == 'June') {
                                                        echo 'selected';
                                                    } ?> value="06">June</option>
                                                    <option <?php if ($bulan_ini == 'July') {
                                                        echo 'selected';
                                                    } ?> value="07">July</option>
                                                    <option <?php if ($bulan_ini == 'August') {
                                                        echo 'selected';
                                                    } ?> value="08">August</option>
                                                    <option <?php if ($bulan_ini == 'September') {
                                                        echo 'selected';
                                                    } ?> value="09">September</option>
                                                    <option <?php if ($bulan_ini == 'October') {
                                                        echo 'selected';
                                                    } ?> value="10">October</option>
                                                    <option <?php if ($bulan_ini == 'November') {
                                                        echo 'selected';
                                                    } ?> value="11">November</option>
                                                    <option <?php if ($bulan_ini == 'December') {
                                                        echo 'selected';
                                                    } ?> value="12">December</option>

                                                </select>
                                            </div>


                                            <select style="width:200px;margin-left:5px;" class="form-control cust-control"
                                                name="year" id="year">
                                                <option value="">Pilih tahun</option>
                                                <option <?php if ($tahun_ini == date('Y')) {
                                                    echo 'selected';
                                                } ?> value="{{ date('Y') }}">{{ date('Y') }}
                                                </option>
                                                <option <?php if ($tahun_ini == date('Y', strtotime('-1 year', strtotime(date('Y'))))) {
                                                    echo 'selected';
                                                } ?>
                                                    value="{{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}">
                                                    {{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}</option>
                                                <option <?php if ($tahun_ini == date('Y', strtotime('-2 year', strtotime(date('Y'))))) {
                                                    echo 'selected';
                                                } ?>
                                                    value="{{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}">
                                                    {{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}</option>
                                                <option <?php if ($tahun_ini == date('Y', strtotime('-3 year', strtotime(date('Y'))))) {
                                                    echo 'selected';
                                                } ?>
                                                    value="{{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}">
                                                    {{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}</option>
                                                <option <?php if ($tahun_ini == date('Y', strtotime('-4 year', strtotime(date('Y'))))) {
                                                    echo 'selected';
                                                } ?>
                                                    value="{{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}">
                                                    {{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}</option>
                                            </select>

                                            <button style="float: right;margin-left:5px;margin-top:1px;"
                                                id="btn-submit-initial-delete" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>



                            </div>
                            <div class="mtop50"></div>
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
