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

                                    <form class="row" action='{{ route('laporan.absensi.index') }}' method='GET' enctype='multipart/form-data'>
                                        
                                        <div class="col-md-3 me-3">
                                            <select id="yearFilter" name="year" class="form-control" aria-label="Pilih Tahun">
                                                <option value="">Tampilkan Semua Tahun</option>
                                                @foreach (tahun() as $thn)
                                                    <option value="{{ $thn }}"
                                                        {{ now()->year == $thn ? 'selected' : '' }}>
                                                        {{ $thn }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="monthFilter" class="form-control" name="month" aria-label="Pilih Bulan">
                                                <option value="">Tampilkan Semua Bulan</option>
                                                @foreach (bulan() as $key => $bln)
                                                    <option value="{{ $key }}"
                                                        {{ (request('month') ?? now()->month) == $key ? 'selected' : '' }}>
                                                        {{ $bln }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <div class='input-group'>
                                                <input type="hidden" name="user_key" value="{{ $userKey ?? null }}">
                                                {{-- <input type='date' class='form-control' name="date"
                                                    value="{{ request('date') ?? now()->format('Y-m-d') }}" id='date'
                                                    placeholder=''> --}}
                                                <button type='submit' class='btn btn-primary me-2'><i
                                                        class="feather-filter"></i> Filter</button>
                                                <button type="button" class="btn btn-success" id="export-btn"
                                                    onclick="exportData()"><i class="feather-download"></i> Export
                                                    Xls</button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="mtop30"></div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" id="data-table">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="text-center" style="line-height: 4;">Karyawan</th>
                                                    <th rowspan="2" class="text-center" style="line-height: 4;">Jabatan</th>
                                                    <th colspan="{{ $daysInMonth }}" class="text-center">Tanggal</th>
                                                    <th rowspan="2" class="text-center" style="line-height: 0.1;"><p>Jumlah</p> <p>Hari Kerja</p></th>
                                                    <th colspan="5" class="text-center">Keterangan</th>
                                                </tr>
                                                <tr>
                                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                                        <th>{{ $i }}</th>
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
                                                                @if ((int)now()->format('d') < $attendance['date'] && $attendance['month'] == now()->format('m') && $attendance['year'] == now()->format('Y'))
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
                                    </div>
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

                        <form class="row" action='{{ route('laporan.absensi.index') }}' method='GET' enctype='multipart/form-data'>
                                        
                            <div class="col-md-3 me-3">
                                <select id="yearFilter" name="year" class="form-control" aria-label="Pilih Tahun">
                                    <option value="">Tampilkan Semua Tahun</option>
                                    @foreach (tahun() as $thn)
                                        <option value="{{ $thn }}"
                                            {{ now()->year == $thn ? 'selected' : '' }}>
                                            {{ $thn }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="monthFilter" class="form-control" name="month" aria-label="Pilih Bulan">
                                    <option value="">Tampilkan Semua Bulan</option>
                                    @foreach (bulan() as $key => $bln)
                                        <option value="{{ $key }}"
                                            {{ (request('month') ?? now()->month) == $key ? 'selected' : '' }}>
                                            {{ $bln }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class='input-group'>
                                    <input type="hidden" name="user_key" value="{{ $userKey ?? null }}">
                                    {{-- <input type='date' class='form-control' name="date"
                                        value="{{ request('date') ?? now()->format('Y-m-d') }}" id='date'
                                        placeholder=''> --}}
                                    <button type='submit' class='btn btn-primary me-2'><i
                                            class="feather-filter"></i> Filter</button>
                                    <button type="button" class="btn btn-success" id="export-btn"
                                        onclick="exportData()"><i class="feather-download"></i> Export
                                        Xls</button>
                                </div>
                            </div>
                        </form>


                        <div class="mtop30"></div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="data-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center" style="line-height: 4;">Karyawan</th>
                                        <th rowspan="2" class="text-center" style="line-height: 4;">Jabatan</th>
                                        <th colspan="{{ $daysInMonth }}" class="text-center">Tanggal</th>
                                        <th rowspan="2" class="text-center" style="line-height: 0.1;"><p>Jumlah</p> <p>Hari Kerja</p></th>
                                        <th colspan="5" class="text-center">Keterangan</th>
                                    </tr>
                                    <tr>
                                        @for ($i = 1; $i <= $daysInMonth; $i++)
                                            <th>{{ $i }}</th>
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
                                                    @if ((int)now()->format('d') < $attendance['date'] && $attendance['month'] == now()->format('m') && $attendance['year'] == now()->format('Y'))
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
                        </div>
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
            const url = new URL(`${userKey ? '/preview' : ''}/laporan/absensi/data`, window.location.origin);
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
        function exportData() {
            var month = $('#monthFilter').val();
            var year = $('#yearFilter').val();

            var url = "{{ !$userKey ? route('laporan.absensi.export') : route('preview.laporan.absensi.export') }}?month="+month+"&year="+year

            // Menggunakan window.location.href untuk mengunduh file
            window.open(url)
        }
    </script>
@endsection
