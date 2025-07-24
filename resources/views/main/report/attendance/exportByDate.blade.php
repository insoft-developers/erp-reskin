<table class="table table-striped" id="data-table">
    <thead>
        <tr>
            <td>Nama Perusahaan/Toko</td>
            <td>Nama Staff/Karyawan</td>
            <td>Jam Kerja</td>
            <td>Hari Libur</td>
            <td>Mulai Bekerja</td>

            <th>Absensi Masuk</th>
            <th>Lokasi Masuk</th>
            <th>Foto Masuk</th>

            <th>Absensi Keluar</th>
            <th>Lokasi Keluar</th>
            <th>Foto Keluar</th>

            <th>Mulai Istirahat</th>
            <th>Lokasi Istirahat</th>
            <th>Foto Istirahat</th>

            <th>Selesai Istirahat</th>
            <th>Lokasi Istirahat</th>
            <th>Foto Istirahat</th>

            <th>Mulai Lembur</th>
            <th>Lokasi Lembur</th>
            <th>Foto Lembur</th>

            <th>Selesai Lembur</th>
            <th>Lokasi Lembur</th>
            <th>Foto Lembur</th>

            <th>Catatan Absen Masuk</th>
            <th>Catatan Absen Pulang</th>
            <th>Catatan Istirahat</th>
            <th>Catatan Lembur</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $item->nama_toko }}</td>
                <td>{{ $item->nama_staff }}</td>
                <td>{{ $item->jam_kerja }}</td>
                <td>{{ $item->hari_libur }}</td>
                <td>{{ $item->mulai_kerja }}</td>

                <td>{{ $item->clock_in }}</td>
                <td>{{ $item->location_clock_in }}</td>
                <td>{{ $item->attachment_clock_in }}</td>

                <td>{{ $item->clock_out }}</td>
                <td>{{ $item->location_clock_out }}</td>
                <td>{{ $item->attachment_clock_out }}</td>

                <td>{{ ($item->start_rest == null) ? null : Carbon\Carbon::parse($item->start_rest)->format('H:i') }}</td>
                <td>{{ $item->location_start_rest }}</td>
                <td>{{ $item->attachment_start_rest }}</td>

                <td>{{ ($item->end_rest == null) ? null : Carbon\Carbon::parse($item->end_rest)->format('H:i') }}</td>
                <td>{{ $item->location_end_rest }}</td>
                <td>{{ $item->attachment_end_rest }}</td>

                <td>{{ ($item->start_overtime == null) ? null : Carbon\Carbon::parse($item->start_overtime)->format('H:i') }}</td>
                <td>{{ $item->location_start_overtime }}</td>
                <td>{{ $item->attachment_start_overtime }}</td>

                <td>{{ ($item->end_overtime == null) ? null : Carbon\Carbon::parse($item->end_overtime)->format('H:i') }}</td>
                <td>{{ $item->location_end_overtime }}</td>
                <td>{{ $item->attachment_end_overtime }}</td>

                <td>{{ $item->note_clock_in }}</td>
                <td>{{ $item->note_clock_out }}</td>
                <td>{{ $item->note_start_rest }}</td>
                <td>{{ $item->note_end_rest }}</td>
            </tr>
        @endforeach
    </tbody>
</table>