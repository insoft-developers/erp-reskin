@if ($view == 'main-material')
    <script>
        $(document).ready(function() {
            $("#unit").select2();
            $("#supplier_id").select2();
            $("#category_id").select2();

            $("#form-add-material").submit(function(e) {
                e.preventDefault();
                $("#loading-image").show();
                var id = $("#id").val();
                if (save_method == 'add') url = "{{ url('main_material') }}";
                else url = "{{ url('main_material') }}" + "/" + id;
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "JSON",
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            $("#modal-tambah").modal("hide");
                            table.ajax.reload(null, false);
                            $("#loading-image").hide();
                            update_category_data();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Notice!...",
                                html: data.message,
                                footer: ''
                            });
                            $("#loading-image").hide();
                        }
                    }
                })

            });


            $("#form-upload-material").submit(function(e) {
                $("#loading-image").show();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                const data = new FormData();

                data.append('_method', 'POST');
                data.append('file', $('#file')[0].files[0]);
                data.append('_token', csrf_token);
                e.preventDefault();
                $.ajax({
                    url: "{{ url('material_upload') }}",
                    type: "POST",
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function(data) {
                        if (data.success) {
                            $("#loading-image").hide();
                            Swal.fire({
                                icon: "success",
                                title: "Success...",
                                html: data.message,
                                footer: ''
                            });
                            table.ajax.reload(null, false);
                            $("#modal-upload").modal("hide");
                        } else {
                            $("#loading-image").hide();
                            Swal.fire({
                                icon: "warning",
                                title: "Failed...",
                                html: data.message,
                                footer: ''
                            });
                            table.ajax.reload(null, false);
                        }
                    }
                })
            })

        });


        function add_material() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Data Bahan Baku");
            resetData();
        }


        function generateMaterialSKU() {
            const materialNameInput = document.getElementById('material_name').value.trim();
            const skuInput = document.getElementById('sku');

            if (materialNameInput) {
                const words = materialNameInput.split(' ');
                let sku = '';

                words.forEach(word => {
                    if (word) {
                        sku += word.charAt(0).toUpperCase(); // Ambil huruf pertama dan jadikan huruf besar
                        sku += word.slice(1).replace(/[^\d]/g,
                            ''); // Tambahkan angka yang ada setelah huruf pertama
                    }
                });

                // Membuat empat angka random
                const randomNumbers = Math.floor(Math.random() * 9000) + 1000; // Pastikan selalu empat angka

                // Membuat dua huruf random
                const randomLetters = Array(2).fill(null).map(() => String.fromCharCode(Math.floor(Math.random() * 26) +
                    65)).join('');

                // Gabungkan SKU dengan tanda hubung, angka random, dan huruf random
                sku += '-' + randomNumbers + randomLetters;

                skuInput.value = sku;
            } else {
                skuInput.value = ''; // Kosongkan jika Nama Bahan Baku dihapus
            }
        }

        var table = $('#table-material').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            columnDefs: [{
                target: 1,
                visible: false,
                searchable: false
            }, ],

            ajax: "{{ route('material.table') }}",
            order: [
                [1, "desc"]
            ],
            columns: [{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'material_name',
                    name: 'material_name'
                },
                {
                    data: 'sku',
                    name: 'sku'
                },
                {
                    data: 'category_id',
                    name: 'category_id'
                },
                {
                    data: 'unit',
                    name: 'unit'
                },
                {
                    data: 'stock',
                    name: 'stock'
                },
                {
                    data: 'cost',
                    name: 'cost'
                },
                {
                    data: 'supplier_id',
                    name: 'supplier_id'
                },


            ]
        });

        function editData(id) {
            save_method = "edit";
            $("input[name='_method']").val("PATCH");
            $(".modal-title").text("Edit Data Bahan Baku")

            $.ajax({
                url: "{{ url('main_material') }}" + "/" + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    $("#id").val(data.id);
                    $("#material_name").val(data.material_name);
                    $("#sku").val(data.sku);
                    $("#category_id").val(data.category_id).trigger('change');
                    $("#unit").val(data.unit).trigger('change');
                    $("#supplier_id").val(data.supplier_id).trigger('change');
                    $("#min_stock").val(data.min_stock);
                    $("#ideal_stock").val(data.ideal_stock);
                    $("#description").val(data.description);

                    $("#modal-tambah").appendTo("body").modal("show");
                }
            })
        }

        function resetData() {
            $("#id").val("");
            $("#material_name").val("");
            $("#sku").val("");
            $("#category_id").val("").trigger('change');
            $("#unit").val("").trigger('change');
            $("#supplier_id").val("").trigger('change');
            $("#min_stock").val("");
            $("#ideal_stock").val("");
            $("#description").val("");
        }

        function deleteData(id) {
            Swal.fire({
                title: "Delete Data?",
                text: "Are you sure, do you want to delete this Material..?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    confirm_delete(id);

                }
            });
        }

        function confirm_delete(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ url('main_material') }}" + "/" + id,
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "_method": "DELETE",
                    "id": id
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Notice!...",
                            html: data.message,
                            footer: ''
                        });
                    }
                }
            })
        }

        function reloadTable() {
            var table = $("#table-material").DataTable();
            table.ajax.reload(null, false);
        }

        function update_category_data() {
            $.ajax({
                url: "{{ url('material_category_update') }}",
                type: "GET",
                success: function(data) {
                    $("#category_id").html(data);
                }
            })
        }

        function upload_bahan_baku() {
            $("#modal-upload").appendTo("body").modal("show");
        }

        function download_template_upload() {
            window.open("{{ asset('storage/upload_file/material_upload_template.xlsx') }}");
        }
    </script>
@endif
