<table class="table table-striped" id="data-table">
    <thead>
        <tr>
            <th rowspan="2" class="text-center" style="line-height: 4;">Karyawan</th>
            <th rowspan="2" class="text-center" style="line-height: 4;">Jabatan</th>
            <th colspan="{{ $daysInMonth }}" class="text-center">Tanggal</th>
            <th rowspan="2" class="text-center" style="line-height: 0.1;">
                <p>Jumlah</p>
                <p>Hari Kerja</p>
            </th>
            <th colspan="5" class="text-center">Keterangan</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= $daysInMonth; $i++) <th>{{ $i }}</th>
                @endfor
                <th>H</th>
                <th>L</th>
                <th>A</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $user_id => $user)
        @php
        $ml_account = \App\Models\MLAccount::find($user_id);
        $count_h = 0;
        $count_l = 0;
        $count_a = 0;
        @endphp

        <tr>
            <td>{{ $ml_account->fullname }}</td>
            <td class="text-center">{{ $ml_account->position }}</td>

            @foreach ($user as $attendance)
            @if ($attendance['holiday'] == true)
            <td class="text-center text-white" style="--bs-table-accent-bg: red !important;">L</td>

            @php
            $count_l++;
            @endphp
            @else
            @if ((int)now()->format('d') < $attendance['date'] && $attendance['month']==now()->format('m') &&
                $attendance['year'] == now()->format('Y'))
                <td>-</td>
                @else
                @if ($attendance['attendance'])
                <td class="text-center">H</td>

                @php
                $count_h++;
                @endphp
                @else
                <td class="text-center">A</td>

                @php
                $count_a++;
                @endphp
                @endif
                @endif
                @endif
                @endforeach
                <td class="text-center">{{ $daysInMonth }}</td>
                <td class="text-center">{{ $count_h }}</td>
                <td class="text-center">{{ $count_l }}</td>
                <td class="text-center">{{ $count_a }}</td>
        </tr>
        @endforeach
    </tbody>
</table>