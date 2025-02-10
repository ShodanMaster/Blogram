@extends('app.master')
@section('content')

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h1 class="modal-title fs-5" id="reportModalLabel">Report</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reportForm">
                <input type="hidden" name="userId" id="reportUserId">
                <input type="hidden" name="blogId" id="reportBlogId">
                <input type="hidden" name="commentId" id="reportCommentId">

                <div class="modal-body bg-dark">
                    <div class="form-group">
                        <label for="reason" class="form-label">Reason: </label>
                        <input type="text" class="form-control" name="reason" id="reason" required>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
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
                @if($user->profile && $user->profile->profile_image)
                    <img src="{{ asset('storage/' . $user->profile->profile_image) }}"
                         alt="{{ $user->name }}"
                         title="{{ $user->name }}"
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
                        <h4 class="mb-0">{{ $user->name ?? ''}}</h4>
                    </div>

                    <!-- Gender Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-white">{{ $user->profile->gender ?? '' }}</p>
                    </div>

                    <!-- Bio Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-white">{{ $user->profile->bio ?? 'No bio...' }}</p>
                    </div>

                    <!-- Follow/Followers Section (Optional) -->
                    <div class="col-12 mt-2">
                        <span class="text-white">1000 followers | 500 following</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#reportModal" data-name="user" data-userid="{{ encrypt($user->id) }}">Report User</button>

    </div>
</div>

<h3>{{$user->name}}'s Blogs</h3>
<div id="blog-container">
    @forelse ($user->blogs as $blog)
    @if (!$blog->ban)
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
                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportModal" data-name="blog" data-blogid="{{ encrypt($blog->id) }}">Report Blog</a></li>
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
    @endif
    @empty
        <p class="text-center text-white">NoData</p>
    @endforelse
</div>


@if (count($user->blogs)> 10)
    <div id="loading-spinner" class="d-flex justify-content-center" style="display:none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
@endif

@endsection
@section('scripts')
    <script>
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

        $('#reportModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal

            // Retrieve data attributes from the clicked button
            var buttonName = button.data('name');
            var blogId = button.data('blogid');
            var userId = button.data('userid');
            var commentId = button.data('commentid');

            console.log(buttonName);

            // Pass the data to the modal form fields
            var modal = $(this);

            if(buttonName === 'blog'){
                modal.find('#reportModalLabel').html('Report Blog');
            }

            if(buttonName === 'user'){
                modal.find('#reportModalLabel').html('Report User');
            }

            // Reset form fields before populating
            $('#reportUserId').val('');
            $('#reportBlogId').val('');
            $('#reportCommentId').val('');
            $('#reason').val('');

            if (userId) {
                modal.find('#reportUserId').val(userId);
            }

            if (blogId) {
                modal.find('#reportBlogId').val(blogId);
            }

            if (commentId) {
                modal.find('#reportCommentId').val(commentId);
            }
        });

        $(document).on('submit', '#reportForm', function (e) {
            e.preventDefault();  // Prevent default form submission

            // Collect the form data
            var formData = {
                userId: $('#reportUserId').val(),
                blogId: $('#reportBlogId').val(),
                commentId: $('#reportCommentId').val(),
                reason: $('#reason').val(),
                reportableType: $('#reportableType').val(),
                _token: '{{ csrf_token() }}'  // CSRF token for security
            };

            // Log the collected data for debugging (optional)
            console.log(formData);

            // Send the data via AJAX
            $.ajax({
                url: "{{ route('report') }}",  // Make sure the route is correct
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reported',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(function() {
                            location.reload(); // Reload page or update the UI
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK'
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
        });
    </script>
@endsection
