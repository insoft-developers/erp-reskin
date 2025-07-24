@if ($view == 'product-list')
    <script>
        function detailData(id) {
            $.ajax({
                url: "{{ url('product') }}" + "/" + id,
                type: "GET",
                success: function(data) {
                    $("#modal-product-detail").appendTo("body").modal("show");
                    $("#content-product-detail").html(data);
                }
            })

        }

        $("#btn_download").click(function() {

            var chkArray = [];
            $('#id:checked').each(function() {
                chkArray.push($(this).data("id"));
            });
            var idJString = JSON.stringify(chkArray);

            window.location = "{{ url('product_export') }}" + "/" + idJString;

        });

        function product_single_delete(id) {
            Swal.fire({
                title: "Hapus Data Produk?",
                text: "Apakah anda yakin ingin menghapus Data Produk ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_single_product(id);
                }
            });
        }

        function delete_single_product(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ url('product') }}" + "/" + id,
                type: "POST",
                dataType: "JSON",
                data: {
                    "_method": "DELETE",
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                }
            });
        }

        $("#btn_delete_all").click(function() {
            Swal.fire({
                title: "Hapus Data Produk?",
                text: "Apakah anda yakin ingin menghapus Data Produk ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_multiple_product();
                }
            });
        });

        function delete_multiple_product() {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var idArray = [];
            $('#id:checked').each(function() {
                idArray.push($(this).data("id"));
            });

            $.ajax({
                url: "{{ url('delete_multiple_product') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": idArray,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: data.message,
                            icon: "error"
                        });
                    }

                }
            })

        }


        $("#btn_search").click(function() {
            var category = $("#filter_category").val();
            var persamaan = $("#persamaan").val();
            var stock = $("#stock").val();
            init_product_table(category, persamaan, stock);

        });

        $("#check-all-product").click(function() {
            $('.chechbox-id').not(this).prop('checked', this.checked);

            var chkArray = [];
            $("#id:checked").each(function() {
                chkArray.push($(this).data("id"));
                console.log(chkArray);
            });

            if (chkArray.length > 0) {
                $("#btn_delete_all").removeAttr("disabled");
            } else {
                $("#btn_delete_all").attr("disabled", true);
            }
        });

        $(document).ready(function() {
            $("#table-product-list").on("click", '.chechbox-id', function() {
                var chkArray = [];
                $("#id:checked").each(function() {
                    chkArray.push($(this).data("id"));
                    console.log(chkArray);
                });

                if (chkArray.length > 0) {
                    $("#btn_delete_all").removeAttr("disabled");
                } else {
                    $("#btn_delete_all").attr("disabled", true);
                }

            });
        });


        function change_display(id, nilai) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ url('store_display_change') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "nilai": nilai,
                    "_token": csrf_token
                },
                success: function(data) {
                    reloadTable();
                }
            });
        }


        function change_editable(id, nilai) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ url('store_editable_change') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "nilai": nilai,
                    "_token": csrf_token
                },
                success: function(data) {
                    reloadTable();
                }
            });
        }

        function use_stock(id, nilai) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ url('use_stock') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "nilai": nilai,
                    "_token": csrf_token
                },
                success: function(data) {
                    reloadTable();
                }
            });
        }

        function use_banned(type) {
            var message = "";
            if(type == 1) {
                message = 'Produk dengan tipe Manufaktur - di Buat by Pesanan, TIDAK boleh menggunakan Stock';
            } else {
                 message = 'Produk dengan tipe Manufaktur - di Buat dahulu, WAJIB menggunakan Stock';
            }
            Swal.fire({
                icon: "warning",
                title: "Failed...",
                html: message,
                footer: ''
            });
             reloadTable();
        }



        init_product_table("", "", "");

        function init_product_table(category, persamaan, stock) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var productTable = new DataTable('#table-product-list');
            productTable.destroy();
            productTable = $('#table-product-list').DataTable({
                stateSave: true,
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
                    url: "{{ route('product.table') }}",
                    data: {
                        "category": category,
                        "persamaan": persamaan,
                        "stock": stock,
                        "_token": csrf_token
                    },
                },
                order: [
                    [0, "desc"]
                ],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },

                    {
                        data: 'pilih',
                        name: 'pilih',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'display',
                        name: 'display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'editable',
                        name: 'editable',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'buffered_stock',
                        name: 'buffered_stock',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_image',
                        name: 'product_image',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'komposisi',
                        name: 'komposisi'
                    },
                    {
                        data: 'sku',
                        name: 'sku'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'cost',
                        name: 'cost'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'product_value',
                        name: 'product_value'
                    },
                    {
                        data: 'margin',
                        name: 'margin'
                    },
                    {
                        data: 'persen_margin',
                        name: 'persen_margin'
                    },
                    {
                        data: 'stock_alert',
                        name: 'stock_alert'
                    },


                ]
            });

        }

        function reloadTable() {
            var table = new DataTable('#table-product-list');
            table.ajax.reload(null, false);
            $("#check-all-product").prop('checked', false);
            $("#btn_delete_all").attr("disabled", true);
        }


        function upload_product() {
            $("#modal-upload").appendTo("body").modal("show");
        }

        function download_template_upload() {
            window.open("{{ asset('storage/upload_file/product_upload_template.xlsx') }}");
        }

        $(document).ready(function() {
            $("#form-upload-product").submit(function(e) {
                $("#loading-image").show();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                const data = new FormData();

                data.append('_method', 'POST');
                data.append('file', $('#file')[0].files[0]);
                data.append('_token', csrf_token);
                e.preventDefault();
                $.ajax({
                    url: "{{ url('product_upload') }}",
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
                            reloadTable();
                            $("#modal-upload").modal("hide");
                        } else {
                            $("#loading-image").hide();
                            Swal.fire({
                                icon: "warning",
                                title: "Failed...",
                                html: data.message,
                                footer: ''
                            });
                            reloadTable();
                        }
                    }
                })
            });


        });
    </script>
@endif
