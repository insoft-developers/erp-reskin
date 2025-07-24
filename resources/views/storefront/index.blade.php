@extends('storefront.template.layouts.app')
@section('content')
@if($store->template == 'FNB')
@include('storefront.template.themes.fnb.components.header')
<div class="page-content">
    <div class="container bottom-content">
        @include('storefront.template.themes.fnb.partials.categories')
        @include('storefront.template.themes.fnb.partials.offer')
        @include('storefront.template.themes.fnb.partials.home')
    </div>
</div>
@else
@include('storefront.template.themes.nonfnb.components.header')
<div class="page-content space-top p-b80">
	<div class="container p-0">
        @include('storefront.template.themes.nonfnb.partials.offer')
        @include('storefront.template.themes.nonfnb.partials.categories')
        @include('storefront.template.themes.nonfnb.partials.home')
    </div>
</div>
@endif
<!-- Page Content End-->

@endsection
@section('js')
<!-- In your Blade template (e.g., cart.blade.php) -->
<script>
$(document).ready(function() {
    function updateCartDisplay(totalQuantity) {
        $('#cart-count').text(totalQuantity);
    }

    $.ajax({
        url: '{{ route('cart.index', $username) }}',
        method: 'GET',
        success: function(response) {
            updateCartDisplay(response.totalQuantity);
        }
    });

    $('#search-icon').click(function() {
        var inputVisible = $('#search-input').is(':visible');

        if (inputVisible) {
            // Hide input and change icon to search
            $('#search-input').val("");
            $('#search-input').hide();
            $('#search-results').hide();

            $(this).removeClass('icon-x').addClass('icon-search'); // Change to search icon
            $('#search-results').html('');  // Clear any search results
        } else {
            // Show input and change icon to close
            $('#search-input').show();
            $('#search-input').focus(); // Automatically focus the input
            $(this).removeClass('icon-search').addClass('icon-x'); // Change to close icon
        }
    });

    var searchTimeout;
    var currentIndex = -1;
    $('#search-input').on('keyup', function() {
        clearTimeout(searchTimeout);  // Clear any existing timeout to reset the delay
        var query = $(this).val();
        // Dynamically set the width of the search results to match the input
        $('#search-results').width($('#search-input').outerWidth());
        // Set a delay of 500ms (adjust as needed)
        searchTimeout = setTimeout(function() {
            if (query.length > 0) {
                $.ajax({
                    url: "{{ route('product.search.api', $username) }}",  // Laravel search route
                    type: "GET",
                    data: { search: query },
                    success: function({data}) {
                        var results = '';
                        data.forEach(function(item) {
                            results += `<a class="search-result-item" href="/{{$username}}/p/${item.id}" style="width:100%;">${item.name}</a>`;
                        });
                        if("{{ $store->template }}"  == 'FNB')
                        {
                            $('#fnb-search-results').html(results).show();  // Show search results
                        }
                        else {
                            $('#search-results').html(results).show();  // Show search results
                        }
                    }
                });
            } else {
                $('#search-results').hide();  // Hide results if query is empty
            }
        }, 500);  // Delay of 500ms before making the request
    });
    // Function to highlight the search result
    function highlightResult(index) {
        $('#search-results .search-result-item').removeClass('highlight');  // Remove highlight from all
        $('#search-results .search-result-item').eq(index).addClass('highlight');  // Highlight the current one
    }
    $('#search-input').on('keydown', function(event) {
        var resultsCount = $('#search-results .search-result-item').length;
        if (event.key === 'ArrowDown') {
            event.preventDefault();  // Prevent default scrolling
            currentIndex++;
            if (currentIndex >= resultsCount) {
                currentIndex = 0;  // Loop back to the first result
            }
            highlightResult(currentIndex);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();  // Prevent default scrolling
            currentIndex--;
            if (currentIndex < 0) {
                currentIndex = resultsCount - 1;  // Loop back to the last result
            }
            highlightResult(currentIndex);
        } else if (event.key === 'Enter') {
            event.preventDefault();  // Prevent form submission
            if (currentIndex >= 0 && currentIndex < resultsCount) {
                // Trigger click on the currently highlighted result
                $('#search-results .search-result-item').eq(currentIndex).click();
            }
        } else if (event.keyCode === 13) {
            event.preventDefault();
            window.location.href = `/{{$username}}/p/${event.target.value}`;
        }
    });
    $(document).click(function(e) {
        if (!$(e.target).closest('#search-input, #search-results, #search-icon').length) {
            $('#search-results').hide();
        }
        if (!$(e.target).closest('#search-input, #search-results, #search-icon').length) {
            $('#fnb-search-results').hide();
        }
    });

});
</script>

@endsection


