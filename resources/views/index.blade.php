@extends('app.master')

@section('content')
    <h1>Index</h1>
    <div id="blog-container">
        @forelse ($blogs as $blog)
            <div class="card bg-dark mb-3">
                <div class="card-header bg-secondary text-white fs-4">
                    {{ $blog->title }}
                </div>
                <div class="card-body text-white text-center">
                    {{ $blog->content }}
                </div>
            </div>
        @empty
            <p class="text-center text-white">NoData</p>
        @endforelse
    </div>

    @if (count($blogs)> 0)
        <div id="loading-spinner" class="d-flex justify-content-center" style="display:none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
<script>
    let page = 2; // Start from page 2
    let loading = false; // Flag to prevent multiple simultaneous requests

    // Function to check if the user has scrolled to the bottom of the page
    $(window).on('scroll', function() {
        // Check if the user has scrolled to the bottom of the page
        if (!loading && $(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loading = true;  // Set the loading flag to prevent multiple requests
            $('#loading-spinner').show();  // Show the loading spinner

            // Make the AJAX request to load more blogs
            $.ajax({
                url: "{{ route('loadMoreBlogs') }}",
                method: 'GET',
                data: { page: page },
                success: function(response) {
                    // Append the new blogs to the container
                    $('#blog-container').append(response.blogs);

                    // Update the page number
                    page++;

                    // If there's no next page, stop further AJAX requests
                    if (!response.next_page) {
                        $(window).off('scroll');  // Disable scroll event if no more pages
                    }

                    // Hide the loading spinner and reset the loading flag
                    $('#loading-spinner').hide();
                    loading = false;
                },
                error: function() {
                    alert('There was an error loading more blogs.');
                    $('#loading-spinner').hide();  // Hide the loading spinner
                    loading = false;  // Reset the loading flag
                }
            });
        }
    });
</script>
@endsection
