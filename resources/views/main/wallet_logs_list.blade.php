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
                        <li class="breadcrumb-item"><a href="{{ url('wallet-logs') }}">Riwayat Transaksi</a></li>
                        <li class="breadcrumb-item">Lihat Riwayat</li>
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
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper"> </div>
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
                            <div class="col-12 col-md-6">
                                <div class="card stretch-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0">Total Transaksi Withdrawal: </p>
                                                <h3 class="display-6" id="sumOmset1">@currency($sum1)</h3>
                                            </div>

                                            <div class="d-flex justify-content-end w-50">
                                                <div class="me-3" style="min-width: 250px">
                                                    <select id="yearFilter1" class="form-select w-100"
                                                        aria-label="Pilih Tahun">
                                                        <option value="0">Tampilkan Semua Tahun</option>
                                                        @foreach ($tahun as $thn)
                                                            <option value="{{ $thn }}">{{ $thn }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="min-width: 250px">
                                                    <select id="monthFilter1" class="form-select w-100"
                                                        aria-label="Pilih Bulan" disabled>
                                                        <option value="0">Tampilkan Semua Bulan</option>
                                                        @foreach ($bulan as $key => $bln)
                                                            <option value="{{ $key + 1 }}">{{ $bln }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card stretch-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0">Total Transaksi Randu Wallet (Complete): </p>
                                                <h3 class="display-6" id="sumOmset2">@currency($sum2)</h3>
                                            </div>

                                            <div class="d-flex justify-content-end w-50">
                                                <div class="me-3" style="min-width: 250px">
                                                    <select id="yearFilter2" class="form-select w-100"
                                                        aria-label="Pilih Tahun">
                                                        <option value="0">Tampilkan Semua Tahun</option>
                                                        @foreach ($tahun as $thn)
                                                            <option value="{{ $thn }}">{{ $thn }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="min-width: 250px">
                                                    <select id="monthFilter2" class="form-select w-100"
                                                        aria-label="Pilih Bulan" disabled>
                                                        <option value="0">Tampilkan Semua Bulan</option>
                                                        @foreach ($bulan as $key => $bln)
                                                            <option value="{{ $key + 1 }}">{{ $bln }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card stretch-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0">Total Transaksi Randu Wallet (Process): </p>
                                                <h3 class="display-6" id="sumOmset3">@currency($sum3)</h3>
                                            </div>

                                            <div class="d-flex justify-content-end w-50">
                                                <div class="me-3" style="min-width: 250px">
                                                    <select id="yearFilter3" class="form-select w-100"
                                                        aria-label="Pilih Tahun">
                                                        <option value="0">Tampilkan Semua Tahun</option>
                                                        @foreach ($tahun as $thn)
                                                            <option value="{{ $thn }}">{{ $thn }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="min-width: 250px">
                                                    <select id="monthFilter3" class="form-select w-100"
                                                        aria-label="Pilih Bulan" disabled>
                                                        <option value="0">Tampilkan Semua Bulan</option>
                                                        @foreach ($bulan as $key => $bln)
                                                            <option value="{{ $key + 1 }}">{{ $bln }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card stretch-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0">Total Transaksi Randu Wallet (Waiting): </p>
                                                <h3 class="display-6" id="sumOmset4">@currency($sum4)</h3>
                                            </div>

                                            <div class="d-flex justify-content-end w-50">
                                                <div class="me-3" style="min-width: 250px">
                                                    <select id="yearFilter4" class="form-select w-100"
                                                        aria-label="Pilih Tahun">
                                                        <option value="0">Tampilkan Semua Tahun</option>
                                                        @foreach ($tahun as $thn)
                                                            <option value="{{ $thn }}">{{ $thn }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div style="min-width: 250px">
                                                    <select id="monthFilter4" class="form-select w-100"
                                                        aria-label="Pilih Bulan" disabled>
                                                        <option value="0">Tampilkan Semua Bulan</option>
                                                        @foreach ($bulan as $key => $bln)
                                                            <option value="{{ $key + 1 }}">{{ $bln }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Riwayat Transaksi</h5>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modal-withdraw">
                                        Withdraw
                                    </button>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#modal-topup">
                                        Topup
                                    </button>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="main-box">
                                    <div class="mtop40"></div>
                                    <div class="table-responsive">
                                        <table id="wallet-logs-table" class="table table-striped mb-0 table-bordered">
                                            <thead>
                                                <tr class="border-b">
                                                    <th width="10%">No</th>
                                                    <th width="30%">Kode Transaksi</th>
                                                    <th width="30%">Catatan</th>
                                                    <th width="30%">Jumlah Transaksi</th>
                                                    <th width="20%">Tipe</th>
                                                    <th width="20%">Tanggal Diperbaharui</th>
                                                    <th width="20%">Tanggal Dibuat</th>
                                                    <th width="0%">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="mtop30"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="modal-topup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Topup Wallet</h1>
                </div>
                <form action="{{ route('topup.duitku') }}" method="POST">
                    <div class="modal-body">
                        <div>
                            Minimum topup sebesar {{ $min_topup_in_rp }}
                        </div>
                        <div>
                            Biaya Admin {{ $fee_payment_gateway }}%
                        </div>
                        @csrf
                        <label for="topup-log" class="form-label mt-3">Masukkan Jumlah Topup</label>
                        <select class="form-select @error('amount') is-invalid @enderror" name="amount" required
                            id="topup-select">
                            <option selected disabled>Pilih Jumlah Rp.</option>
                            <option value="10000">@currency(10000)</option>
                            <option value="50000">@currency(50000)</option>
                            <option value="100000">@currency(100000)</option>
                            <option value="200000">@currency(200000)</option>
                            <option value="500000">@currency(500000)</option>
                            <option value="1000000">@currency(1000000)</option>
                            <option value="2000000">@currency(2000000)</option>
                            <option value="5000000">@currency(5000000)</option>
                            <option value="10000000">@currency(10000000)</option>
                        </select>
                        <input type="hidden" name="topup_charge" id="topup-charge" />
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="total-charge" class="mt-2"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Topup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-withdraw" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Withdraw Wallet</h1>
                </div>
                <form action="{{ route('withdraw.duitku') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        @if ($latestBalance)
                            <div>Saldo Anda: @currency($latestBalance->balance)</div>
                        @else
                            <div>@currency(0)</div>
                        @endif
                        <div>
                            Biaya penarikan sebesar {{ $fee_withdraw_in_rp }}
                        </div>
                        <div>
                            Minimum penarikan sebesar {{ $min_withdraw_in_rp }}
                        </div>
                        <div>
                            Maximum penarikan sebesar Rp 50.000.000
                        </div>

                        <label for="withdraw-log" class="form-label mt-3">Masukkan Jumlah Penarikan</label>
                        <input type="text" id="withdraw-log"
                            class="form form-control @error('amount') is-invalid @enderror" min="{{ $min_withdraw }}"
                            name="amount" max="{{ $latestBalance->balance ?? 0 }}" required />
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Withdraw</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#wallet-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('wallet-logs.data') }}',
                columns: [{
                        data: null, // This will be replaced by rowCallback
                        orderable: false,
                        searchable: false,
                        name: 'DT_RowIndex'
                    }, {
                        data: 'reference',
                        name: 'reference',
                    }, {
                        data: 'note',
                        name: 'note',
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'payment_at',
                        name: 'payment_at',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                ],
                rowCallback: function(row, data, index) {
                    var pageInfo = table.page.info();
                    $('td:eq(0)', row).html(pageInfo.start + index +
                        1); // Set the number in the second column
                },
                paging: true,
            })

            function fetchFilteredData(year, month, sumOf, targetElement) {
                $.ajax({
                    url: '{{ route('wallet-logs.data.filter') }}',
                    method: 'GET',
                    data: {
                        year: year,
                        month: month,
                        sumOf: sumOf
                    },
                    success: function(response) {
                        $(targetElement).text(new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(response.sum));
                    }
                });
            }

            $('#yearFilter1').change(function() {
                var year = $(this).val();
                if (year != 0) {
                    $('#monthFilter1').prop('disabled', false);
                } else {
                    $('#monthFilter1').prop('disabled', true).val(0);
                }
                fetchFilteredData(year, $('#monthFilter1').val(), 1, '#sumOmset1');
            });

            $('#monthFilter1').change(function() {
                fetchFilteredData($('#yearFilter1').val(), $(this).val(), 1, '#sumOmset1');
            });

            $('#yearFilter2').change(function() {
                var year = $(this).val();
                if (year != 0) {
                    $('#monthFilter2').prop('disabled', false);
                } else {
                    $('#monthFilter2').prop('disabled', true).val(0);
                }
                fetchFilteredData(year, $('#monthFilter2').val(), 2, '#sumOmset2');
            });

            $('#monthFilter2').change(function() {
                fetchFilteredData($('#yearFilter2').val(), $(this).val(), 2, '#sumOmset2');
            });

            $('#yearFilter3').change(function() {
                var year = $(this).val();
                if (year != 0) {
                    $('#monthFilter3').prop('disabled', false);
                } else {
                    $('#monthFilter3').prop('disabled', true).val(0);
                }
                fetchFilteredData(year, $('#monthFilter3').val(), 3, '#sumOmset3');
            });

            $('#monthFilter4').change(function() {
                fetchFilteredData($('#yearFilter4').val(), $(this).val(), 3, '#sumOmset4');
            });

            $('#yearFilter4').change(function() {
                var year = $(this).val();
                if (year != 0) {
                    $('#monthFilter4').prop('disabled', false);
                } else {
                    $('#monthFilter4').prop('disabled', true).val(0);
                }
                fetchFilteredData(year, $('#monthFilter4').val(), 4, '#sumOmset4');
            });

            $('#monthFilter4').change(function() {
                fetchFilteredData($('#yearFilter4').val(), $(this).val(), 4, '#sumOmset4');
            });
        })

        document.addEventListener('DOMContentLoaded', (event) => {
            const input = document.getElementById('withdraw-log');

            input.addEventListener('input', (event) => {
                let value = input.value.replace(/\D/g, '');
                value = new Intl.NumberFormat('en-US').format(value);
                input.value = value;
            });

            input.addEventListener('blur', (event) => {
                let value = input.value.replace(/\D/g, '');
                if (value) {
                    input.value = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                }
            });

            const selectElement = document.getElementById('topup-select');
            const totalChargeDiv = document.getElementById('total-charge');
            const feePercentage = {{ $fee_payment_gateway }} / 100;

            selectElement.addEventListener('change', function() {
                const amount = parseInt(this.value);
                const fee = amount * feePercentage;
                const total = amount + fee;
                $('#topup-charge').val(fee.toFixed(0))
                totalChargeDiv.textContent = 'Total Charge: ' + formatRupiah(total);
            });

            function formatRupiah(number) {
                return 'Rp ' + number.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).replace('IDR', '').trim();
            }
        });
    </script>
@endsection
