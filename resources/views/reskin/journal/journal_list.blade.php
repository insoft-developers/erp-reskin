 @extends('main.master_new')
 @section('content')
     <div class="page-wrapper">

         <!-- Start Content -->
         <div class="content">



             <!-- start row -->
             <div class="row">
                 <div class="col-sm-12">
                     <div class="card">
                         <div class="card-header">
                             <h4 class="card-title">Jurnal Akuntansi</h4>
                             <button id="btn-reskin-tambah" onclick="tambah_jurnal_reguler()"
                                    class="btn btn-soft-success pull-right">Input Jurnal</button>


                                <button id="btn-reskin-tambah2" onclick="add_jurnal()" class="btn btn-soft-warning ">Jurnal
                                    Cepat</button>

                         </div><!-- end card header -->
                         <div class="card-body">
                             @php

                                 $sesi = session('sess_periode') ?? ['bulan' => date('m'), 'tahun' => date('Y')];

                                 $tahun_ini = $sesi['tahun'];
                                 $bulan_ini = $sesi['bulan'];

                             @endphp
                             <div class="table-search d-flex align-items-center">
                                 <div class="search-input">
                                     <a href="javascript:void(0);" class="btn-searchset"><i
                                             class="isax isax-search-normal fs-12"></i></a>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-6">
                                     <select class="form-control cust-control" id="bulan">
                                         <option value="">Pilih bulan</option>
                                         <option <?php if ($bulan_ini == '01') {
                                             echo 'selected';
                                         } ?> value="01">Januari</option>
                                         <option <?php if ($bulan_ini == '02') {
                                             echo 'selected';
                                         } ?> value="02">Februari</option>
                                         <option <?php if ($bulan_ini == '03') {
                                             echo 'selected';
                                         } ?> value="03">Maret</option>
                                         <option <?php if ($bulan_ini == '04') {
                                             echo 'selected';
                                         } ?> value="04">April</option>
                                         <option <?php if ($bulan_ini == '05') {
                                             echo 'selected';
                                         } ?> value="05">Mei</option>
                                         <option <?php if ($bulan_ini == '06') {
                                             echo 'selected';
                                         } ?> value="06">Juni</option>
                                         <option <?php if ($bulan_ini == '07') {
                                             echo 'selected';
                                         } ?> value="07">Juli</option>
                                         <option <?php if ($bulan_ini == '08') {
                                             echo 'selected';
                                         } ?> value="08">Agustus</option>
                                         <option <?php if ($bulan_ini == '09') {
                                             echo 'selected';
                                         } ?> value="09">September</option>
                                         <option <?php if ($bulan_ini == '10') {
                                             echo 'selected';
                                         } ?> value="10">Oktober</option>
                                         <option <?php if ($bulan_ini == '11') {
                                             echo 'selected';
                                         } ?> value="11">November</option>
                                         <option <?php if ($bulan_ini == '12') {
                                             echo 'selected';
                                         } ?> value="12">Desember</option>
                                     </select>
                                 </div>
                                 <div class="col-md-6">
                                     <select class="form-control cust-control" id="tahun">
                                         <option value="">Pilih tahun</option>
                                         <option <?php if ($tahun_ini == date('Y', strtotime('+1 year', strtotime(date('Y'))))) {
                                             echo 'selected';
                                         } ?>
                                             value="{{ date('Y', strtotime('+1 year', strtotime(date('Y')))) }}">
                                             {{ date('Y', strtotime('+1 year', strtotime(date('Y')))) }}</option>
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
                                 </div>
                                 <div class="mtop20"></div>
                                 <div class="col-md-12">
                                     <div class="form-group">
                                         <input type="text" id="cari" placeholder="Cari disini.."
                                             class="form-control cust-control">
                                     </div>

                                 </div>
                             </div>
                             <div class="mtop20"></div>
                             <input type="checkbox" id="inbalance_checkbox" name="inbalance_checkbox" value="">
                             <label for="inbalance_checkbox"><span class="text-inbalance">Tampilkan Jurnal yang Tidak
                                     Balance (Bermasalah)</span></label>
                             <div class="mtop20"></div>
                             <div class="table-responsive">
                                 <table style="width: 100%;" id="table-jurnal" class="table table-striped mb-0">
                                     <thead>
                                         <tr class="border-b">
                                             <th width="0%">ID</th>
                                             <th width="3%">Tanggal</th>
                                             <th width="*">Nama Transaksi</th>
                                             <th width="23%">Nominal</th>
                                             <th width="20%" style="white-space: nowrap">Dibuat</th>
                                             <th width="15%" class="text-end">Opsi</th>
                                         </tr>
                                     </thead>
                                     <tbody></tbody>
                                 </table>

                             </div>
                         </div><!-- end card body -->
                     </div><!-- end card -->
                 </div><!-- end col -->
             </div>
             <!-- end row -->

         </div>
         <!-- End Content -->

         <!-- Start Footer -->
         @include('component_new.footer')
         <!-- End Footer -->

     </div>
 @endsection
