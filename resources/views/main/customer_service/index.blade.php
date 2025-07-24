@extends('master', [
    'use_tailwind' => true,
])
{{-- @dd($products) --}}
@section('style')
    <style>
        .selectedKategori {
            background-color: #c3e0ff;
            color: white;
        }

        .counter-btn {
            width: 30px;
            height: 30px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 50%;
            cursor: pointer;
        }

        .modal-backdrop {
            display: none;
        }

        .modal {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css"
        integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
@endsection
@section('content')
    <div id="app">
        <main class="nxl-container">
            <div class="nxl-content">
                <!-- [ page-header ] start -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">Customer Service</li>
                            <li class="breadcrumb-item">Layanan Customer Service</li>
                        </ul>
                    </div>
                    <div class="page-header-right ms-auto">
                        <div class="page-header-right-items">
                            {{--  --}}
                        </div>
                    </div>
                </div>
                <div class="main-content">
                    <div class="row">
                        <div class="col-xxl-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <h5 class="card-title">List Of Customer Service</h5>
                                    <div class="flex flex-row">
                                        <button v-if="data.subscribe"
                                            @click="methods.modalToggler('modal-add-customer-service', 'show')"
                                            type="button" class="btn btn-md btn-light mr-2">
                                            Add Customer Service
                                        </button>
                                        <button @click="methods.modalToggler('modal-status-imessage', 'show')"
                                            type="button" class="btn btn-md btn-light mr-2">
                                            Whatsapp Gateway Status
                                        </button>
                                        <div class="dropdown">
                                            <button id="action-button" disabled class="btn btn-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Bulk Action
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" id="delete-row">
                                                        Delete
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" id="change-active">
                                                        Change To Active
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" id="change-inactive">
                                                        Change To Inactive
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning" role="alert">
                                        <h1 class="alert-heading font-bold text-xl">Selamat Datang!</h1>
                                        <p>Halaman ini merupakan pusat manajemen customer service yang memungkinkan Anda
                                            untuk mengirim pesan WhatsApp kepada pelanggan Anda melalui beberapa kontak
                                            customer service. Selain itu, Anda juga dapat mengubah template pesan yang akan
                                            dikirimkan kepada
                                            pelanggan. Fitur ini secara otomatis akan mengirimkan pesan saat ada pesanan
                                            masuk, termasuk mengirimkan struk kepada pelanggan.
                                        </p>
                                        <hr class="my-3" />
                                        <p>Layanan WhatsApp ini didukung oleh pihak ketiga (iMessage).
                                            Pembayaran dilakukan melalui halaman iMessage, dan Anda akan menerima akun untuk
                                            mengakses dashboard iMessage guna mendapatkan fitur dan akses lebih lanjut.</p>
                                    </div>


                                    <div style="min-width: 300px; overflow-y: auto" class="mt-3">
                                        <table class="table table-striped border" id="landing-pages-table">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="select-all"></th>
                                                    <th style='min-width: 150px'>Customer Service Name</th>
                                                    <th style='min-width: 150px'>Customer Service Phone</th>
                                                    <th style='min-width: 150px'>Is Active</th>
                                                    <th style='max-width: 100px'>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>


        <div class="modal fade" id="modal-add-customer-service" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form @submit.prevent="methods.onAddCustomerService" class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Add Customer Service</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name" class="form-label">Customer Service Name</label>
                                    <input type="text" name="name" id="name" v-model="data.form.name"
                                        class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modal-status-imessage" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Whatsapp Gateway Status</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-3 my-auto">Is Registered</div>
                            <div class="col d-flex align-items-center">
                                <div>
                                    : @{{ data.isRegistered === 0 ? 'No' : data.isRegistered === 1 ? 'Yes' : '-' }}
                                </div>
                                <a v-if="data.isRegistered !== 1"
                                    :href="`https://imessage.id/pricing?email=${data.email}&phone=${data.phone}&name=${data.name}`"
                                    target="_blank" class="btn btn-warning btn-sm ml-2">Register Now</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3 my-auto">Has Subscribe</div>
                            <div class="col d-flex align-items-center">
                                <div>
                                    : @{{ data.subscribe ? 'Yes' : 'No' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3 my-auto">Expired At</div>
                            <div class="col d-flex align-items-center">
                                <div>
                                    : @{{ data.subscribe ? moment(data.subscribe.will_expire).format('DD-MM-YYYY') : '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3 my-auto">Device Limit</div>
                            <div class="col d-flex align-items-center">
                                <div>
                                    : @{{ data.device_limit }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-scan-device" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Scan Device</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body qr-area flex justify-center">
                        <div class="w-[276px] h-[276px] flex items-center justify-center text-md font-bold text-black"
                            id="skeleton-box">
                            Please wait...
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> --}}
    {{-- <script src="https://unpkg.com/vue@3.2.47/dist/vue.global.js"></script> --}}
    <script>
        // const {
        //     createApp,
        //     ref,
        //     reactive,
        //     onMounted
        // } = Vue;

        let table = null
        createApp({
            setup() {
                const data = reactive({
                    isRegistered: -1,
                    user_id: null,
                    device_limit: null,
                    email: '{{ $email }}',
                    phone: '{{ $phone }}',
                    name: '{{ $name }}',
                    subscribe: null,
                    form: {
                        name: ''
                    },
                    attampt: 0,
                    session_attampt: 0,
                    checkSessionRecurr: null,
                    sessionMake: null,
                    device_id: null,
                    has_receive: false,
                })

                const methods = {
                    init: () => {
                        return new Promise((resolve) => {
                            table = $('#landing-pages-table').DataTable({
                                processing: true,
                                serverSide: true,
                                dom: 'Blfrtip',

                                ajax: '{{ route('customer-service.getData') }}',
                                columns: [{
                                        data: 'checkbox',
                                        name: 'checkbox',
                                        orderable: false,
                                        searchable: false,
                                        render: function(data, type, row) {
                                            return '<input type="checkbox" class="row-checkbox" value="' +
                                                row
                                                .DT_RowIndex + '">';
                                        }
                                    },
                                    // {
                                    //     data: null, // This will be replaced by rowCallback
                                    //     orderable: false,
                                    //     searchable: false,
                                    //     name: 'DT_RowIndex'
                                    // },
                                    {
                                        data: 'name',
                                        name: 'name',
                                        orderable: false
                                    },
                                    {
                                        data: 'phone',
                                        name: 'phone',
                                        orderable: false
                                    },
                                    {
                                        data: 'is_active',
                                        name: 'is_active',
                                        orderable: false
                                    },
                                    {
                                        data: 'action',
                                        name: 'action',
                                        orderable: false
                                    },
                                ],
                                // order: [
                                //     [3, 'desc']
                                // ],
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
                                ]
                            });

                            table.on('change', 'input.row-checkbox', function() {
                                var selectedRows = table.$('input.row-checkbox:checked')
                                    .length;
                                $('#action-button').prop('disabled', selectedRows === 0);
                            });

                            function toggleActionButton() {
                                var selectedRows = table.$('input.row-checkbox:checked').length;
                                $('#action-button').prop('disabled', selectedRows === 0);
                            }

                            $('#select-all').on('click', function() {
                                var rows = table.rows({
                                    'search': 'applied'
                                }).nodes();
                                $('input[type="checkbox"]', rows).prop('checked', this
                                    .checked);
                                toggleActionButton();
                            });

                            $('#delete-row').on('click', function() {
                                var selectedRows = [];
                                table.$('input.row-checkbox:checked').each(function() {
                                    selectedRows.push($(this).val());
                                });

                                Swal.fire({
                                    title: "Hapus Customer Service",
                                    text: "Jika anda sudah yakin untuk menghapus customer service ini, klik Iya untuk melanjutkan",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    confirmButtonText: "Iya",
                                    cancelButtonText: "Batal"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        axios({
                                            method: 'DELETE',
                                            url: '/v1/imessage/customer-service',
                                            data: {
                                                cs_ids: selectedRows
                                            }
                                        }).then((res) => {
                                            table.ajax.reload();
                                            notyf.open({
                                                type: 'success',
                                                message: res.data
                                                    .message
                                            });
                                        }).catch((err) => {
                                            notyf.open({
                                                type: 'error',
                                                message: err
                                                    .response.data
                                                    .message
                                            });
                                        })
                                    }
                                });
                            });

                            $('#change-active').on('click', function() {
                                var selectedRows = [];
                                table.$('input.row-checkbox:checked').each(function() {
                                    selectedRows.push($(this).val());
                                });

                                Swal.fire({
                                    title: "Ubah Customer Service Aktif",
                                    text: "Jika anda ingin merubah status customer service menjadi aktif, klik Iya untuk melanjutkan",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    confirmButtonText: "Iya",
                                    cancelButtonText: "Batal"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        axios({
                                            method: 'PUT',
                                            url: '/v1/imessage/customer-service',
                                            data: {
                                                cs_ids: selectedRows,
                                                is_active: 1
                                            }
                                        }).then((res) => {
                                            table.ajax.reload();
                                            notyf.open({
                                                type: 'success',
                                                message: res.data
                                                    .message
                                            });
                                        }).catch((err) => {
                                            notyf.open({
                                                type: 'error',
                                                message: err
                                                    .response.data
                                                    .message
                                            });
                                        })
                                    }
                                });
                            });

                            $('#change-inactive').on('click', function() {
                                var selectedRows = [];
                                table.$('input.row-checkbox:checked').each(function() {
                                    selectedRows.push($(this).val());
                                });

                                Swal.fire({
                                    title: "Ubah Customer Service Tidak Aktif",
                                    text: "Jika anda ingin merubah status customer service menjadi tidak aktif, klik Iya untuk melanjutkan",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    confirmButtonText: "Iya",
                                    cancelButtonText: "Batal"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        axios({
                                            method: 'PUT',
                                            url: '/v1/imessage/customer-service',
                                            data: {
                                                cs_ids: selectedRows,
                                                is_active: 0,
                                            }
                                        }).then((res) => {
                                            table.ajax.reload();
                                            notyf.open({
                                                type: 'success',
                                                message: res.data
                                                    .message
                                            });
                                        }).catch((err) => {
                                            notyf.open({
                                                type: 'error',
                                                message: err
                                                    .response.data
                                                    .message
                                            });
                                        })
                                    }
                                });
                            });

                            $('#landing-pages-table').on('click', '.scan-btn', function() {
                                const id = $(this).data('id');
                                data.device_id = id
                                methods.modalToggler('modal-scan-device', 'show')
                            });

                            $('#landing-pages-table').on('click', '.logout-device-btn', function() {
                                const id = $(this).data('id');
                                Swal.fire({
                                    title: 'Logout Device?',
                                    text: "Jika anda ingin me logout akun whatsapp untuk customer service ini klik Iya",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Iya',
                                    cancelButtonText: 'Batal',
                                }).then((result) => {
                                    if (result.value == true) {
                                        axios({
                                            method: 'POST',
                                            url: '/v1/imessage/logout-session/' +
                                                id
                                        }).then((res) => {
                                            notyf.open({
                                                type: 'success',
                                                message: res.data
                                                    .message
                                            });
                                            table.ajax.reload();
                                        }).catch((err) => {
                                            notyf.open({
                                                type: 'warning',
                                                message: err
                                                    .response.data
                                                    .message
                                            });
                                        })
                                    }
                                });
                            })

                            resolve(true)
                        })
                    },
                    checkWhatsappGateway: () => {
                        axios.get('/v1/imessage/status', {
                            params: {
                                email: data.email,
                                phone: data.phone,
                                name: data.name
                            }
                        }).then((res) => {
                            data.device_limit = res.data.device_limit ?? '-'
                            data.user_id = res.data.user_id
                            data.email = res.data.email
                            data.phone = res.data.phone
                            data.subscribe = res.data.subscribe
                            if (!res.data.status) {
                                data.isRegistered = 0
                            } else {
                                data.isRegistered = 1
                            }
                        }).catch((err) => {
                            console.log(err.respose.data)
                        })
                    },
                    modalToggler: (id, open) => {
                        $('#' + id).modal(open)

                        if (id === 'modal-status-imessage') {
                            methods.checkWhatsappGateway()
                        } else if (id === 'modal-scan-device') {
                            methods.checkSession('init chekc session');

                            data.sessionMake = setInterval(function() {
                                methods.createSession('after 12 seconds');
                            }, 12000);

                            data.checkSessionRecurr = setInterval(function() {
                                methods.checkSession('check session after 5 seconds');
                            }, 5000);

                            const el = document.getElementById('modal-scan-device')
                            el.addEventListener('hidden.bs.modal', event => {
                                console.log('clearInterval checkSessionRecurr')
                                clearInterval(data.checkSessionRecurr)
                                console.log('clearInterval checkSessionRecurr')
                                clearInterval(data.sessionMake)
                            })
                        }
                    },
                    onAddCustomerService: () => {
                        axios({
                            method: 'POST',
                            url: '/v1/imessage/customer-service',
                            data: {
                                user_id: data.user_id,
                                name: data.form.name // customer service
                            }
                        }).then((res) => {
                            data.form.name = ''
                            methods.modalToggler('modal-add-customer-service', 'hide')
                            table.ajax.reload();
                            notyf.open({
                                type: 'success',
                                message: res.data.message
                            });
                        }).catch((err) => {
                            notyf.open({
                                type: 'warning',
                                message: err.response.data.message
                            });
                        })
                    },
                    createSession: (from = 'Uknown') => {
                        console.log(`createSession from ${from}`)
                        data.attampt++;

                        if (data.attampt == 6) {
                            clearInterval(data.sessionMake);
                            // const image = `<img src="${base_url}/uploads/waiting.jpeg" class="w-50">`;
                            // $('.qr-area').html(image);
                            Swal.fire({
                                title: 'Opps!',
                                text: "Time Expired For Logged In Please Reload This Page",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Close',
                                confirmButtonText: 'Refresh This Page'
                            }).then((result) => {
                                if (result.value == true) {
                                    location.reload();
                                }
                            });
                            return false;
                        }
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        //sending ajax request
                        $.ajax({
                            type: 'POST',
                            url: '/v1/imessage/create-session/' + data.device_id,
                            dataType: 'json',
                            success: function(response) {
                                console.log(response)
                                $('#skeleton-box').addClass('d-none')
                                const image = `<img src="${response.qr}" class="w-90">`;
                                $('.qr-area').html(image);
                                // $('.server_disconnect').hide();
                                // $('.progress').show();

                            },
                            error: function(xhr, status, error) {

                                // const image =
                                //     `<img src="${base_url}/uploads/disconnect.webp" class="w-50"><br>`;
                                // $('.qr-area').html(image);
                                // $('.server_disconnect').show();

                                if (xhr.status == 500) {
                                    clearInterval(data.checkSessionRecurr);
                                    clearInterval(data.sessionMake);
                                }
                            }
                        });
                    },
                    checkSession: (from) => {
                        data.session_attampt++;
                        if (data.session_attampt >= 10) {
                            clearInterval(data.checkSessionRecurr);
                            return false;
                        }
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: 'POST',
                            url: '/v1/imessage/check-session/' + data.device_id,
                            dataType: 'json',
                            success: function(response) {
                                if (response.connected === true) {
                                    if (data.has_receive === false) {
                                        data.has_receive = true
                                        clearInterval(data.checkSessionRecurr);
                                        clearInterval(data.sessionMake);

                                        notyf.open({
                                            type: 'success',
                                            message: response.message
                                        });
                                        methods.modalToggler('modal-scan-device', 'hide')
                                        table.ajax.reload();
                                        // $('.loggout_area').show();

                                        // const image =
                                        //     `<img src="${base_url}/uploads/connected.png" class="w-50"><br>`;
                                        // $('.qr-area').html(image);

                                        // device_status == '0' ? congratulations() : '';
                                        setTimeout(() => {
                                            data.has_receive = false
                                        }, 3000);
                                    }
                                } else {
                                    data.session_attampt == 1 ? methods.createSession(
                                        'check session when session_attamp is 1') : '';
                                }
                            },
                            error: function(xhr, status, error) {
                                if (xhr.status == 500) {
                                    clearInterval(data.checkSessionRecurr);
                                    clearInterval(data.sessionMake);
                                    // const image =
                                    //     `<img src="${base_url}/uploads/disconnect.webp" class="w-50"><br>`;
                                    // $('.qr-area').html(image);
                                    // $('.server_disconnect').show();
                                }

                            }
                        });
                    }
                }

                onMounted(() => {
                    methods.init().then(() => {
                        methods.checkWhatsappGateway()
                    })
                })

                return {
                    moment,
                    data,
                    methods
                }
            }
        }).mount('#app');
    </script>
@endsection
