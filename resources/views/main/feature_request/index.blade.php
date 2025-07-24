@extends('master', [
    'use_tailwind' => true,
])
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
                            <li class="breadcrumb-item">Feature Request</li>
                            <li class="breadcrumb-item">Saran Fitur Baru</li>
                        </ul>
                    </div>
                </div>

                <div class="main-content">
                    <div class="row">
                        <div class="col-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <h5 class="card-title">Saran Fitur Baru</h5>
                                </div>
                                <div class="card-body">
                                    <form @submit.prevent="handleSubmit" class="row">
    <div class="mb-3 col-12">
        <label for="judulFitur" class="form-label">Judul Fitur</label>
        <input type="text" class="form-control" id="judulFitur"
            v-model="form.judulFitur" placeholder="Masukkan Judul Fitur">
    </div>
    <div class="mb-3 col-12">
        <label for="kategori" class="form-label">Kategori</label>
        <select class="form-select" id="kategori" v-model="form.kategori">
            <option selected>Pilih Kategori</option>
        </select>
    </div>
    <div class="mb-3 col-12">
        <label for="detail" class="form-label">Detail</label>
        <textarea class="form-control" id="detail" v-model="form.detail" rows="3" placeholder="Masukkan Detail"></textarea>
    </div>
    <div class="mb-3 col-12">
        <label class="form-label">Foto / Gambar</label>

        <div class="mt-2 d-flex flex-wrap unready d-none" style="gap: 5px;">
            <div v-for="(image, index) in form.foto" :key="index"
                class="image-preview" @click="openImage(image)"
                style="width: 50px; height: 50px; overflow: hidden; cursor: pointer;">
                <img :src="image.url" alt="Preview"
                    style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>

        <div>
            <input type="file" class="form-control" id="foto"
                @change="handleFileUpload($event)" multiple>
        </div>

<div class="d-flex flex-wrap gap-2 justify-content-start mt-3">
    <!-- Tombol Submit -->
    <button type="submit" class="btn btn-warning d-inline-flex align-items-center gap-1">
        <i class="bx bx-send"></i> 
        <span class="d-none d-sm-inline">Buat Permintaan Fitur</span>
        <span class="d-inline d-sm-none">Kirim Permintaan</span>
    </button>

    <!-- Tombol VIP -->
    <a href="https://ping.co.id/chat/request-fitur-vip/"
       class="btn btn-success d-inline-flex align-items-center gap-1"
       target="_blank" rel="noopener noreferrer">
        <i class="bx bx-star"></i>
        <span class="d-none d-sm-inline">Request Jalur VIP</span>
        <span class="d-inline d-sm-none">VIP</span>
    </a>
</div>


    </div>
</form>


                                    <div class="row">
                                        <div class="col-12 mt-4">
                                            <table class="table table-striped border" id="landing-pages-table">
                                                <thead>
                                                    <tr>
                                                        <th style='min-width: 100px'>Judul</th>
                                                        <th style='min-width: 150px'>Kategori</th>
                                                        <th style='min-width: 200px'>Detail</th>
                                                        <th style='min-width: 250px'>Screenshot</th>
                                                        <th style='min-width: 150px'>Dibuat Pada</th>
                                                        <th style='min-width: 150px'>Status</th>
                                                        <th style='min-width: 100px'>Aksi</th>
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
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        // Tambahkan fungsi untuk menghapus data
        const deleteFeatureRequest = async (id) => {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah anda yakin ingin menghapus permintaan fitur ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#375ed1",
                cancelButtonColor: "#d33",
                cancelButtonText: "Batal",
                confirmButtonText: "Iya, Hapus"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.delete(`/feature-request/${id}`);
                        notyf.open({
                            type: 'success',
                            message: 'Permintaan fitur berhasil dihapus.'
                        });
                        $('#landing-pages-table').DataTable().ajax
                            .reload(); // Reload tabel setelah hapus
                    } catch (error) {
                        console.error(error);
                        notyf.open({
                            type: 'error',
                            message: 'Terjadi kesalahan saat menghapus data.'
                        });
                    }
                }
            });
        };

        createApp({
            setup() {
                const form = reactive({
                    judulFitur: '',
                    kategori: '',
                    detail: '',
                    foto: [] // Array untuk menyimpan gambar
                });

                const handleFileUpload = (event) => {
                    const files = Array.from(event.target.files);
                    form.foto = files.map(file => ({
                        file: file,
                        url: URL.createObjectURL(file) // Membuat URL untuk preview
                    }));
                };

                const openImage = (image) => {
                    window.open(image.url, '_blank'); // Membuka gambar di tab baru
                };

                const handleSubmit = async () => {
                    const formData = new FormData();
                    formData.append('judulFitur', form.judulFitur);
                    formData.append('detail', form.detail);
                    formData.append('kategori', form.kategori);

                    // Menambahkan gambar ke FormData
                    form.foto.forEach(image => {
                        formData.append('foto[]', image.file);
                    });

                    try {
                        const response = await axios.post('/feature-request', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                        console.log(response.data);
                        notyf.open({
                            type: 'success',
                            message: response.data.message
                        });
                        // Reset form atau tindakan lain setelah berhasil
                        form.judulFitur = '';
                        form.kategori = '';
                        form.detail = '';
                        form.foto = []; // Reset array foto
                        document.getElementById('foto').value = '';
                        $('#kategori').val(null).trigger('change');

                        $('#landing-pages-table').DataTable().ajax.reload();
                    } catch (error) {
                        console.error(error);
                        notyf.open({
                            type: 'error',
                            message: error.response.data.message
                        });
                        setTimeout(function() {
                            window.location.href = '/premium'
                        }, 4000);
                    }
                };

                onMounted(() => {
                    // Inisialisasi Select2
                    $('#kategori').select2({
                        theme: 'bootstrap-5',
                        ajax: {
                            url: '/v1/feature-request-category',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term // parameter pencarian
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.data.map(function(item) {
                                        return {
                                            id: item.id, // ID kategori
                                            text: item.name // Nama kategori
                                        };
                                    })
                                };
                            },
                            cache: true
                        },
                        placeholder: 'Pilih Kategori',
                        allowClear: true
                    });

                    // Listener untuk perubahan Select2
                    $('#kategori').on('change', function() {
                        form.kategori = $(this).val(); // Simpan nilai yang dipilih ke form.kategori
                    });

                    $('.unready').removeClass('d-none');

                    var table = $('#landing-pages-table').DataTable({
                        processing: true,
                        serverSide: true,
                        dom: 'Blfrtip',

                        ajax: '{{ route('feature-request.datatable') }}',
                        columns: [{
                                data: 'title',
                                name: 'title',
                                orderable: false
                            },
                            {
                                data: 'category',
                                name: 'category',
                                orderable: false
                            },
                            {
                                data: 'detail',
                                name: 'detail',
                                orderable: false
                            },
                            {
                                data: 'images',
                                name: 'images',
                                orderable: false
                            },
                            {
                                data: 'created_at', // Tambahkan kolom created_at
                                name: 'created_at',
                                orderable: true // Anda bisa mengatur ini menjadi true jika ingin mengurutkan berdasarkan kolom ini
                            },
                            {
                                data: 'status',
                                name: 'status',
                                orderable: false
                            },
                            {
                                data: 'aksi',
                                name: 'aksi',
                                orderable: false
                            },
                        ],
                        // order: [
                        //     [4, 'desc']
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
                        ],
                    });
                });

                return {
                    form,
                    handleFileUpload,
                    openImage,
                    handleSubmit
                };
            }
        }).mount('#app');
    </script>
@endsection
