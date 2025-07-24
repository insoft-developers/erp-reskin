@if ($view == 'general-ledger')
    <script>
        $("#estimation").select2();

        function export_excel() {
            var estimation = $("#estimation").val();
            if (estimation == '') {
                alert('Akaun rekening belum dipilih...!');
            } else {
                var monthfrom = $("#month_from").val();
                var yearfrom = $("#year_from").val();
                var estimation = $("#estimation").val();
                var tanggal = monthfrom + '_' + yearfrom + '_' + estimation;

                var url = "{{ !$userKey ? url('/general_ledger_export') : url('/api/general_ledger_export') }}" +
                    "/" + tanggal + "?user_key=" + "{{ $userKey ?? 'null' }}";
                window.open(url);
            }
        }

        function export_pdf() {
            var estimation = $("#estimation").val();
            if (estimation == '') {
                alert('Akaun rekening belum dipilih...!');
            } else {
                var monthfrom = $("#month_from").val();
                var yearfrom = $("#year_from").val();
                var estimation = $("#estimation").val();
                var tanggal = monthfrom + '_' + yearfrom + '_' + estimation;
                window.open("{{ !$userKey ? url('/general_ledger_pdf') : url('/api/general_ledger_pdf') }}" + "/" +
                    tanggal + "?user_key=" + "{{ $userKey ?? 'null' }}");
            }



        }


        $("#form-general-ledger-submit").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ !$userKey ? url('general_ledger_submit') : url('/api/general_ledger_submit') . '?user_key=' . $userKey }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(data) {
                    console.log(data);

                    if (data.success) {
                        $(".table-responsive").html(data.data);
                    } else {
                        Swal.fire({
                            title: "Error!",
                            html: data.message,
                            icon: "error"
                        });
                    }
                }
            })
        });
    </script>
@endif
