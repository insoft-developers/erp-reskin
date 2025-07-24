@if ($view == 'opening-balance')
    <script>
        $("#form-opening-balance").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ url('submit_opening_balance') }}",
                dataType: "JSON",
                type: "POST",
                data: $(this).serialize(),
                success: function(data) {
                    console.log(data);
                    Swal.fire({
                        title: "Success!",
                        text: data.message,
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endif
