@if ($view == 'main-supplier')
    <script>
        $(document).ready(function() {
            $("#province").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });

            $("#modal-tambah form").submit(function(e) {
                e.preventDefault();
                $("#loading-image").show();
                var id = $("#id").val();
                if (save_method == 'add') url = "{{ url('main_supplier') }}";
                else url = "{{ url('main_supplier') }}" + "/" + id;
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


        function add_supplier() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Data Supplier");
            resetData();
        }



        var table = $('#table-supplier').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            columnDefs: [{
                target: 1,
                visible: false,
                searchable: false
            }, ],

            ajax: "{{ route('supplier.table') }}",
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
                    data: 'contact_name',
                    name: 'contact_name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'fax',
                    name: 'fax'
                },
                {
                    data: 'province',
                    name: 'province'
                },

            ]
        });

        function editData(id) {
            save_method = "edit";
            $("input[name='_method']").val("PATCH");
            $(".modal-title").text("Edit Data Supplier")

            $.ajax({
                url: "{{ url('main_supplier') }}" + "/" + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    $("#id").val(data.id);
                    $("#name").val(data.name);
                    $("#contact_name").val(data.contact_name);
                    $("#phone").val(data.phone);
                    $("#email").val(data.email);
                    $("#fax").val(data.fax);
                    $("#website").val(data.website);
                    $("#jalan1").val(data.jalan1);
                    $("#jalan2").val(data.jalan2);
                    $("#postal_code").val(data.postal_code);
                    $("#country").val(data.country);
                    $("#province").val(data.province).trigger('change');
                    $("#modal-tambah").appendTo("body").modal("show");
                }
            })
        }

        function resetData() {
            $("#id").val("");
            $("#name").val("");
            $("#contact_name").val("");
            $("#phone").val("");
            $("#email").val("");
            $("#fax").val("");
            $("#website").val("");
            $("#jalan1").val("");
            $("#jalan2").val("");
            $("#postal_code").val("");
            $("#country").val("");
            $("#province").val("").trigger("change");
        }

        function deleteData(id) {
            Swal.fire({
                title: "Delete Data?",
                text: "Are you sure, do you want to delete this Supplier..?",
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
                url: "{{ url('main_supplier') }}" + "/" + id,
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
            var table = $("#table-supplier").DataTable();
            table.ajax.reload(null, false);
        }
    </script>
@endif
