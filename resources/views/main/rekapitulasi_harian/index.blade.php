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
                            <li class="breadcrumb-item"><a href="{{ route('rekapitulasi-harian.index') }}">Rekapitulasi
                                    Harian</a></li>
                            <li class="breadcrumb-item">Rekapitulasi Harian Builder</li>
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
                                    <h5 class="card-title">Rekapitulasi Harian</h5>
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
                                        <div class="col-md-6">
                                            <form id="rekapitulasiForm" action="{{ route('rekapitulasi-harian.index') }}"
                                                method='GET' enctype='multipart/form-data'>
                                                <div class='input-group'>
                                                    <input type="hidden" name="user_key" value="{{ $userKey ?? null }}">
                                                    <input type='date' class='form-control' name="date"
                                                        value="{{ request('date') ?? now()->format('Y-m-d') }}"
                                                        id='' placeholder=''>
                                                    <button type='submit' class='btn btn-primary'>Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>


                                    <div class="mtop30"></div>

                                    <div id="rekapitulasiContent">
                                        @foreach ($data as $item)
                                            <hr>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="3">
                                                                Nama Toko : {{ $item->nama_toko }}, <br>
                                                                Staff : {{ $item->user->fullname ?? '-' }}, <br>
                                                                Buka Kasir : {{ $item->kasKecil->open_cashier_at ?? null }}, <br>
                                                                Tutup Kasir : {{ $item->kasKecil->close_cashier_at ?? null }},
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tr>
                                                        <th width="350px">Kas Awal</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp. {{ number_format($item->initial_cash, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th width="350px">Penjualan Tunai</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp. {{ number_format($item->cash_sale, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th width="350px">Penjualan Transfer/EDC</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp. {{ number_format($item->transfer_sales, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th width="350px">Penjualan Payment Gateway</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp.
                                                            {{ number_format($item->payment_gateway_sales, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    {{-- <tr>
                                                <th width="350px">Penjualan COD</th>
                                                <td width="10px">:</td>
                                                <td>Rp. {{ number_format($item->cod_sales, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th width="350px">Penjualan Marketplace</th>
                                                <td width="10px">:</td>
                                                <td>Rp. {{ number_format($item->marketplace_sales, 0, ',', '.') }}</td>
                                            </tr> --}}
                                                    <tr>
                                                        <th width="350px">Penjualan Piutang</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp. {{ number_format($item->piutang_sales, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th width="350px">Pengeluaran Outlet</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp. {{ number_format($item->outlet_output, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th width="350px">Total Kas Tunai</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp. {{ number_format($item->total_cash, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th width="350px">Total Penjualan</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp. {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th width="350px">Total Penjualan + Kas Awal - Pengeluaran</th>
                                                        <td width="10px">:</td>
                                                        <td>Rp.
                                                            {{ number_format($item->total_sales + $item->initial_cash - $item->outlet_output, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- [Recent Orders] end -->
                        <!-- [Table] start -->
                        <!-- [Table] end -->
                    </div>

                </div>
                <!-- [ Main Content ] end -->

            </div>
        </main>
    @else
        <div class="row">
            <!-- [Leads] start -->
            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    @if($from === 'desktop')
                    <div class="card-header">
                        <h5 class="card-title">Rekapitulasi Harian</h5>
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
                                <form id="rekapitulasiForm"
                                    method='GET' enctype='multipart/form-data'>
                                    <input type="hidden" name="user_key" value="{{ $userKey ?? null }}">
                                    <div class='input-group'>
                                        <input type='date' class='form-control' name="date"
                                            value="{{ request('date') ?? now()->format('Y-m-d') }}" id=''
                                            placeholder=''>
                                        <button type='submit' class='btn btn-primary'>Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="mtop30"></div>
                        <div id="rekapitulasiContent">
                            @foreach ($data as $item)
                                <hr>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="3">
                                                    Nama Toko : {{ $item->nama_toko }}, <br>
                                                    Staff : {{ $item->user->fullname ?? '-' }}, <br>
                                                    Buka Kasir : {{ $item->kasKecil->open_cashier_at }}, <br>
                                                    Tutup Kasir : {{ $item->kasKecil->close_cashier_at }},
                                                </th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <th width="350px">Kas Awal</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->initial_cash, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Penjualan Tunai</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->cash_sale, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Penjualan Transfer/EDC</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->transfer_sales, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Penjualan Payment Gateway</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->payment_gateway_sales, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Penjualan Piutang</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->piutang_sales, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Pengeluaran Outlet</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->outlet_output, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Total Kas Tunai</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->total_cash, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Total Penjualan</th>
                                            <td width="10px">:</td>
                                            <td>Rp. {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th width="350px">Total Penjualan + Kas Awal</th>
                                            <td width="10px">:</td>
                                            <td>Rp.
                                                {{ number_format($item->total_sales + $item->initial_cash, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODALS --}}
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-ce">
        <div class="modal-dialog modal-lg" id="content-modal-ce">

        </div>
    </div>
@endsection
@section('js')
    {{-- <script>
        const userKey = '{{$userKey ?? null}}'
        document.getElementById('rekapitulasiForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah form submit secara default

            // Ambil data dari form
            const form = e.target;
            const date = form.querySelector('input[name="date"]').value;
            const userKey = form.querySelector('input[name="user_key"]').value;

            // Buat URL dengan query string
            const url = new URL(`${userKey ? '/api' : ''}/rekapitulasi-harian-data`, window.location.origin);
            url.searchParams.append('date', date);
            if (userKey) {
                url.searchParams.append('user_key', userKey);
            }
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

                    data.forEach(item => {
                        content += `
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="3">
                                    Nama Toko : ${item.nama_toko}, <br>
                                    Staff : ${item.user ? item.user.fullname : '-'}, <br>
                                    Buka Kasir : ${item.kasKecil ? item.kasKecil.open_cashier_at : '-'}, <br>
                                    Tutup Kasir : ${item.kasKecil ? item.kasKecil.close_cashier_at : '-'},
                                </th>
                            </tr>
                        </thead>
                        <tr>
                            <th width="350px">Kas Awal</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.initial_cash)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Penjualan Tunai</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.cash_sale)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Penjualan Transfer/EDC</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.transfer_sales)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Penjualan Payment Gateway</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.payment_gateway_sales)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Penjualan Piutang</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.piutang_sales)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Pengeluaran Outlet</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.outlet_output)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Total Kas Tunai</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.total_cash)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Total Penjualan</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.total_sales)}</td>
                        </tr>
                        <tr>
                            <th width="350px">Total Penjualan + Kas Awal</th>
                            <td width="10px">:</td>
                            <td>Rp. ${new Intl.NumberFormat().format(item.total_sales + item.initial_cash)}</td>
                        </tr>
                    </table>
                </div>`;
                    });

                    document.getElementById('rekapitulasiContent').innerHTML = content;
                })
                .catch(error => console.error('Error:', error));
        });
    </script> --}}
@endsection
