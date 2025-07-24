<?php

$kolspan1 = 3 + 2 * $periode;

?>


<!DOCTYPE html>
<html>

<head>
    <title>Export Laporan Neraca</title>

</head>

<body>

    <div class="container">
        <center>
            <h4 style="font-size: 28px;">Laporan Neraca</h4>

        </center>


        <table class="table table-bordered" id="table-profit-loss" width="100%" border="1px solid black;"
            style="border-collapse: collapse">
            <tr>
                <th rowspan="2">
                    <center>Keterangan</center>
                </th>

                <?php
                $display_month = $start;
                for ($i = 0; $i <= $periode; $i++) { ?>
                <th colspan="2">
                    <center>{{ date('F Y', strtotime($display_month)) }}</center>
                </th>

                <?php 
                    $display_month = date('Y-m-d', strtotime($display_month . ' + 1 month'));
                } ?>

            </tr>

            <tr>
                @for ($i = 0; $i <= $periode; $i++)
                    <th>*</th>
                    <th>*</th>
                @endfor
            </tr>

            <tr>
                <td colspan="{{ $kolspan1 }}" style="border-top:2px solid black;"><strong>Aktiva Lancar</strong></td>
            </tr>
            @php
                $total_lancar = [];
                for ($c = 0; $c <= $periode; $c++) {
                    array_push($total_lancar, 0);
                }

            @endphp
            @foreach ($dt['aktiva_lancar'] as $i)
                <tr>
                    <?php
                   for ($in = 0; $in <= $periode; $in++) {
                        $lancar = DB::table('ml_journal_list')
                            ->where('asset_data_id', $i->id)
                            ->where('account_code_id', 1)
                            ->where('created', '>=', $time_array[$in]['awal'])
                            ->where('created', '<=', $time_array[$in]['akhir'])
                            ->sum(\DB::raw('debet - credit'));
                        $total_lancar[$in] = $total_lancar[$in] + $lancar; 

                    if($in == 0) { ?>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $i->name }} </td>
                    <?php } ?>

                    <td style="text-align:right;"> {{ number_format($lancar) }} </td>
                    <td></td>
                    <?php  } ?>
                </tr>
            @endforeach


            <tr>
                <td><strong>Total Aktiva Lancar</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;"> {{ number_format($total_lancar[$in]) }} </td>
                @endfor
            </tr>

            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Aktiva Tetap</strong></td>
            </tr>
            @php

                $total_tetap = [];
                for ($c = 0; $c <= $periode; $c++) {
                    array_push($total_tetap, 0);
                }
            @endphp
            @foreach ($dt['aktiva_tetap'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $tetap = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 2)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('debet-credit'));
                            $total_tetap[$in] = $total_tetap[$in] + $tetap;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                        @endif
                        <td style="text-align:right;">{{ number_format($tetap) }} </td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach

            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Akumulasi</strong></td>
            </tr>
            @php

                $total_akumulasi = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_akumulasi, 0);
                }

            @endphp
            @foreach ($dt['akumulasi'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $akumulasi = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 3)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('credit-debet'));
                            $total_akumulasi[$in] = $total_akumulasi[$in] + $akumulasi;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                        @endif
                        <td style="text-align:right;">{{ number_format($akumulasi * -1) }} </td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach

            <tr>
                <td><strong>Total Akumulasi Penyusutan</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">{{ number_format($total_akumulasi[$in] * -1) }}</td>
                @endfor
            </tr>
            <tr>
                <td><strong>Total Aktiva Tetap</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">{{ number_format($total_tetap[$in] - $total_akumulasi[$in]) }}</td>
                @endfor
            </tr>

            <tr>
                <td><strong>TOTAL AKTIVA</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;"><strong>
                            {{ number_format($total_lancar[$in] + $total_tetap[$in] - $total_akumulasi[$in]) }}
                        </strong></td>
                @endfor
            </tr>


            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Utang Jangka Pendek</strong></td>
            </tr>
            @php

                $total_pendek = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_pendek, 0);
                }
            @endphp

            @foreach ($dt['utang_pendek'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $pendek = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 4)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('credit-debet'));
                            $total_pendek[$in] = $total_pendek[$in] + $pendek;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                        @endif
                        <td style="text-align:right;">{{ number_format($pendek) }}</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Utang Jangka Pendek</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">{{ number_format($total_pendek[$in]) }}</td>
                @endfor
            </tr>



            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Utang Jangka Panjang</strong></td>
            </tr>
            @php

                $total_panjang = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_panjang, 0);
                }
            @endphp
            @foreach ($dt['utang_panjang'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $panjang = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 5)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('credit-debet'));
                            $total_panjang[$in] = $total_panjang[$in] + $panjang;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                        @endif
                        <td style="text-align:right;">{{ number_format($panjang) }}</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Utang Jangka Panjang</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">{{ number_format($total_panjang[$in]) }}</td>
                @endfor
            </tr>

            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Modal</strong></td>
            </tr>
            @php

                $total_modal = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_modal, 0);
                }
            @endphp
            @foreach ($dt['modal'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $modal = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 6)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('credit-debet'));
                            $total_modal[$in] = $total_modal[$in] + $modal;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                        @endif
                        <td style="text-align:right;"> {{ number_format($modal) }} </td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>    
            @for ($in=0; $in <= $periode; $in++)
            @if($in==0)
            <td>&nbsp;&nbsp;&nbsp;&nbsp; LABA/RUGI BERSIH </td>
            @endif
            <td style="text-align:right;"> {{ number_format($laba_bersih[$in]) }} </td>
            <td></td>
            @endfor
            </tr>


            <tr>
                <td><strong>Total Modal</strong></td>
                @for ($in=0; $in <= $periode; $in++)
                <td></td>
                <td style="text-align:right;"> {{ number_format($total_modal[$in] + $laba_bersih[$in]) }} </td>
                @endfor
            </tr>
            <tr>
                <td><strong>TOTAL UTANG DAN MODAL</strong></td>
                @for ($in=0; $in <= $periode; $in++)
                <td></td>
                <td style="text-align:right;"><strong>
                        {{ number_format($total_pendek[$in] + $total_panjang[$in] + $total_modal[$in] + $laba_bersih[$in]) }}</strong></td>
                @endfor
            </tr>




        </table>


    </div>

</body>

</html>
