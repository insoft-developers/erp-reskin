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
                                    <h5 class="card-title">Whatsapp Provider Service</h5>
                                    <div class="flex flex-row">
                                        <button type="button" class="btn btn-md btn-primary mr-2" id="manageTemplateBtn">
                                            Manage Message Template
                                        </button>
                                        <button type="button" class="btn btn-md btn-light mr-2" id="addProviderBtn">
                                            Add New Whatsapp Provider
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning" role="alert">
                                        <h1 class="alert-heading font-bold text-xl">Selamat Datang!</h1>
                                        <p>Halaman ini merupakan pusat manajemen whatsapp device untuk mengirim text whatsapp saat ada pesanan baru
                                            masuk, termasuk mengirimkan struk kepada pelanggan.</p>
                                        <hr class="my-3" />
                                        <p></p>
                                            Layanan WhatsApp ini didukung oleh pihak ketiga <a href="https://ping.co.id" target="_blank" class="text-blue-600 underline">PING</a>.
                                            Pembayaran dilakukan melalui halaman PING, dan Anda akan menerima akun untuk
                                            mengakses dashboard PING guna mendapatkan fitur dan akses lebih lanjut.
                                        </p>
                                    </div>


                                    <div style="min-width: 300px; overflow-y: auto" class="mt-3">
                                        <table id="providers-table" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>API Key</th>
                                                    <th>Device ID</th>
                                                    <th>Active</th>
                                                    <th>Action</th>
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

        <div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="providerModalLabel">Provider</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="providerForm">
                            <input type="hidden" name="id" id="providerId">
                            <div class="mb-3">
                                <label for="api_key" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="api_key" name="api_key" required>
                            </div>
                            <div class="mb-3">
                                <label for="device_id" class="form-label">Device ID</label>
                                <input type="text" class="form-control" id="device_id" name="device_id" required>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active">
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveProviderBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Template Message -->
        <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="templateModalLabel">Kelola Template Pesan WhatsApp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            <strong>Parameter yang tersedia:</strong> {customer_name}, {invoice_number}, {link_struk}
                        </div>
                        
                        <form id="templateForm">
                            <div class="mb-3">
                                <label for="invoice_pending" class="form-label">Template Invoice Pending Payment</label>
                                <textarea class="form-control" id="invoice_pending" name="invoice_pending" rows="5" required>Halo Kak {customer_name}! 🌟, ini adalah pengingat bahwa Anda memiliki tagihan yang belum dibayar. Silakan lakukan pembayaran untuk referensi invoice: {invoice_number}. Terima kasih!

Anda dapat melihat invoice di sini: {link_struk}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="invoice_payment_complete" class="form-label">Template Invoice Payment Complete</label>
                                <textarea class="form-control" id="invoice_payment_complete" name="invoice_payment_complete" rows="5" required>Halo Kak {customer_name}! 🌟, terima kasih telah melakukan pembayaran untuk referensi invoice: {invoice_number}.

Anda dapat melihat detail pembayaran di sini: {link_struk}</textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="saveTemplateBtn">Simpan Template</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        // sudah include axios, notyf, moment, bootstrap, jquery, datatables di layout master
        let table = null
        createApp({
            setup() {
                const data = reactive({
                    //
                })

                const methods = {
                    init: () => {
                        return new Promise((resolve) => {
                            // Initialize DataTable
                        })
                    },
                }

                onMounted(() => {
                    table = $('#providers-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('whatsapp-provider.data') }}",
                            type: 'GET',
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                searchable: false,
                                orderable: false
                            },
                            {
                                data: 'api_key',
                                name: 'api_key'
                            },
                            {
                                data: 'device_id',
                                name: 'device_id'
                            },
                            {
                                data: 'active',
                                name: 'active'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                searchable: false,
                                orderable: false
                            }
                        ],
                        order: [
                            [0, 'asc']
                        ],
                        drawCallback: function() {
                            // Event listener tombol edit
                            $('.editBtn').off('click').on('click', function() {
                                const id = $(this).data('id');
                                fetch(`/whatsapp-provider/${id}/edit`, {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    $('#providerId').val(data.id);
                                    $('#api_key').val(data.credentials.api_key);
                                    $('#device_id').val(data.credentials.device_id);
                                    $('#is_active').prop('checked', !!data.is_active);
                                    $('#providerModal').modal('show');
                                });
                            });

                            // Event listener tombol delete
                            $('.deleteBtn').off('click').on('click', function() {
                                const id = $(this).data('id');
                                if(confirm('Yakin ingin menghapus data ini?')) {
                                    fetch(`/whatsapp-provider/${id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if(data.success) {
                                            table.ajax.reload();
                                        }
                                    });
                                }
                            });
                        }
                    });
                })

                return {
                    moment,
                }
            }
        }).mount('#app');

        document.getElementById('addProviderBtn').addEventListener('click', function() {
            document.getElementById('providerForm').reset();
            document.getElementById('providerId').value = '';
            $('#providerModal').modal('show');
        });

        document.getElementById('saveProviderBtn').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('providerForm'));
            const id = document.getElementById('providerId').value;
            const url = id ? `/whatsapp-provider/${id}` : '/whatsapp-provider';
            const method = 'POST';
            if (id) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#providerModal').modal('hide');
                        table.ajax.reload();
                    }
                });
        });

        // Template Modal Functions
        document.getElementById('manageTemplateBtn').addEventListener('click', function() {
            // Load existing templates
            fetch('/whatsapp-crm-template', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.template) {
                    const templateData = data.template.template_data;
                    if (templateData.invoice_pending) {
                        document.getElementById('invoice_pending').value = templateData.invoice_pending;
                    }
                    if (templateData.invoice_payment_complete) {
                        document.getElementById('invoice_payment_complete').value = templateData.invoice_payment_complete;
                    }
                }
                $('#templateModal').modal('show');
            })
            .catch(error => {
                console.error('Error:', error);
                $('#templateModal').modal('show');
            });
        });

        document.getElementById('saveTemplateBtn').addEventListener('click', function() {
            const formData = new FormData();
            formData.append('invoice_pending', document.getElementById('invoice_pending').value);
            formData.append('invoice_payment_complete', document.getElementById('invoice_payment_complete').value);

            fetch('/whatsapp-crm-template', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#templateModal').modal('hide');
                    // Show success notification
                    if (typeof notyf !== 'undefined') {
                        notyf.success('Template berhasil disimpan!');
                    } else {
                        alert('Template berhasil disimpan!');
                    }
                } else {
                    // Show error notification
                    if (typeof notyf !== 'undefined') {
                        notyf.error(data.message || 'Terjadi kesalahan saat menyimpan template');
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat menyimpan template');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof notyf !== 'undefined') {
                    notyf.error('Terjadi kesalahan saat menyimpan template');
                } else {
                    alert('Terjadi kesalahan saat menyimpan template');
                }
            });
        });
    </script>
@endsection
