<!DOCTYPE html>
<html>

<head>
    <title>Export Laporan Excel</title>

</head>

<body>

    <div class="container">
        <center>
            <h4><span style="font-size: 28px;">Laporan Laba Rugi</span>
            </h4>

        </center>
        @php
            $kolspan1 = 3 + 2 * $periode;
        @endphp
        <table class="table table-bordered" id="table-profit-loss" width="100%" border="1px solid black"
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
                <td colspan="{{ $kolspan1 }}" style="border-top:2px solid black;"><strong>Pendapatan</strong></td>
            </tr>
            @php
                $total_income = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_income, 0);
                }
            @endphp
            @foreach ($data['income'] as $i)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $income = DB::table('ml_journal_list')
                                ->where('asset_data_id', $i->id)
                                ->where('account_code_id', 7)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('credit - debet'));
                            $total_income[$in] = $total_income[$in] + $income;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $i->name }}</td>
                        @endif
                        <td style="text-align:right;">{{ number_format($income) }}</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Pendapatan Bersih</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">{{ number_format($total_income[$in]) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Harga Pokok Penjualan</strong></td>
            </tr>
            @php
                $total_hpp = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_hpp, 0);
                }
            @endphp
            @foreach ($data['hpp'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $hpp = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 8)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('debet-credit'));
                            $total_hpp[$in] = $total_hpp[$in] + $hpp;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                        @endif
                        <td style="text-align:right;">({{ number_format($hpp) }})</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Harga Pokok Penjualan</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">({{ number_format($total_hpp[$in]) }})</td>
                @endfor
            </tr>

            <tr>
                <td><strong>LABA/RUGI KOTOR</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">
                        <strong>{{ number_format($total_income[$in] - $total_hpp[$in]) }}</strong>
                    </td>
                @endfor
            </tr>
            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Biaya Penjualan</strong></td>
            </tr>
            @php
                $total_selling_cost = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_selling_cost, 0);
                }
            @endphp
            @foreach ($data['selling_cost'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $selling_cost = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 9)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('debet-credit'));
                            $total_selling_cost[$in] = $total_selling_cost[$in] + $selling_cost;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                        @endif
                        <td style="text-align:right;">({{ number_format($selling_cost) }})</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Biaya Penjualan</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">({{ number_format($total_selling_cost[$in]) }})</td>
                @endfor
            </tr>
            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Biaya Umum Admin</strong></td>
            </tr>
            @php
                $total_general_fees = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_general_fees, 0);
                }

            @endphp
            @foreach ($data['general_fees'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $general_fees = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 10)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('debet-credit'));
                            $total_general_fees[$in] = $total_general_fees[$in] + $general_fees;
                        @endphp
                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                        @endif
                        <td style="text-align:right;">({{ number_format($general_fees) }})</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Biaya Admin dan Umum</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">({{ number_format($total_general_fees[$in]) }})</td>
                @endfor
            </tr>

            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Pendapatan Diluar Usaha</strong></td>
            </tr>
            @php
                $total_nb_income = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_nb_income, 0);
                }
            @endphp
            @foreach ($data['non_business_income'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $nb_income = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 11)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('credit-debet'));
                            $total_nb_income[$in] = $total_nb_income[$in] + $nb_income;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                        @endif
                        <td style="text-align:right;">{{ number_format($nb_income) }}</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Pendapatan Diluar Usaha</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">{{ number_format($total_nb_income[$in]) }}</td>
                @endfor
            </tr>


            <tr>
                <td colspan="{{ $kolspan1 }}"><strong>Biaya Diluar Usaha</strong></td>
            </tr>
            @php
                $total_nb_cost = [];
                for ($in = 0; $in <= $periode; $in++) {
                    array_push($total_nb_cost, 0);
                }
            @endphp
            @foreach ($data['non_business_cost'] as $a)
                <tr>
                    @for ($in = 0; $in <= $periode; $in++)
                        @php
                            $nb_cost = DB::table('ml_journal_list')
                                ->where('asset_data_id', $a->id)
                                ->where('account_code_id', 12)
                                ->where('created', '>=', $time_array[$in]['awal'])
                                ->where('created', '<=', $time_array[$in]['akhir'])
                                ->sum(\DB::raw('debet-credit'));
                            $total_nb_cost[$in] = $total_nb_cost[$in] + $nb_cost;
                        @endphp

                        @if ($in == 0)
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                        @endif
                        <td style="text-align:right;">({{ number_format($nb_cost) }})</td>
                        <td></td>
                    @endfor
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Biaya Diluar Usaha</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">({{ number_format($total_nb_cost[$in]) }})</td>
                @endfor
            </tr>
            @php

            @endphp
            <tr>
                <td><strong>LABA/RUGI BERSIH</strong></td>
                @for ($in = 0; $in <= $periode; $in++)
                    <td></td>
                    <td style="text-align:right;">
                        <strong>{{ number_format($total_income[$in] - $total_hpp[$in] - $total_selling_cost[$in] - $total_general_fees[$in] + $total_nb_income[$in] - $total_nb_cost[$in]) }}</strong>
                    </td>
                @endfor
            </tr>
        </table>

</body>

</html>
