@extends('master')

@section('content')
    <style>
        .active-border {
            border: 2px solid #1a4280;
        }
    </style>

    {{-- Modal Pilih Meja --}}
    <div class="modal fade" id="modal-meja">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #2f467a;">
                    <h5 class="modal-title" style="color:white;">Data Nomor Meja</h5>
                </div>
                <div class="modal-body">
                    <h4 class="text-center">Pilih Nomor Meja</h4>
                    <div class="row d-flex gap-2" id="view-meja">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Ubah Status Meja --}}
    <div class="modal fade" id="modal-editmeja">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #2f467a;">
                    <h5 class="modal-title" style="color:white;">Data Nomor Meja</h5>
                </div>
                <div class="modal-body">
                    <h4 class="text-center">Ubah Status Nomor Meja</h4>
                    <form action="{{ url('ubah_status_meja') }}" method="post">
                        @csrf
                        <input type="hidden" id="id_qr_edit" name="id_qr">

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nomor Meja</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-meja">
                                </tbody>
                            </table>
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #2f467a;">
                    <h5 class="modal-title" style="color:white;">Data Edit Reservasi</h5>
                </div>
                <div class="modal-body">
                    <form action="{{ url('edit_reservasi') }}" method="post">
                        @csrf
                        <input type="hidden" class="form-control" name="id" required id="id_reservasi">
                        <input type="hidden" class="form-control" name="id_qr" required id="id_qr_reservasi">

                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Nama Pelanggan</label>
                                    <input type="text" class="form-control" name="nama_pelanggan"
                                        id="edit_nama_pelanggan" readonly required>
                                </div>
                                <div class="col-md-6">
                                    <label for="">No Hp</label>
                                    <input type="number" class="form-control no_hp" name="no_hp" id="edit_no_hp" readonly
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Tanggal</label>
                                    <input type="date" class="form-control" name="tgl" id="edit_tgl" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Jam</label>
                                    <input type="time" class="form-control" name="jam" id="edit_jam" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="">Jumlah</label>
                                    <input type="number" class="form-control" name="jumlah" id="edit_jumlah" required
                                        min="0">
                                </div>
                                <div class="col-md-6" id="edit_no_meja">
                                </div>
                            </div>
                        </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"> Reservasi </h5>
                    </div>
                </div>
            </div>

            <div class="main-content">
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Kelola Reservasi</h5>
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mx-auto">
                                        <h4>Tambah Data Reservasi</h4>
                                        <form action="{{ url('reservasi') }}" method="post">
                                            @csrf

                                            <div class="form-group mb-3">
                                                <label for="">Pilih Data Pelanggan</label>
                                                <select class="form-control" id="select-pelanggan">
                                                    <option value="" selected>Pilih</option>
                                                    <option value="baru">Baru</option>
                                                    <option value="ada">Sudah Ada</option>
                                                </select>
                                            </div>

                                            <div class="form-group" id="pelanggan-form">
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="">Tanggal</label>
                                                        <input type="date" class="form-control" name="tgl"
                                                            required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="">Jam</label>
                                                        <input type="time" class="form-control" name="jam"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        &nbsp;
                                                        <button class="btn btn-sm btn-info" id="btn-meja">Pilih Nomor
                                                            Meja</button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="">Nomor Meja</label>
                                                        <input type="text" class="form-control" name="no_meja"
                                                            required id="meja" readonly>
                                                        <input type="hidden" name="id_qr" id="id_qr">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="">Jumlah Orang</label>
                                                        <input type="number" min="0" class="form-control"
                                                            name="jml_orang" required>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="submit" class="btn btn-secondary" id="ubah-meja">Ubah
                                                        Status
                                                        Meja</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Filter</label>
                                        <select id="filter" class="form-control">
                                            <option value="now">Hari ini</option>
                                            <option value="range">Range Tanggal</option>
                                            <option value="year">Tahun</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="range-tgl" class="col-md-9 d-none">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="">Dari Tanggal</label>
                                            <input type="date" name="" id="" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="">Sampai Tanggal</label>
                                            <input type="date" name="" id="" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}


                            <div class="table-responsive">
                                <table class="table table-bordered" id="table-reservasi">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Meja</th>
                                            <th>Tanggal Reservasi</th>
                                            <th>Jam Reservasi</th>
                                            <th>Jumlah Orang</th>
                                            <th>Nama Pelanggan</th>
                                            <th>No HP</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ 'Meja ' . $item->nomor_meja }}</td>
                                                <td>{{ $item->tgl_reservasi }}</td>
                                                <td>{{ $item->jam_reservasi }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{!! $item->status == 1
                                                    ? '<span class="badge bg-soft-primary text-dark">Booked</span>'
                                                    : '<span class="badge bg-soft-warning text-dark">Selesai</span>' !!}
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-primary btn-sm btn-edit"
                                                            id="{{ $item->id }}"><i class="fa fa-edit"></i></button>

                                                        <button class="btn btn-danger btn-sm btn-hapus"
                                                            id="{{ $item->id }}" data-idqr="{{ $item->qrcode_id }}"
                                                            data-no_meja="{{ $item->nomor_meja }}"><i
                                                                class="fa fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
@endsection


@section('js')
    <script>
        var csrf_token = $('meta[name="csrf-token"]').attr('content');

        $('#table-reservasi').DataTable();

        $('#btn-meja').on('click', function(e) {
            e.preventDefault();
            $('#modal-meja').modal('show');
            $.ajax({
                method: "POST",
                url: "/get_meja",
                data: {
                    '_token': csrf_token,
                },
                success: function(res) {
                    let meja = JSON.parse(res.qr_meja);
                    let div = '';
                    let nomeja = $('#meja').val();

                    $('#id_qr').val(res.id);

                    $.each(meja, function(key, item) {
                        let cursor = 'pointer';
                        let cls = 'bg-warning text-light no-meja';
                        let key_val = key;
                        if (item.status == 1) {
                            cursor = 'none';
                            cls = 'bg-light text-dark';
                            key_val = key + '<br> Booked';
                        }
                        div += `
                        <div class="col-md-3 ${cls} d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px; cursor: ${cursor};" id="meja${key}">
                            <span class="text-center">${key_val}</span>
                        </div>
                        `;

                        if (nomeja == key) {
                            $('#meja' + nomeja).addClass('active-border');
                        }

                    })
                    $('#view-meja').html(div);
                }
            })
        });

        $(document).delegate('.no-meja', 'click', function() {
            $('.no-meja').removeClass('active-border'); // Menghapus border dari semua div
            $(this).addClass('active-border');
            let no = $(this).find('span').text();
            $('#meja').val(no);
        });

        $('#ubah-meja').on('click', function(e) {
            e.preventDefault();
            data_edit_meja()
            $('#modal-editmeja').modal('show');
        })

        function data_edit_meja() {
            meja().then((res) => {
                let html = '';
                let meja = JSON.parse(res.qr_meja);

                $.each(meja, function(key, item) {
                    let status = item.status == 0 ?
                        '<span class="badge bg-soft-warning text-dark">Kosong</span>' :
                        '<span class="badge bg-soft-primary text-dark">Booked</span>';


                    html += `
                    <tr>
                        <td>Nomor Meja ${key}</td>
                        <td>${status}</td>
                        <td>
                        <select class="form-control pilih-status" name="status[]">
                            <option value="${key}_0" ${item.status == 0 ? "selected" : '' } data-key="${key}">Kosong</option>
                            <option value="${key}_1" ${item.status == 1 ? "selected" : '' } data-key="${key}">Booked</option>
                        </select>   
                        </td>
                    </tr>
                    `;
                })

                $('#id_qr_edit').val(res.id);
                $('#tbody-meja').html(html);
            })
        }


        $('#select-pelanggan').on('change', function(e) {
            e.preventDefault();
            let val_pelanggan = $(this).val();
            // let lokasi = get_lokasi();
            form_pelanggan(val_pelanggan)
            // console.log(lokasi);
        });

        $(document).delegate('.no_hp', 'keyup', function(e) {
            e.preventDefault();
            let hp = hpInd($(this).val());
            $(this).val(hp);
        })

        function form_pelanggan(param) {
            let form = '';
            let pelanggan = '';
            let lokasi = '';
            let readonly = '';
            if (param == "baru") {
                pelanggan += `<label for="">Nama Pelanggan</label>
                        <input type="text" class="form-control" name="nama_pelanggan"
                            required>`;
                lokasi += get_lokasi();

            } else {
                readonly += 'readonly';
                pelanggan += get_pelanggan();
                lokasi += `<label for="">Lokasi</label>
                        <input type="text" class="form-control" name="lokasi"
                            required readonly>`
            }

            form += `
                <div class="row mb-3">
                    <div class="col-md-6" id="input-pelanggan">
                    ${ pelanggan }
                    </div>
                    <div class="col-md-6">
                        <label for="">No Hp</label>
                        <input type="number" class="form-control no_hp" name="no_hp" min="0"
                            required ${readonly}>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6" id="select-lokasi">
                    ${lokasi}
                    </div>
                    <div class="col-md-6">
                        <label for="">Kelurahan</label>
                        <input type="text" class="form-control" name="kelurahan"
                            required ${readonly}>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="">Alamat Lengkap</label>
                        <textarea class="form-control" name="alamat" ${readonly}></textarea>
                    </div>
                </div>
                `;

            $('#pelanggan-form').html(form);

        }

        function get_lokasi() {
            $.ajax({
                method: "POST",
                url: "/get_lokasi",
                data: {
                    '_token': csrf_token,
                },
                success: function(res) {
                    let opt = `
                    <label>Pilih Lokasi</label>
                    <select class="form-control" id="lokasi" name="lokasi">
                        <option value="">Provinsi, Kabupaten, Kecamatan</option>
                    `;
                    $.each(res, function(key, item) {
                        opt +=
                            `<option>${ item.provinsi + ', ' + item.kabupaten + ', ' + item.distrik}</option>`;
                    })
                    opt += '</select>';
                    $('#select-lokasi').html(opt);
                    $('#lokasi').select2();

                }
            })
        }

        function get_pelanggan() {
            $.ajax({
                method: "POST",
                url: "/get_pelanggan",
                data: {
                    '_token': csrf_token,
                },
                success: function(res) {

                    let opt = `<label for="">Nama Pelanggan</label>
                    <select class="form-control" name="nama_pelanggan" id="nm_pelanggan">
                        <option value="">Pilih</option>
                    `;
                    $.each(res, function(key, item) {
                        opt +=
                            `<option value="${item.id}">${ item.name + ' ('+ item.kecamatan +')'}</option>`;
                    })
                    opt += '</select>';
                    if (res.length == 0) {
                        $('#input-pelanggan').html('<p class="text-danger">Data Tidak Ada</p>');
                    } else {
                        $('#input-pelanggan').html(opt);
                    }
                    $('#nm_pelanggan').select2();
                }
            })
        }

        $(document).delegate('#nm_pelanggan', 'change', function(e) {
            e.preventDefault();
            let id = $(this).val();
            $.ajax({
                method: "POST",
                url: "/get_pelanggan",
                data: {
                    '_token': csrf_token,
                    id
                },
                success: function(res) {
                    $('input[name="no_hp"]').val(res.phone);
                    $('input[name="lokasi"]').val(res.kecamatan);
                    $('input[name="kelurahan"]').val(res.kecamatan);
                    $('textarea[name="alamat"]').val(res.alamat);
                }
            })
        })

        function hpInd(nohp) {
            nohp = nohp.replace(/\s+/g, '');
            nohp = nohp.replace(/[\(\)]/g, '');
            nohp = nohp.replace(/\./g, '');

            if (/^[\+0-9]+$/.test(nohp.trim())) {
                if (nohp.startsWith('62')) {
                    return nohp.trim();
                } else if (nohp.startsWith('0') || nohp.startsWith('+') || /^[1-9]/.test(nohp)) {
                    return '62' + nohp.substring(1).trim();
                }
            }
            return nohp;
        }

        $('.btn-edit').on('click', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            $.ajax({
                method: "POST",
                url: "/get_reservasi",
                data: {
                    '_token': csrf_token,
                    id
                },
                success: function(res) {
                    console.log(res);
                    $('#edit_nama_pelanggan').val(res.name);
                    $('#edit_no_hp').val(res.phone);
                    $('#edit_tgl').val(res.tgl_reservasi);
                    $('#edit_jam').val(res.jam_reservasi);
                    $('#edit_jumlah').val(res.jumlah);
                    $('#id_reservasi').val(res.id);
                    $('#id_qr_reservasi').val(res.qrcode_id);

                    let select_meja = '';

                    if (res.status == 1) {
                        meja().then((result) => {
                            let meja = JSON.parse(result.qr_meja);
                            select_meja +=
                                '<label>Pilih No meja</label> <select class="form-control" name="no_meja">';
                            $.each(meja, function(key, item) {
                                let selected = key == res.nomor_meja ? 'selected' : '';
                                let cek_status = '';
                                let text_class = '';
                                let disable = '';
                                if (item.status == 0) {
                                    cek_status = 'Kosong';
                                } else {
                                    cek_status = 'Booked';
                                    text_class = 'text-warning';
                                    disable = 'disabled';
                                }
                                select_meja +=
                                    `<option value="${key}" ${selected} class="${text_class}" ${disable}>Nomor Meja ${key} - ${cek_status}</option>`;
                            });

                            select_meja += '</select>';
                            $('#edit_no_meja').html(select_meja);

                        });
                    } else {
                        select_meja += `<label>Nomer meja</label>
                        <input class="form-control" name="no_meja" readonly value="${res.nomor_meja}">
                        `;
                        $('#edit_no_meja').html(select_meja);
                    }

                    $('#modal-edit').modal('show');
                }
            })
        })


        function meja() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    method: "POST",
                    url: "/get_meja",
                    data: {
                        '_token': csrf_token,
                    },
                    success: function(res) {
                        resolve(res);
                    },
                    error: function(err) {
                        reject(err);
                    }
                })
            })
        }

        $('.btn-hapus').on('click', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            let idqr = $(this).data('idqr');
            let no_meja = $(this).data('no_meja');
            Swal.fire({
                title: 'Peringatan',
                text: "Apakah anda yakin ingin menghapus data",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                            url: "/hapus_reservasi",
                            type: 'POST',
                            data: {
                                '_token': csrf_token,
                                id,
                                idqr,
                                no_meja
                            }
                        })
                        .done(function(res) {

                            swal.fire(res.title, res.text, res.icon).then(() => {
                                location.reload()
                            });

                        })
                        .fail(function() {
                            swal.fire('Oops...', 'Something went wrong with data !', 'error');
                        });
                }

            })
        });
    </script>
@endsection
