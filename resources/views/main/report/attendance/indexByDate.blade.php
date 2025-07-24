@extends(isset($userKey) ? 'master-preview' : 'master')

@section('content')
    @if (!$userKey)
        <main class="nxl-container">
            <div class="nxl-content">
                <!-- [ page-header ] start -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('report') }}">Laporan Absensi</a></li>
                            <li class="breadcrumb-item">Laporan Absensi Builder</li>
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
                                    <h5 class="card-title">Laporan Absensi</h5>
                                </div>
                                <div class="card-body custom-card-action p-3">
                                    @if (session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                    @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <div class="row">
                                        {{-- <div class="col-md-3">
                                            <select id="yearFilter" class="form-control" aria-label="Pilih Tahun">
                                                <option value="">Tampilkan Semua Tahun</option>
                                                @foreach (tahun() as $thn)
                                                    <option value="{{ $thn }}"
                                                        {{ now()->year == $thn ? 'selected' : '' }}>
                                                        {{ $thn }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="monthFilter" class="form-control" aria-label="Pilih Bulan">
                                                <option value="">Tampilkan Semua Bulan</option>
                                                @foreach (bulan() as $key => $bln)
                                                    <option value="{{ $key }}"
                                                        {{ now()->month == $key ? 'selected' : '' }}>
                                                        {{ $bln }}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        <div class="col-md-6">
                                            <div class='input-group'>
                                                <input type="hidden" name="user_key" value="{{ $userKey ?? null }}">
                                                <input type='date' class='form-control' name="date"
                                                    value="{{ request('date') ?? now()->format('Y-m-d') }}" id='date'
                                                    placeholder=''>
                                                <button type='submit' class='btn btn-primary me-2' onclick="filter()"><i
                                                        class="feather-filter"></i> Filter</button>
                                                <button type="button" class="btn btn-success" id="export-btn"
                                                    onclick="exportData()"><i class="feather-download"></i> Export
                                                    Xls</button>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="mtop30"></div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table">
                                            <thead>
                                                <tr>
                                                    <th>Karyawan</th>
                                                    <th>Absensi Masuk</th>
                                                    <th>Absensi Keluar</th>
                                                    <th>Mulai Istirahat</th>
                                                    <th>Selesai Istirahat</th>
                                                    <th>Mulai Lembur</th>
                                                    <th>Selesai Lembur</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    {{-- <div id="laporanAbsensi">
                                @foreach ($data['attendance'] as $item)
                                <hr>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="3">
                                                    Nama Perusahaan/Toko : {{ $item->nama_toko }} <br>
                                                    Nama Staff/Karyawan : {{ $item->nama_staff ?? '-' }} <br>
                                                    Jam Kerja : {{ $item->jam_kerja }} <br>
                                                    Hari Libur : {{ $item->hari_libur }} <br>
                                                    Mulai Bekerja : {{ $item->mulai_kerja }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <th width="350px">Absen Masuk</th>
                                            <td width="10px">:</td>
                                            <td>{{ $item->clock_in }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Keterangan</th>
                                            <td width="10px">:</td>
                                            <td>
                                                @if ($item->range_clock_in > 0)
                                                <span class="text-success">{{ $item->note_clock_in }}</span>
                                                @else
                                                <span class="text-danger">{{ $item->note_clock_in }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Foto Masuk</th>
                                            <td width="10px">:</td>
                                            <td>
                                                <a href="{{ $item->attachment_clock_in }}"
                                                    style="width: 200px; background-color: #385a9c !important;"
                                                    class="btn btn-primary btn-sm" target="_blank">Link Gambar</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Lokasi Masuk</th>
                                            <td width="10px">:</td>
                                            <td>
                                                <a href="{{ $item->location_clock_in }}" class="btn btn-primary btn-sm"
                                                    style="width: 200px; background-color: #385a9c !important;"
                                                    target="_blank">Link Lokasi</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="350px">Absen Pulang</th>
                                            <td width="10px">:</td>
                                            <td>{{ $item->clock_out }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Keterangan</th>
                                            <td width="10px">:</td>
                                            <td>
                                                @if ($item->range_clock_out < 0) <span class="text-success">{{
                                                    $item->note_clock_out }}</span>
                                                    @else
                                                    <span class="text-danger">{{ $item->note_clock_out }}</span>
                                                    @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Foto Pulang</th>
                                            <td width="10px">:</td>
                                            <td>
                                                <a href="{{ $item->attachment_clock_out }}"
                                                    style="width: 200px; background-color: #385a9c !important;"
                                                    class="btn btn-primary btn-sm" target="_blank">Link Gambar</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Lokasi Pulang</th>
                                            <td width="10px">:</td>
                                            <td>
                                                <a href="{{ $item->location_clock_in }}" class="btn btn-primary btn-sm"
                                                    style="width: 200px; background-color: #385a9c !important;"
                                                    target="_blank">Link Lokasi</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                @endforeach

                                <div class="mt-5">
                                    <h3>Karyawan/Staff Tanpa Absen</h3>

                                    @foreach ($data['not_attendance'] as $item)
                                    <hr>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th colspan="3">
                                                        Nama Perusahaan/Toko : {{ $item->nama_toko }} <br>
                                                        Nama Staff/Karyawan : {{ $item->nama_staff ?? '-' }} <br>
                                                        Jam Kerja : {{ $item->jam_kerja }} <br>
                                                        Hari Libur : {{ $item->hari_libur }} <br>
                                                        Mulai Bekerja : {{ $item->mulai_kerja }}
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    @endforeach
                                </div>
                            </div> --}}
                                </div>
                            </div>
                        </div>

                        <!-- [Recent Orders] end -->
                        <!-- [Table] start -->
                        <!-- [Table] end -->
                    </div>
                </div>
            </div>
        </main>
    @else
        <div class="row">
            <!-- [Leads] start -->
            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    @if ($from === 'desktop')
                        <div class="card-header">
                            <h5 class="card-title">Laporan Absensi</h5>
                        </div>
                    @endif
                    <div class="card-body custom-card-action p-3">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class='input-group'>
                                    <input type="hidden" name="user_key" value="{{ $userKey ?? null }}">
                                    <input type='date' class='form-control' name="date"
                                        value="{{ request('date') ?? now()->format('Y-m-d') }}" id='date'
                                        placeholder=''>
                                    <button type='submit' class='btn btn-primary me-2' onclick="filter()"><i
                                            class="feather-filter"></i> Filter</button>
                                    <button type="button" class="btn btn-success" id="export-btn" onclick="exportData()"><i
                                            class="feather-download"></i> Export
                                        Xls</button>
                                </div>
                            </div>
                        </div>


                        <div class="mtop30"></div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Absensi Masuk</th>
                                        <th>Absensi Keluar</th>
                                        <th>Mulai Istirahat</th>
                                        <th>Selesai Istirahat</th>
                                        <th>Mulai Lembur</th>
                                        <th>Selesai Lembur</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        {{-- <div id="laporanAbsensi">
                    @foreach ($data['attendance'] as $item)
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th colspan="3">
                                        Nama Perusahaan/Toko : {{ $item->nama_toko }} <br>
                                        Nama Staff/Karyawan : {{ $item->nama_staff ?? '-' }} <br>
                                        Jam Kerja : {{ $item->jam_kerja }} <br>
                                        Hari Libur : {{ $item->hari_libur }} <br>
                                        Mulai Bekerja : {{ $item->mulai_kerja }}
                                    </th>
                                </tr>
                            </thead>
                            <tr>
                                <th width="350px">Absen Masuk</th>
                                <td width="10px">:</td>
                                <td>{{ $item->clock_in }}</td>
                            </tr>
                            <tr>
                                <th width="350px">Keterangan</th>
                                <td width="10px">:</td>
                                <td>
                                    @if ($item->range_clock_in > 0)
                                    <span class="text-success">{{ $item->note_clock_in }}</span>
                                    @else
                                    <span class="text-danger">{{ $item->note_clock_in }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th width="350px">Foto Masuk</th>
                                <td width="10px">:</td>
                                <td>
                                    <a href="{{ $item->attachment_clock_in }}"
                                        style="width: 200px; background-color: #385a9c !important;"
                                        class="btn btn-primary btn-sm" target="_blank">Link Gambar</a>
                                </td>
                            </tr>
                            <tr>
                                <th width="350px">Lokasi Masuk</th>
                                <td width="10px">:</td>
                                <td>
                                    <a href="{{ $item->location_clock_in }}" class="btn btn-primary btn-sm"
                                        style="width: 200px; background-color: #385a9c !important;"
                                        target="_blank">Link Lokasi</a>
                                </td>
                            </tr>

                            <tr>
                                <th width="350px">Absen Pulang</th>
                                <td width="10px">:</td>
                                <td>{{ $item->clock_out }}</td>
                            </tr>
                            <tr>
                                <th width="350px">Keterangan</th>
                                <td width="10px">:</td>
                                <td>
                                    @if ($item->range_clock_out < 0) <span class="text-success">{{
                                        $item->note_clock_out }}</span>
                                        @else
                                        <span class="text-danger">{{ $item->note_clock_out }}</span>
                                        @endif
                                </td>
                            </tr>
                            <tr>
                                <th width="350px">Foto Pulang</th>
                                <td width="10px">:</td>
                                <td>
                                    <a href="{{ $item->attachment_clock_out }}"
                                        style="width: 200px; background-color: #385a9c !important;"
                                        class="btn btn-primary btn-sm" target="_blank">Link Gambar</a>
                                </td>
                            </tr>
                            <tr>
                                <th width="350px">Lokasi Pulang</th>
                                <td width="10px">:</td>
                                <td>
                                    <a href="{{ $item->location_clock_in }}" class="btn btn-primary btn-sm"
                                        style="width: 200px; background-color: #385a9c !important;"
                                        target="_blank">Link Lokasi</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endforeach

                    <div class="mt-5">
                        <h3>Karyawan/Staff Tanpa Absen</h3>

                        @foreach ($data['not_attendance'] as $item)
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="3">
                                            Nama Perusahaan/Toko : {{ $item->nama_toko }} <br>
                                            Nama Staff/Karyawan : {{ $item->nama_staff ?? '-' }} <br>
                                            Jam Kerja : {{ $item->jam_kerja }} <br>
                                            Hari Libur : {{ $item->hari_libur }} <br>
                                            Mulai Bekerja : {{ $item->mulai_kerja }}
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        @endforeach
                    </div>
                </div> --}}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal untuk menampilkan attachment -->
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #2f467a;">
                    <h5 class="modal-title" style="color:white;" id="attachmentModalLabel"></h5>
                    <button type="button" data-bs-dismiss="modal" aria-label="Close"
                        style="color:white; opacity: 1; background-color: #2f467a;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="attachmentContent"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        const userKey = '{{ $userKey ?? null }}'
        document.getElementById('absensiForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah form submit secara default

            // Ambil data dari form
            const form = e.target;
            const date = form.querySelector('input[name="date"]').value;
            const userKey = form.querySelector('input[name="user_key"]').value;

            // Buat URL dengan query string
            const url = new URL(`${userKey ? '/preview' : ''}/laporan/absensi/data-by-date`, window.location.origin);
            url.searchParams.append('date', date);
            url.searchParams.append('all', true);
            if (userKey) {
                url.searchParams.append('user_key', userKey);
            }

            // Kirim request AJAX dengan metode GET dan query string
            fetch(url, {
                    method: 'GET',
                })
                .then(response => response.json()) // Mengambil respons sebagai JSON
                .then(data => {
                    // Update konten halaman dengan data yang diterima
                    let content = '';

                    data.attendance.forEach(item => {
                        if (item.range_clock_out < 0) {
                            var color_out = 'text-success';
                        } else {
                            var color_out = 'text-danger';
                        }

                        if (item.range_clock_in > 0) {
                            var color_in = 'text-success';
                        } else {
                            var color_in = 'text-danger';
                        }

                        content += `
                    <hr>
                    <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="3">
                                            Nama Perusahaan/Toko : ${item.nama_toko} <br>
                                            Nama Staff/Karyawan : ${item.nama_staff} <br>
                                            Jam Kerja : ${item.jam_kerja} <br>
                                            Hari Libur : ${item.hari_libur} <br>
                                            Mulai Bekerja : ${item.mulai_kerja}
                                        </th>
                                    </tr>
                                </thead>
                                <tr>
                                    <th width="350px">Absen Masuk</th>
                                    <td width="10px">:</td>
                                    <td>${item.clock_in}</td>
                                </tr>
                                <tr>
                                    <th width="350px">Keterangan</th>
                                    <td width="10px">:</td>
                                    <td>
                                        <span class="${color_in}">${item.note_clock_in}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="350px">Foto Masuk</th>
                                    <td width="10px">:</td>
                                    <td>
                                        <a href="${item.attachment_clock_in}" class="btn btn-primary btn-sm" style="width: 200px; background-color: #385a9c !important;"
                                            target="_blank">Link Gambar</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="350px">Lokasi Masuk</th>
                                    <td width="10px">:</td>
                                    <td>
                                        <a href="${item.location_clock_in}" class="btn btn-primary btn-sm" style="width: 200px; background-color: #385a9c !important;"
                                            target="_blank">Link Lokasi</a>
                                    </td>
                                </tr>

                                <tr>
                                    <th width="350px">Absen Pulang</th>
                                    <td width="10px">:</td>
                                    <td>${item.clock_out}</td>
                                </tr>
                                <tr>
                                    <th width="350px">Keterangan</th>
                                    <td width="10px">:</td>
                                    <td>
                                        <span class="${color_out}">${item.note_clock_out}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="350px">Foto Pulang</th>
                                    <td width="10px">:</td>
                                    <td>
                                        <a href="${item.attachment_clock_out}" class="btn btn-primary btn-sm" style="width: 200px; background-color: #385a9c !important;"
                                            target="_blank">Link Gambar</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="350px">Lokasi Pulang</th>
                                    <td width="10px">:</td>
                                    <td>
                                        <a href="${item.location_clock_in}" class="btn btn-primary btn-sm" style="width: 200px; background-color: #385a9c !important;"
                                            target="_blank">Link Lokasi</a>
                                    </td>
                                </tr>
                            </table>
                        </div>`;
                    });

                    content += `<div class="mt-5">
                        <h3>Karyawan/Staff Tanpa Absen</h3>`;

                    data.not_attendance.forEach(item => {
                        if (item.range_clock_out < 0) {
                            var color_out = 'text-success';
                        } else {
                            var color_out = 'text-danger';
                        }

                        if (item.range_clock_in > 0) {
                            var color_in = 'text-success';
                        } else {
                            var color_in = 'text-danger';
                        }

                        content += `
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="3">
                                            Nama Perusahaan/Toko : ${item.nama_toko} <br>
                                            Nama Staff/Karyawan : ${item.nama_staff} <br>
                                            Jam Kerja : ${item.jam_kerja} <br>
                                            Hari Libur : ${item.hari_libur} <br>
                                            Mulai Bekerja : ${item.mulai_kerja}
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>`;
                    });

                    content += `</div>`;

                    document.getElementById('laporanAbsensi').innerHTML = content;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>

    <script>
        $(document).ready(function() {
            init_table();
        });

        function filter() {
            var keyword = $('#searchData').val();
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();
            var date = $('#date').val();

            init_table(keyword, month, year, date);
        }

        function exportData() {
            var date = $('#date').val();

            var url = "{{ !$userKey ? route('laporan.absensi.exportByDate') : route('preview.laporan.absensi.exportByDate') }}?date=" +
                encodeURIComponent(date) + "&user_key={{ $userKey ?? '' }}";

            // Menggunakan window.location.href untuk mengunduh file
            window.open(url)
        }

        $(document).on('input', '#searchData', function() {
            filter();
        })

        function init_table(keyword = '', month = '', year = '', date = '') {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#data-table');
            table.destroy();

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Blfrtip',
                columnDefs: [{
                    target: 0,
                    visible: true,
                    searchable: false
                }, ],

                ajax: {
                    type: "GET",
                    url: "{{ !$userKey ? route('laporan.absensi.dataByDate') : route('preview.laporan.absensi.dataByDate') }}",
                    data: {
                        'keyword': keyword,
                        'date': date,
                        'month': month,
                        'year': year,
                        'user_key': '{{ $userKey }}'
                    }
                },
                columns: [{
                        data: 'detail_user',
                        name: 'detail_user'
                    },
                    {
                        data: 'clock_in',
                        name: 'clock_in'
                    },
                    {
                        data: 'clock_out',
                        name: 'clock_out'
                    },
                    {
                        data: 'start_rest',
                        name: 'start_rest'
                    },
                    {
                        data: 'end_rest',
                        name: 'end_rest'
                    },
                    {
                        data: 'start_overtime',
                        name: 'start_overtime'
                    },
                    {
                        data: 'end_overtime',
                        name: 'end_overtime'
                    },

                ]
            });
        }

        function showAttachmentModal(attachmentUrl, name) {
            var attachmentContent = '<img src="' + attachmentUrl + '" alt="Attachment Clock In" style="width: 100%;">';

            document.getElementById('attachmentContent').innerHTML = attachmentContent;
            document.getElementById('attachmentModalLabel').innerHTML = name;

            $('#attachmentModal').modal('show');
        }
    </script>
@endsection
