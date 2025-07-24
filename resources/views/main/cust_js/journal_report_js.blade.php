@if ($view == 'journal-report')
    <script>
        function export_excel() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var tanggal = monthfrom + '_' + yearfrom;
            window.open("{{ !$userKey ? url('/journal_report_export') : url('/api/journal_report_export') }}" + "/" + tanggal + "?user_key=" + "{{$userKey ?? 'null'}}")
        }

        function export_pdf() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var tanggal = monthfrom + '_' + yearfrom;
            // window.location = "{{ url('journal_report_pdf') }}" + "/" + tanggal;

            window.open("{{ !$userKey ? url('/journal_report_pdf') : url('/api/journal_report_pdf') }}" + "/" + tanggal + "?user_key=" + "{{$userKey ?? 'null'}}");
        }


        $("#form-journal-report-submit").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ !$userKey ? url('journal_report_submit') : url('/api/journal_report_submit') . '?user_key=' . $userKey }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(data) {
                    console.log(data);
                    $(".table-responsive").html(data.data);
                }
            })
        })
    </script>
@endif
