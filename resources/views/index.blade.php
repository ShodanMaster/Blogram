@extends('app.master')

@section('content')

<!-- Modal -->
<div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h1 class="modal-title fs-5" id="editBlogModalLabel">Edit Blog</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBlog" enctype="multipart/form-data">
                <div class="modal-body bg-dark">
                    <input type="hidden" class="form-control" name="id" id="edit_blog_id" required>
                    <div class="form-group">
                        <label for="blog_title" class="form-label">Blog Title: </label>
                        <input type="text" class="form-control" name="title" id="edit_blog_title" required>
                    </div>
                    <div class="form-group">
                        <label for="blog_content" class="form-label">Blog Content: </label>
                        <!-- Textarea for TinyMCE editor -->
                        <textarea class="form-control" name="content" id="edit_blog_content" cols="30" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-dark">
                    <button type="submit" class="btn btn-secondary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<h1>Index</h1>
<div id="blog-container">
    @forelse ($blogs as $blog)
        <div class="card bg-dark mb-3">
            <div class="card-header bg-secondary text-white fs-4 d-flex justify-content-between">
                {{ $blog->title }}

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                            <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu">
                        @if(Auth::check() && Auth::id() == $blog->user_id)
                            <li><a class="dropdown-item" href="#" id="deleteBlog" data-id="{{ encrypt($blog->id) }}">Delete Blog</a></li>
                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editBlogModal"
                                data-id="{{encrypt($blog->id)}}"
                                data-title = "{{$blog->title}}"
                                data-content = "{{$blog->content}}"
                                >
                                Edit2</a>
                            </li>
                        @endif
                        <li><a class="dropdown-item" href="#">Menu item 3</a></li>
                    </ul>
                </div>

            </div>
            <div class="card-body text-white text-center">
                {!! $blog->content !!}
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div class="user d-flex align-items-center">
                    @if($blog->user->profile && $blog->user->profile->profile_image)
                    {{-- <p class="text-white">{{$blog->user->profile->profile_image}}</p> --}}
                        <img src="{{ asset('storage/' . $blog->user->profile->profile_image) }}" alt="Profile Image" class="rounded-circle" width="40" height="40">

                    @else
                        <img src="{{ asset('defaults/default_profile.jpeg') }}"
                            alt="No profile photo"
                            title="No profile photo"
                            class="rounded-circle" width="40" height="40">
                    @endif
                    <span class="ms-2 text-white">{{ $blog->user->name }}</span>
                </div>
                <div class="like">
                    <button type="button" class="btn btn-primary" id="likeButton" value="{{ encrypt($blog->id) }}">
                        @if(auth()->check() && auth()->user()->likedBlogs()->where('blog_id', $blog->id)->exists())
                            Unlike
                        @else
                            Like
                        @endif
                    </button>
                    <span id="likeCount{{ $blog->id }}" class="text-white">
                        {{ $blog->likedUsers()->count() }} Likes
                    </span>
                </div>

            </div>
        </div>
    @empty
        <p class="text-center text-white">No Data</p>
    @endforelse
</div>

@if (count($blogs) > 10)
    <div id="loading-spinner" class="d-flex justify-content-center" style="display:none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@endif

@endsection

@section('scripts')
<script>
    tinymce.init({
        selector: '#edit_blog_content',
        menubar: false,  // Optional: disable the menu bar
        plugins: 'link image lists',  // Optional: you can add more plugins if needed
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image', // Optional: customize the toolbar
    });
</script>

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

    $('#editBlogModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var blogId = button.data('id'); // Extract info from data-* attributes
        var blogTitle = button.data('title');
        var blogContent = button.data('content');

        // Populate the modal's fields with the data
        var modal = $(this);
        modal.find('#edit_blog_id').val(blogId);
        modal.find('#edit_blog_title').val(blogTitle);
        modal.find('#edit_blog_content').val(blogContent);

        tinymce.get('edit_blog_content').setContent(blogContent);  // Initialize TinyMCE editor with blog content
    });

    $(document).on('submit', '#editBlog',function (e) {
        e.preventDefault();

        console.log('Inside');

        var formData = new FormData(this);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "{{route('blog.updateblog')}}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);
                if (response.status == 200) {
                    // Flash success message using SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });

                    setTimeout(function() {
                        $('#createBlogModal').modal('hide');
                        location.reload();
                    }, 2000);
                } else if (response.status == 404) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Not Found',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                } else {
                    // Flash error message using SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }

            },
            error: function(xhr, status, error) {
                // Flash error if request fails and show the specific error message

                // If validation errors are returned from Laravel
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMessage = 'Validation errors occurred:';

                    // Loop through the validation errors and show them in a single message
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errorMessage += `\n${messages.join(', ')}`;
                    });

                    // Show the validation errors in SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Errors!',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                } else {
                    // If some other error occurs (e.g., server error)
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });

    });

    $(document).on('click', '#likeButton', function (e) {
        e.preventDefault();  // Prevent the default action

        var blogId = $(this).val();  // Get the blog ID from the button value
        var button = $(this);  // The like/unlike button
        var likeCountElement;  // The like count span for this blog

        $.ajax({
            url: "{{ route('blog.likeblog') }}",  // Your route for liking the blog
            method: 'POST',
            data: {
                blog_id: blogId,
                _token: '{{ csrf_token() }}',  // CSRF token for security
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Toggle the button text based on the response
                    if (response.message === 'added') {
                        button.text('Unlike');  // Change button text to 'Unlike'
                    } else {
                        button.text('Like');  // Change button text back to 'Like'
                    }

                    // Update the like count on the UI
                    likeCountElement = $('#likeCount' + response.blogId);
                    likeCountElement.text(response.likeCount + ' Likes');  // Set the new like count
                } else {
                    alert('Something went wrong. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('There was an error. Please try again later.');
            }
        });
    });

</script>
@endsection
