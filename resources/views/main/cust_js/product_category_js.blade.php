@if ($view == 'product-category')
    <script>
        $(document).ready(function() {

            $("#modal-tambah form").submit(function(e) {
                console.log('tes helo')
                e.preventDefault();
                $("#loading-image").show();
                var id = $("#id").val();
                var met = $('input[name=_method]').val();
                if (save_method == 'add') url = "{{ url('product_category') }}";
                else url = "{{ url('product_category') }}" + "/" + id;
                const data = new FormData()
                const name = $('#name').val()
                const ctg = $('#code').val()
                const description = $("#description").val();
                data.append('_method', met);
                data.append('id', id);
                data.append('name', name); // Tambahkan name ke FormData
                data.append('code', ctg); // Tambahkan code ke FormData
                data.append('image', $('#image')[0].files[0]);
                data.append('description', description); // Ambil file image yang benar
                $.ajax({
                    url: url,
                    type: "POST",
                    data,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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


        function add() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Kategori Produk");
            resetData();
        }



        var table = $('#table-product-category').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            columnDefs: [{
                target: 1,
                visible: false,
                searchable: false
            }, ],

            ajax: "{{ route('product.category.table') }}",
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'image',
                    name: 'image'
                },
                {
                    data: 'description',
                    name: 'description'
                },

            ]
        });

        function editData(id) {
            save_method = "edit";
            $("input[name='_method']").val("PATCH");
            $(".modal-title").text("Edit Kategori Produk")

            $.ajax({
                url: "{{ url('product_category') }}" + "/" + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    $("#id").val(data.id);
                    $("#name").val(data.name);
                    $("#code").val(data.code);
                    $("#description").val(data.description);
                    $("#modal-tambah").appendTo("body").modal("show");
                }
            })
        }

        function resetData() {
            $("#id").val("");
            $("#name").val("");
            $("#code").val("");
            $("#image").val(null);
            $("#description").val("");
        }

        function deleteData(id) {
            Swal.fire({
                title: "Delete Data?",
                text: "Are you sure, do you want to delete this Category..?",
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
                url: "{{ url('product_category') }}" + "/" + id,
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "_method": "DELETE",
                    "id": id
                },
                success: function(data) {

                    reloadTable();

                }
            })
        }

        function reloadTable() {
            var table = $("#table-product-category").DataTable();
            table.ajax.reload(null, false);
        }
    </script>
@endif
