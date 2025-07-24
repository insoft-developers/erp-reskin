@if ($view == 'report')
    <script>
        function on_sales_report_click() {
            window.location = "{{ route('laporan.penjualan.index') }}";
        }

        function on_sales_advance_report_click() {
            window.location = "{{ route('laporan.penjualan.advance.index') }}";
        }

        function on_profit_loss_click() {
            window.location = "{{ url('profit_loss') }}";
        }

        function on_balance_click() {
            window.location = "{{ url('balance') }}";
        }

        function on_journal_report_click() {
            window.location = "{{ url('journal_report') }}";
        }

        function on_trial_balance_click() {
            window.location = "{{ url('trial_balance') }}";
        }

        function on_general_ledger_click() {
            window.location = "{{ url('general_ledger') }}";
        }

        function download_file_spt() {
            window.location = "{{ asset('template/main/files/file_spt.xls') }}";
        }

        function on_rekapitulasi_harian_click() {
            window.location = "{{ route('rekapitulasi-harian.index') }}";
        }

        function on_tax_report_click() {
            window.location = "{{ route('laporan.pajak.index') }}";
        }

        function on_attendance_click() {
            window.location = "{{ route('laporan.absensi.index') }}";
        }

        function on_stock_click() {
            window.location = "{{ route('laporan.stock.index') }}";
        }

        function on_visit_click() {
            window.location = "{{ route('visit.index') }}";
        }
    </script>
@endif
