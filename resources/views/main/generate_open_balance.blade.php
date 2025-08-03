@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        
            <div style="background: whitesmoke;" class="content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Generate Opening Balance</h5>

                                @php
                                    $bulan_ini = date('F');
                                    $tahun_ini = date('Y');
                                @endphp
                            </div>

                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <form id="form-opening-balance" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control cust-control" name="month">
                                                        <option value="">Pilih bulan</option>
                                                        <option <?php if ($bulan_ini == 'January') {
                                                            echo 'selected';
                                                        } ?> value="January">January</option>
                                                        <option <?php if ($bulan_ini == 'February') {
                                                            echo 'selected';
                                                        } ?> value="February">February</option>
                                                        <option <?php if ($bulan_ini == 'March') {
                                                            echo 'selected';
                                                        } ?> value="March">March</option>
                                                        <option <?php if ($bulan_ini == 'April') {
                                                            echo 'selected';
                                                        } ?> value="April">April</option>
                                                        <option <?php if ($bulan_ini == 'May') {
                                                            echo 'selected';
                                                        } ?> value="May">May</option>
                                                        <option <?php if ($bulan_ini == 'June') {
                                                            echo 'selected';
                                                        } ?> value="June">June</option>
                                                        <option <?php if ($bulan_ini == 'July') {
                                                            echo 'selected';
                                                        } ?> value="July">July</option>
                                                        <option <?php if ($bulan_ini == 'August') {
                                                            echo 'selected';
                                                        } ?> value="August">August</option>
                                                        <option <?php if ($bulan_ini == 'September') {
                                                            echo 'selected';
                                                        } ?> value="September">September</option>
                                                        <option <?php if ($bulan_ini == 'October') {
                                                            echo 'selected';
                                                        } ?> value="October">October</option>
                                                        <option <?php if ($bulan_ini == 'November') {
                                                            echo 'selected';
                                                        } ?> value="November">November</option>
                                                        <option <?php if ($bulan_ini == 'December') {
                                                            echo 'selected';
                                                        } ?> value="December">December</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control cust-control" name="year">
                                                        <option value="">Pilih tahun</option>
                                                        <option <?php if ($tahun_ini == date('Y')) {
                                                            echo 'selected';
                                                        } ?> value="{{ date('Y') }}">
                                                            {{ date('Y') }}</option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-1 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-2 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-3 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-4 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">

                                                <button class="btn btn-primary mtop20">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="mtop30"></div>
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
