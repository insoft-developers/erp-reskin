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
                        <li class="breadcrumb-item"><a href="{{ url('report') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">Pembayaran</li>
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
                @if (session()->has('success'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-success">
                                {!! session('success') !!}
                            </div>
                        </div>
                    </div>
                @elseif (session()->has('warning'))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="alert alert-warning">
                                {!! session('warning') !!}
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Pengaturan Pembayaran</h5>

                            </div>
                            <div class="card-body custom-card-action">

                                <div class="mtop30 main-box">

                                    <div class="row">
                                        <div id="jsonOutput" hidden=""></div>
                                        <div class="col-md-12" style="display: inline-flex">
                                            <div class="mb-3 ml-3 row">

                                                <div id="paymentMethods">
                                                    <div class="paymentMethod form-check form-switch" data-method="Cash"
                                                        checked>
                                                        <input type="checkbox" class="paymentCheckbox form-check-input"
                                                            type="checkbox" id="Cash"> Kas / Bayar Tunai di Kasir
                                                    </div>
                                                    <div class="paymentMethod form-check form-switch"
                                                        data-method="Online-Payment">
                                                        <input type="checkbox" class="paymentCheckbox form-check-input"
                                                            type="checkbox" id="Online-Payment"> Payment Gateway Randu
                                                        Wallet (Cek Otomatis)
                                                    </div>
                                                    <div class="paymentMethod form-check form-switch"
                                                        data-method="Transfer">
                                                        <input type="checkbox" class="paymentCheckbox form-check-input"
                                                            type="checkbox" id="Transfer"> Transfer Rekening / EDC / QRIS
                                                        Toko
                                                        (Cek Manual)
                                                        <div class="banks row d-none" id="banks">

                                                            <div class="bank" data-bank="1">
                                                                <div class="bankDetails row mt-3">
                                                                    <div class="col-sm-1 d-flex">
                                                                        <input type="checkbox" class="bankCheckbox"
                                                                            id="checkBank1">
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <input type="text" class="form-control bankName"
                                                                            id="inputBank1" value="Bank BCA" readonly>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text" class="bankOwner form-control"
                                                                            placeholder="Nama Pemilik Rekening"
                                                                            id="ownerBank1" />
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankAccountNumber form-control"
                                                                            placeholder="Nomor Rekening"
                                                                            id="accountBank1" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="bank" data-bank="2">
                                                                <div class="bankDetails row mt-3">
                                                                    <div class="col-sm-1 d-flex">
                                                                        <input type="checkbox" class="bankCheckbox"
                                                                            id="checkBank2">
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <input type="text" class="form-control bankName"
                                                                            id="inputBank2" value="Bank Mandiri" readonly>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text" class="bankOwner form-control"
                                                                            placeholder="Nama Pemilik Rekening"
                                                                            id="ownerBank2" />
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankAccountNumber form-control"
                                                                            placeholder="Nomor Rekening"
                                                                            id="accountBank2" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="bank" data-bank="3">
                                                                <div class="bankDetails row mt-3">
                                                                    <div class="col-sm-1 d-flex">
                                                                        <input type="checkbox" class="bankCheckbox p3"
                                                                            id="checkBank3">
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <input type="text"
                                                                            class="form-control bankName" id="inputBank3"
                                                                            value="Bank BNI" readonly>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankOwner form-control"
                                                                            placeholder="Nama Pemilik Rekening"
                                                                            id="ownerBank3" />
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankAccountNumber form-control"
                                                                            placeholder="Nomor Rekening"
                                                                            id="accountBank3" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="bank" data-bank="4">
                                                                <div class="bankDetails row mt-3">
                                                                    <div class="col-sm-1 d-flex">
                                                                        <input type="checkbox" class="bankCheckbox p3"
                                                                            id="checkBank4">
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <input type="text"
                                                                            class="form-control bankName" id="inputBank4"
                                                                            value="Bank BRI" readonly>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankOwner form-control"
                                                                            placeholder="Nama Pemilik Rekening"
                                                                            id="ownerBank4" />
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankAccountNumber form-control"
                                                                            placeholder="Nomor Rekening"
                                                                            id="accountBank4" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="bank" data-bank="5">
                                                                <div class="bankDetails row mt-3 mb-3">
                                                                    <div class="col-sm-1 d-flex">
                                                                        <input type="checkbox" class="bankCheckbox p-3"
                                                                            id="checkBank5">
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <input type="text"
                                                                            class="form-control bankName" id="inputBank5"
                                                                            placeholder="Nama Bank">
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankOwner form-control"
                                                                            placeholder="Nama Pemilik Rekening"
                                                                            id="ownerBank5" />
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text"
                                                                            class="bankAccountNumber form-control"
                                                                            placeholder="Nomor Rekening"
                                                                            id="accountBank5" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Add more banks as needed -->

                                                        </div>
                                                    </div>
                                                    <div class="paymentMethod form-check form-switch" data-method="COD">
                                                        <input type="checkbox" class="paymentCheckbox form-check-input"
                                                            type="checkbox" id="COD"> Piutang Cash on Delivery (COD)
                                                    </div>
                                                    <div class="paymentMethod form-check form-switch"
                                                        data-method="Marketplace">
                                                        <input type="checkbox" class="paymentCheckbox form-check-input"
                                                            type="checkbox" id="Marketplace"> Piutang Marketplace (Shopee,
                                                        Tokopedia, GrabFood, GoFood DLL)
                                                    </div>
                                                    <div class="paymentMethod form-check form-switch"
                                                        data-method="Piutang">
                                                        <input type="checkbox" class="paymentCheckbox form-check-input"
                                                            type="checkbox" id="Piutang"> Piutang Usaha
                                                    </div>
                                                    <div class="paymentMethod form-check form-switch" data-method="QRIS">
                                                        <input type="checkbox" class="paymentCheckbox form-check-input"
                                                            type="checkbox" id="QRIS"> QRIS Instan (Cek Otomatis)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-12">
                                            <button id="simpanData" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card stretch-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Pengaturan Flag Metode Pembayaran</h5>
                                <a href="{{ route('payment-method-flag.create') }}" class="btn btn-primary"
                                    id="addPaymentMethod">Tambah</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped border w-full" id="landing-pages-table">
                                        <thead>
                                            <tr>
                                                <th style='min-width: 150px; text-align: left;'>Group</th>
                                                <th style='min-width: 150px; text-align: left;'>Payment Method</th>
                                                <th style='min-width: 200px; text-align: left;'>Flag</th>
                                                <th style='min-width: 150px; text-align: center;'>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
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
    </main>
@endsection
@section('js')
    @include('storefront.paymentData')
    <script>
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        let pay = '{{ $payment }}';
        let payment = null;
        if (pay) {
            payment = JSON.parse(atob(pay));
            console.log(payment)
        }

        // Menggabungkan payment dengan paymentData
        if (payment && paymentData) {
            paymentData = paymentData.map(item => {
                const matchedPayment = payment.find(p => p.method === item.method);
                return {
                    ...item,
                    selected: matchedPayment ? matchedPayment.selected : item.selected
                };
            });
        }

        $(document).ready(function() {
            var table = $('#landing-pages-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Blfrtip',
                ajax: '{{ route('payment-method-flag.data') }}',
                columns: [{
                        data: 'group',
                        name: 'group',
                        orderable: false
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        orderable: false
                    },
                    {
                        data: 'flag',
                        name: 'flag',
                        orderable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true
                    },
                ],
                order: [
                    [3, 'asc']
                ],
                // rowCallback: function(row, data, index) {
                //     var pageInfo = table.page.info();
                //     $('td:eq(1)', row).html(pageInfo.start + index +
                //         1); // Set the number in the second column
                // },
                scrollY: '500px', // Sesuaikan tinggi maksimum yang diinginkan
                scrollCollapse: true,
                paging: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                initComplete: function() {
                    // $('#selectRange').val('isThisMonth'); // Set nilai dropdown ke 'isThisMonth'
                    // onFilter(''); // Panggil fungsi filter untuk menerapkan filter awal
                }
            });

            function updatePaymentData() {
                $('#paymentMethods .paymentMethod').each(function() {
                    let method = $(this).data('method');
                    let methodCheckbox = $(this).find('.paymentCheckbox');
                    let methodData = paymentData.find(m => m.method === method);

                    methodData.selected = methodCheckbox.is(':checked');

                    if (method == 'Transfer') {
                        $(this).find('.bank').each(function() {
                            let bank = $(this).data('bank');
                            let bankName = $(this).find('.bankName').val();
                            let bankCheckbox = $(this).find('.bankCheckbox');
                            let bankOwner = $(this).find('.bankOwner').val();
                            let bankAccountNumber = $(this).find('.bankAccountNumber').val();

                            let bankData = methodData.banks.find(b => parseInt(b.id) === parseInt(
                                bank));
                            if (bankData) {
                                bankData.selected = bankCheckbox.is(':checked');
                                bankData.bankOwner = bankOwner;
                                bankData.bankAccountNumber = bankAccountNumber;
                                bankData.bank = bankName;
                            } else {
                                console.error('Bank data not found for bank:', bank);
                            }
                        });
                    }


                });


                $('#jsonOutput').text(JSON.stringify(paymentData, null, 2));
            }

            $('#paymentMethods .paymentCheckbox, #paymentMethods .bankCheckbox').on('change', function() {
                updatePaymentData();
            });

            $('#paymentMethods .bankOwner, #paymentMethods .bankAccountNumber, #paymentMethods #inputBankLain').on(
                'input',
                function() {
                    updatePaymentData();
                });

            function updateJsonDisplay() {
                $('#jsonOutput').text(JSON.stringify(paymentData, null, 2));
            }
            updateJsonDisplay();

            $('#simpanData').on('click', function() {
                try {
                    $.ajax({
                            url: "{{ url('save-payment-method') }}",
                            type: 'POST',
                            data: {
                                '_token': csrf_token,
                                paymentData: paymentData,
                            }
                        })
                        .done(function(res) {
                            swal.fire(res.title, res.text, res.icon).then(() => {
                                location.reload();
                            })

                        })
                        .fail(function() {
                            swal.fire('Oops...', 'Something went wrong with data !', 'error');
                        });
                } catch (err) {
                    // swal.fire('Oops...', 'error', 'error');
                    console.log(err)
                }
            })
            $('#Transfer').on('change', function() {
                if ($('#Transfer').prop('checked')) {
                    $('#banks').removeClass('d-none');

                } else {
                    $('#banks').addClass('d-none');
                }
            });

        })
        $(document).ready(function() {
            paymentData.forEach((item, index) => {
                let checkbox = $(`#${item.method}`);

                if (item.selected == 'true') {
                    checkbox.prop('checked', true);
                }
                if (item.method === 'Transfer') {
                    item.banks.forEach((bank, subindex) => {
                        let bankItem = bank.id;
                        let bankCheckbox = $('#checkBank' + bankItem);
                        let bankOwner = $('#ownerBank' + bankItem);
                        let bankAccount = $('#accountBank' + bankItem);
                        let bankName = $('#inputBank' + bankItem);

                        if (payment[index].banks[subindex].selected == 'true') {
                            bankCheckbox.prop('checked', true);
                            bankOwner.val(payment[index].banks[subindex].bankOwner)
                            bankAccount.val(payment[index].banks[subindex].bankAccountNumber)
                            bankName.val(payment[index].banks[subindex].bank)
                        }
                    })
                }

            });
            if ($('#Transfer').prop('checked')) {
                $('#banks').removeClass('d-none');
            } else {
                $('#banks').addClass('d-none');
            }
        })
        
    </script>
@endsection
