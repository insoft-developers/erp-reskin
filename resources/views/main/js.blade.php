<script>
    var notyf = new Notyf({
        duration: 10000, // Durasi notifikasi 7 detik
        position: {
            x: 'right', // Posisi horizontal: kanan
            y: 'top', // Posisi vertikal: atas
        },
        dismissible: true,
        types: [{
                type: 'warning',
                background: 'orange',
                icon: {
                    className: 'material-icons',
                    tagName: 'i',
                    text: 'warning',
                    color: 'white'
                }
            },
            {
                type: 'error',
                background: 'indianred',
                duration: 2000,
                dismissible: true
            },
            {
                type: 'info',
                background: '#3299c7',
                icon: {
                    className: 'material-icons',
                    tagName: 'i',
                    text: 'info',
                    color: 'white'
                }
            },
            {
                type: 'success',
                // background: '#46cf78',
                duration: 2000,
                dismissible: true
            },
        ]
    });
    $("#month_from").change(function() {
        var nilai = $(this).val();
        $("#month_to").val(nilai);
    });

    $("#year_from").change(function() {
        var nilai = $(this).val();
        $("#year_to").val(nilai);
    })

    function pemisah_ribuan(textname, targetname) {
        var nominal_text = $(textname).val();
        var nominal = nominal_text.replaceAll(".", "");
        $(targetname).val(nominal);
        var angka_real = $(targetname).val();
        var attr_angka = formatAngka(angka_real);
        $(textname).val(attr_angka);
    }

    function show_error(pesan) {
        Swal.fire({
            icon: "error",
            title: "Notice!...",
            html: pesan,
            footer: ''
        });

    }

    function formatAngka(angka, prefix) {
        var number_string = angka.toString().replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
</script>

@if (isset($view))

    @include('main.cust_js.journal_add_js')
    @include('main.cust_js.journal_edit_js')
    @include('main.cust_js.account_setting_detail_js')
    @include('main.cust_js.account_setting_js')
    @include('main.cust_js.balance_sheet_js')
    @include('main.cust_js.general_ledger_js')
    @include('main.cust_js.initial_capital_js')
    @include('main.cust_js.initial_delete_js')
    @include('main.cust_js.journal_js')
    @include('main.cust_js.journal_report_js')
    @include('main.cust_js.opening_balance_js')
    @include('main.cust_js.product_add_js')
    @include('main.cust_js.product_edit_js')
    @include('main.cust_js.product_list_js')
    @include('main.cust_js.profit_loss_js')
    @include('main.cust_js.report_js')
    @include('main.cust_js.setting_js')
    @include('main.cust_js.trial_balance_js')
    @include('main.cust_js.supplier_js')
    @include('main.cust_js.material_js')
    @include('main.cust_js.inter_product_js')
    @include('main.cust_js.product_category_js')
    @include('main.cust_js.product_purchase_js')
    @include('main.cust_js.material_purchase_js')
    @include('main.cust_js.inter_purchase_js')
    @include('main.cust_js.product_manufacture_js')
    @include('main.cust_js.converse_js')
    @include('main.cust_js.opname_js')



    @if ($view == 'branch')
        <script>
            var name = $("#branchName").val();
            var district = $("#branchDistrict").val();
            var cari = $("#cari").val();

            init_branch_table(name, district, cari);

            function init_branch_table(name, district, cari) {
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                var table = new DataTable('#table-cabang');
                table.destroy();


                var table = $('#table-cabang').DataTable({
                    processing: true,
                    serverSide: true,
                    dom: 'Blfrtip',
                    columnDefs: [{
                        target: 0,
                        visible: false,
                        searchable: false
                    }, ],

                    ajax: {
                        type: "POST",
                        url: "{{ route('branch.table.api') }}",
                        data: {
                            'name': name,
                            'address_detail': district,
                            'cari': cari,
                            '_token': csrf_token
                        }
                    },
                    order: [
                        [0, "desc"]
                    ],
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'address',
                            name: 'address'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },
                        {
                            data: 'address_detail',
                            name: 'address_detail'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

            $("#branchDistrict").change(function() {
                refresh_table();
            });

            $("#branchName").change(function() {
                refresh_table();
            });

            $("#cari").keyup(function() {
                var name = $("#branchName").val();
                var district = $("#branchDistrict").val();
                var cari = $(this).val();
                init_branch_table(name, district, cari);
            })


            function refresh_table() {
                var name = $("#branchName").val();
                var district = $("#branchDistrict").val();
                var cari = $("#cari").val();
                init_branch_table(name, district, cari);
            }

            function add_branch() {
                $("#modal-tambah").modal("show");
                reset_form();
            }

            function reset_form() {
                $("#name").val("");
                $("#phone").val("");
                $("#address").val("");
            }

            function show_validation_errors(errors) {
                $(".invalid-feedback").remove();
                $(".is-invalid").removeClass("is-invalid");

                $.each(errors, function(field, messages) {
                    var input = $('[name="' + field + '"]');
                    input.addClass("is-invalid");
                    $.each(messages, function(index, message) {
                        input.after('<div class="invalid-feedback">' + message + '</div>');
                    });
                });
            }
            $(document).ready(function() {
                $("#form-tambah-cabang").submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ route('branch.store') }}",
                        type: "POST",
                        dataType: "JSON",
                        data: $(this).serialize(),
                        success: function(data) {
                            if (data.success) {
                                $("#modal-tambah").modal("hide");
                                refresh_table();
                            } else {
                                show_error(data.message);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            if (err.responseJSON && err.responseJSON.errors) {
                                var errors = err.responseJSON.errors;
                                show_validation_errors(errors);
                            } else {
                                Swal.fire('Peringatan', err.responseJSON.message, 'warning');
                            }
                            var errors = err.responseJSON.errors;
                            show_validation_errors(errors);
                        }
                    })
                });
                var id = $('#userId').val();
                $('#branchName').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Nama Cabang',
                    ajax: {
                        url: '/api/get-branch-lists/' + id,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                keyword: params.term,
                                limit: 25
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data.data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#branchDistrict').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Nama Kecamatan, Kabupaten, Provinsi',
                    ajax: {
                        url: '/api/get-district-lists/' + id,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                keyword: params.term,
                                limit: 25
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data.data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
                // Initialize select2 with AJAX options
                $('#district').select2({
                    dropdownParent: $('#formSelect'),
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Nama Kecamatan, Kabupaten, Provinsi',
                    ajax: {
                        url: '/api/get-district-lists',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                keyword: params.term,
                                limit: 25
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data.data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            });

            function branch_delete(id) {
                Swal.fire({
                    title: "Delete Data?",
                    text: "Are you sure, do you want to delete this item?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirm_delete_data(id);

                    }
                });
            }

            function confirm_delete_data(id) {
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ url('/branch') }}/" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    data: {
                        'id': id,
                        '_token': csrf_token
                    },
                    success: function(data) {

                        if (data.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your file has been deleted.",
                                icon: "success"
                            });
                            refresh_table();
                        } else {
                            Swal.fire({
                                title: "Failed!",
                                text: data.message,
                                icon: "error"
                            });
                        }
                    }
                })
            }
        </script>
    @endif

    @if ($view == 'staff')
        <script>
            var branch_id = $("#branchId").val();
            var position_id = $("#position").val();
            var cari = $("#cari").val();

            $(document).on('input', '#name', function() {
                var name = $(this).val();

                var namenospace = name.replace(/\s+/g, '').toLowerCase();
                $('#username').val(namenospace);
                $('#email').val(namenospace + '@gmail.com');
            });

            init_staff_table(branch_id, position_id, cari);

            function init_staff_table(branch_id, position_id, cari) {
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                var table = new DataTable('#table-staff');
                var user_id = branch_id == null ? session('id') : null;
                table.destroy();

                var table = $('#table-staff').DataTable({
                    processing: true,
                    serverSide: true,
                    dom: 'Blfrtip',
                    columnDefs: [{
                        target: 0,
                        visible: false,
                        searchable: false
                    }, ],

                    ajax: {
                        type: "POST",
                        url: "{{ route('staff.lists.api') }}",
                        data: {
                            'branch_id': branch_id,
                            'position_id': position_id,
                            'user_id': user_id,
                            'cari': cari,
                            '_token': csrf_token,
                            'user_id': $('#userId').val()
                        }
                    },
                    order: [
                        [0, "desc"]
                    ],
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'position',
                            name: 'position'
                        },
                        {
                            data: 'branch_name',
                            name: 'branch_name'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },
                        {
                            data: 'pin',
                            name: 'pin'
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

            $("#branchId").change(function() {
                refresh_table();
            });

            $("#position").change(function() {
                refresh_table();
            });

            $("#cari").keyup(function() {
                var branch_id = $("#branchId").val();
                var position = $("#position").val();
                var cari = $(this).val();
                init_branch_table(branch_id, position, cari);
            })


            function refresh_table() {
                var branch_id = $("#branchId").val();
                var position_id = $("#position").val();
                var cari = $("#cari").val();
                init_staff_table(branch_id, position_id, cari);
            }

            function create_staff() {
                $("#modal-staff").modal("show");
                reset_form();
            }


            function reset_form() {
                $("#name").val("");
                // $("#selectBranchStaff").val(null);
                $("#selectPositionStaff").val(null);
                $('#username').val("");
                $('#password').val("");
                $('#phone').val("");
                $('#start_date').val("");
                $('#is_active').val("");
            }

            function show_validation_errors(errors) {
                $(".invalid-feedback").remove();
                $(".is-invalid").removeClass("is-invalid");

                $.each(errors, function(field, messages) {
                    var input = $('[name="' + field + '"]');
                    input.addClass("is-invalid");
                    $.each(messages, function(index, message) {
                        input.after('<div class="invalid-feedback">' + message + '</div>');
                    });
                });
            }
            $(document).ready(function() {
                $("#form-tambah-staff").submit(function(e) {
                    const checkboxes = document.querySelectorAll('input[name="holiday[]"]:checked');
                    const checkedCount = checkboxes.length;

                    // if (checkedCount == 0) {
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: 'Oops...',
                    //         text: 'Pilih minimal 1 hari kerja!'
                    //     })
                    // }

                    e.preventDefault();
                    $.ajax({
                        url: "{{ route('store.staff.api') }}",
                        type: "POST",
                        dataType: "JSON",
                        data: $(this).serialize(),
                        success: function(data) {
                            if (data.success) {
                                $("#modal-staff").modal("hide");
                                refresh_table();
                            } else {
                                show_error(data.message);
                            }
                        },
                        error: function(err) {
                            var errors = err.responseJSON.errors;
                            show_validation_errors(errors);
                        }
                    })
                });
                var id = $('#userId').val();


                $('#position').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Posisi Staff',
                    ajax: {
                        url: '/api/get-position-staff',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                keyword: params.term,
                                limit: 25
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data.data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.position
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
                // Initialize select2 with AJAX options
                // $('#selectBranchStaff').select2({
                //     dropdownParent: $('#formSelectBranchStaff'),
                //     theme: 'bootstrap-5',
                //     placeholder: 'Pilih Nama Cabang',
                //     ajax: {
                //         url: '/api/get-branch-lists/' + id,
                //         dataType: 'json',
                //         delay: 250,
                //         data: function(params) {
                //             return {
                //                 keyword: params.term,
                //                 limit: 25
                //             };
                //         },
                //         processResults: function(data) {
                //             return {
                //                 results: $.map(data.data, function(item) {
                //                     return {
                //                         id: item.id,
                //                         text: item.name
                //                     };
                //                 })
                //             };
                //         },
                //         cache: true
                //     }
                // });

                $('#selectPositionStaff').select2({
                    dropdownParent: $('#formSelectPositionStaff'),
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Posisi Staff',
                    ajax: {
                        url: '/api/get-position-staff',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                keyword: params.term,
                                limit: 25
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data.data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.position
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            });

            function staff_delete(id) {
                Swal.fire({
                    title: "Delete Data?",
                    text: "Are you sure, do you want to delete this item?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirm_delete_data(id);

                    }
                });
            }

            function confirm_delete_data(id) {
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ url('/api/remove-staff') }}/" + id,
                    type: "DELETE",
                    dataType: "JSON",
                    data: {
                        'id': id,
                        '_token': csrf_token
                    },
                    success: function(data) {

                        if (data.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Staff has been deleted.",
                                icon: "success"
                            });
                            refresh_table();
                        } else {
                            Swal.fire({
                                title: "Failed!",
                                text: data.message,
                                icon: "error"
                            });
                        }
                    }
                })
            }
        </script>
    @endif
@endif
