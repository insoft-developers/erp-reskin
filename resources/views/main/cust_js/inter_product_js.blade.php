@if ($view == 'inter-product')
    <script>
        let com_index = 1;



        function add_composition_item() {

            $.ajax({
                url: "{{ url('get_data_non_product') }}",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    com_index++;

                    var html = '';
                    html += '<div class="row baris mtop10 baris-tambahan" id="baris_' + com_index + '">';
                    html += '<div class="col-md-8">';
                    html += '<select class="form-control cust-control select-item" id="composition_' +
                        com_index +
                        '" name="composition[]">';
                    html += '<option value="">Pilih komposisi bahan</option>';
                    html += '<optgroup label="Bahan Baku">';
                    for (var i = 0; i < data.material.length; i++) {
                        html += '<option value="' + data.material[i].id + '_' + 1 + '">' + data.material[i]
                            .material_name + ' - ' + data.material[i].unit + '</option>';
                    }
                    html += '</optgroup>';
                    html += '<optgroup label="Barang Setengah Jadi">';
                    for (var i = 0; i < data.inter.length; i++) {
                        html += '<option value="' + data.inter[i].id + '_' + 2 + '">' + data.inter[i]
                            .product_name + ' - ' + data.inter[i].unit + '</option>';
                    }
                    html += '</optgroup>';
                    html += '</select>';
                    html += '</div>';
                    html += '<div class="col-md-3">';
                    html +=
                        '<input type="text" class="form-control cust-control" id="quantity_' + com_index +
                        '" name="quantity[]" placeholder="quantitiy">';
                    html += '</div>';
                    html += '<div class="col-md-1">';
                    html +=
                        '<center><a onclick="delete_composition_item(' + com_index +
                        ')" href="javascript:void(0);" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center>';
                    html += '</div>';
                    html += '</div>';

                    $(".baris").last().after(html);
                    $(".select-item").select2({
                        dropdownParent: $("#modal-tambah .modal-content")
                    });
                }
            })


        }

        function delete_composition_item(id) {
            var row_count = $(".baris").length;
            if (row_count > 1) {
                $("#baris_" + id).remove();
            }
        }

        $(document).ready(function() {

            $(".select-item").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });


            $("#unit").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });
            $("#supplier_id").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });
            $("#category_id").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });

            $("#modal-tambah form").submit(function(e) {
                e.preventDefault();
                $("#loading-image").show();
                var id = $("#id").val();
                if (save_method == 'add') url = "{{ url('inter_product') }}";
                else url = "{{ url('inter_product') }}" + "/" + id;
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
        });


        function add_data() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Barang Setengah Jadi");
            resetData();
        }



        var table = $('#table-inter-product').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            columnDefs: [{
                target: 1,
                visible: false,
                searchable: false
            }, ],

            ajax: "{{ route('inter.table') }}",
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
                    data: 'product_name',
                    name: 'product_name'
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
                    data: 'composition',
                    name: 'composition'
                },
                {
                    data: 'stock',
                    name: 'stock'
                },
                {
                    data: 'cost',
                    name: 'cost'
                },


            ]
        });

        function editData(id) {
            save_method = "edit";
            $("input[name='_method']").val("PATCH");
            $(".modal-title").text("Edit Barang Setengah Jadi")

            $.ajax({
                url: "{{ url('inter_product') }}" + "/" + id + "/edit",
                type: "GET",

                success: function(data) {
                    console.log(data);
                    $("#id").val(data.data.id);
                    $("#product_name").val(data.data.product_name);
                    $("#sku").val(data.data.sku);
                    $("#category_id").val(data.data.category_id).trigger('change');
                    $("#unit").val(data.data.unit).trigger('change');
                    $("#min_stock").val(data.data.min_stock);
                    $("#ideal_stock").val(data.data.ideal_stock);
                    $("#description").val(data.data.description);
                    $(".baris-tambahan").remove();
                    $(".baris").last().after(data.html);
                    $(".select-item").select2({
                        dropdownParent: $("#modal-tambah .modal-    ")
                    });
                    $("#quantity_1").val(data.detail[0].quantity);
                    $("#composition_1").val(data.detail[0].material_id + '_' + data.detail[0].product_type)
                        .trigger('change');
                    com_index = data.count;
                    $("#modal-tambah").appendTo("body").modal("show");

                }
            })
        }

        function resetData() {
            $("#id").val("");
            $("#product_name").val("");
            $("#sku").val("");
            $("#category_id").val("").trigger('change');
            $("#unit").val("").trigger('change');
            $("#min_stock").val("");
            $("#ideal_stock").val("");
            $("#description").val("");

            for (var i = 2; i <= com_index; i++) {
                $("#baris_" + i).remove();
            }

            $("#composition_1").val("").trigger('change');
            $("#quantity_1").val("");

        }

        function deleteData(id) {
            Swal.fire({
                title: "Delete Data?",
                text: "Are you sure, do you want to delete this Product..?",
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
                url: "{{ url('inter_product') }}" + "/" + id,
                type: "POST",
                dataType: "JSON",
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
            var table = $("#table-inter-product").DataTable();
            table.ajax.reload(null, false);
        }

        function update_category_data() {
            $.ajax({
                url: "{{ url('inter_category_update') }}",
                type: "GET",
                success: function(data) {
                    $("#category_id").html(data);
                }
            })
        }
    </script>
@endif
