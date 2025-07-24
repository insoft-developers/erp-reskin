<div style="display: flex; gap: 10px;">
    <a href="{{ route('payment-method-flag.edit', $data->id) }}" class="btn btn-sm btn-primary">Edit</a>
    <form action="{{ route('payment-method-flag.destroy', $data->id) }}" method="POST" class="delete-form" style="display:inline;">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <button type="button" class="btn btn-sm btn-danger delete-button" data-id="{{ $data->id }}">Delete</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.delete-button').on('click', function() {
            const form = $(this).closest('.delete-form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>