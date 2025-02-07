@extends('app.master')
@section('content')

{{-- Edit Blog Modal --}}
<div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
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

{{-- Edit Profile Modal --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-secondary">
          <h1 class="modal-title fs-5" id="editProfileModalLabel">Edit Profile</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="updateProfile" enctype="multipart/form-data">
            <div class="modal-body bg-dark">
                <div class="form-group">
                    <label class="form-label">Gender: </label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                            {{ (old('gender', Auth::user()->profile->gender ?? 'Male') == 'Male') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="Female"
                            {{ (old('gender', Auth::user()->profile->gender ?? 'Male') == 'Female') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="other" value="Other"
                            {{ (old('gender', Auth::user()->profile->gender ?? 'Male') == 'Other') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="other">Other</label>
                    </div>

                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">Bio: </label>
                    <textarea name="bio" id="bio" cols="30" rows="10" class="form-control">{{Auth::user()->profile->bio ?? ''}}</textarea>
                </div>

                <div class="form-group">
                    <label for="profile_image" class="form-label">Profile Image: </label>
                    <input type="file" class="form-control" name="profile_image" id="profile_image">
                    <!-- Image preview container -->
                    <div id="image-preview-container" class="mt-3">
                        <img id="image-preview" src="#" alt="Profile Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; display: none;">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <label class="form-label" for="removeprofileimage">remove profile picture: </label>
                        <input class="form-check-input" type="checkbox" name="removeProfileImage" id="removeprofileimage">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-dark">
              <button type="submit" class="btn btn-secondary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>

<div class="card bg-dark text-white mb-3">
    <div class="card-body">
        <div class="row">
            <!-- Profile Picture -->
            <div class="col-md-3 text-center">
                @if(Auth::user()->profile && Auth::user()->profile->profile_image)
                    <img src="{{ asset('storage/' . Auth::user()->profile->profile_image) }}"
                         alt="{{ Auth::user()->name }}"
                         title="{{ Auth::user()->name }}"
                         class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img src="{{ asset('defaults/default_profile.jpeg') }}"
                         alt="No profile photo"
                         title="No profile photo"
                         class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                @endif
            </div>

            <!-- Profile Info -->
            <div class="col-md-9">
                <div class="row">
                    <!-- Username -->
                    <div class="col-12">
                        <h4 class="mb-0">{{ Auth::user()->name ?? ''}}</h4>
                    </div>

                    <!-- Gender Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-white">{{ Auth::user()->profile->gender ?? '' }}</p>
                    </div>

                    <!-- Bio Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-white">{{ Auth::user()->profile->bio ?? 'Add a bio...' }}</p>
                    </div>

                    <!-- Follow/Followers Section (Optional) -->
                    <div class="col-12 mt-2">
                        <span class="text-white">1000 followers | 500 following</span>
                    </div>

                    <!-- Edit Profile Button -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h3>Your Blogs</h3>
<div id="blog-container">
    @forelse ($blogs as $blog)
        <div class="card {{$blog->ban ? 'bg-danger' : 'bg-dark' }} mb-3">
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
                                Edit</a>
                            </li>
                        @endif
                    </ul>
                </div>

            </div>
            <div class="card-body text-white text-center">
                {!! $blog->content !!}
            </div>
            <div class="card-footer d-flex justify-content-between">
                <div class="user">

                </div>
                <div class="conversation">
                    <a href="{{route('conversation.converstaions', encrypt($blog->id))}}">see conversations
                        <span class="text-secondart">
                            @if (count($blog->comments)>99)
                                |99+
                            @else
                                |{{count($blog->comments)}}
                            @endif
                        </span>
                    </a>
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
        <p class="text-center text-white">NoData</p>
    @endforelse
</div>


@if (count($blogs)> 10)
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
        $(document).ready(function(){

            $(document).on('submit', '#updateProfile', function (e) {
                e.preventDefault();

                // console.log('inside');

                var formData = new FormData(this);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{route('profile.updateprofile')}}",
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
                                $('#editProfileModal').modal('hide');
                                location.reload();
                            }, 2000);
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

            let initialGender = "{{ old('gender', Auth::user()->profile->gender ?? 'Male') }}";
            let initialBio = "{{ Auth::user()->profile->bio ?? '' }}";
            let initialProfileImage = $("#profile_image")[0]?.value ?? '';


            $('#editProfileModal').on('hidden.bs.modal', function () {
                // If the form fields haven't been changed, reset the form values
                if ($('#updateProfile')[0].checkValidity()) {
                    // Reset gender radio buttons
                    $("input[name='gender']").each(function () {
                        if ($(this).val() === initialGender) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });

                    // Reset bio field
                    $('#bio').val(initialBio);

                    // Reset the profile image input field (it won't reset automatically)
                    $('#profile_image').val(initialProfileImage);

                    // Destroy cropper and hide image preview on modal close
                    if (cropper) {
                        cropper.destroy();  // Destroy the cropper instance
                        cropper = null;  // Reset the cropper variable
                    }
                    imagePreview.style.display = 'none';  // Hide image preview
                    imagePreviewContainer.style.display = 'none';  // Hide image preview container
                }
            });

            let cropper;
            const imageInput = document.getElementById('profile_image');
            const imagePreview = document.getElementById('image-preview');
            const imagePreviewContainer = document.getElementById('image-preview-container');

            // Event listener for image input
            imageInput.addEventListener('change', function (e) {
                const file = e.target.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function (event) {
                        // Show the image preview
                        imagePreview.src = event.target.result;
                        imagePreview.style.display = 'block';
                        imagePreviewContainer.style.display = 'block';

                        // If there’s an existing cropper, destroy it
                        if (cropper) {
                            cropper.destroy();
                        }

                        // Create a new cropper instance for the selected image
                        cropper = new Cropper(imagePreview, {
                            aspectRatio: 1, // Keeps it circular (1:1 ratio)
                            viewMode: 2, // Ensures image doesn't overflow the crop box
                            responsive: true,
                            background: true,
                            autoCropArea: 1,
                            ready() {
                                console.log('Cropper is ready');
                            }
                        });
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Remove profile image checkbox
            $(document).on('change', '#removeprofileimage', function () {
                if ($(this).prop('checked')) {
                    // If checked, disable the profile image input and reset cropper and preview
                    $('#profile_image').prop('disabled', true);
                    $('#profile_image').val('');  // Clear the image input value

                    // Destroy cropper and hide preview
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    imagePreview.style.display = 'none';
                    imagePreviewContainer.style.display = 'none';
                } else {
                    // If unchecked, enable the profile image input
                    $('#profile_image').prop('disabled', false);
                }
            });



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

                tinymce.get('edit_blog_content').setContent(blogContent);

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

            $(document).on('click', '#deleteBlog', function (e) {
                e.preventDefault();

                var blogId = $(this).data('id');

                var formData = new FormData();
                formData.append('id', blogId);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this blog?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('blog.deleteblog') }}",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.status == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted',
                                        text: response.message,
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        location.reload();
                                    });
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
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        location.reload();
                                    });
                                }
                            },

                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong. Please try again.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
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

        });
    </script>
@endsection
