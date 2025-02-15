@extends('admin.layout')
@section('content')
<div class="card mb-3 shadow-sm" style="border-radius: 15px; border: 1px solid #ddd;">
    <div class="card-body">
        <div class="row">
            <!-- Profile Picture -->
            <div class="col-md-3 text-center">
                @if($user->profile && $user->profile->profile_image)
                    <img src="{{ asset('storage/' . $user->profile->profile_image) }}"
                         alt="{{ $user->name }}"
                         title="{{ $user->name }}"
                         class="rounded-circle border border-3 border-primary" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <img src="{{ asset('defaults/default_profile.jpeg') }}"
                         alt="No profile photo"
                         title="No profile photo"
                         class="rounded-circle border border-3 border-primary" style="width: 120px; height: 120px; object-fit: cover;">
                @endif
            </div>

            <!-- Profile Info -->
            <div class="col-md-9">
                <div class="row">
                    <!-- Username -->
                    <div class="col-12">
                        <h4 class="mb-1 text-primary">{{ $user->name ?? ''}}</h4>
                    </div>

                    <!-- Gender Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-muted">{{ $user->profile->gender ?? '' }}</p>
                    </div>

                    <!-- Bio Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-muted">{{ $user->profile->bio ?? 'No bio...' }}</p>
                    </div>

                    <!-- Follow/Followers Section (Optional) -->
                    <div class="col-12 mt-2">
                        <span id="followCount{{ $user->id }}">
                            {{ $user->followers()->count() }} Followers|
                            {{ $user->following()->count() }} Following
                        </span>

                        <!-- Checkbox to toggle between Following and Followers -->
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="toggleFollowingFollowers">
                            <label class="form-check-label text-white" for="toggleFollowingFollowers">Show Followers</label>
                        </div>

                        <div class="div" id="following">
                            @if($followingUsers->isNotEmpty())
                                <ul class="list-group">
                                    @foreach($followingUsers as $userFollow)
                                        <li class="list-group-item">
                                            {{ $userFollow->followedUser->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-white">This User not following anyone.</p>
                            @endif
                        </div>

                        <div class="div" id="followers" style="display: none;">
                            @if($followedUsers->isNotEmpty())
                                <ul class="list-group">
                                    @foreach($followedUsers as $userFollowed)
                                        <li class="list-group-item">
                                            {{ $userFollowed->followedUser->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-white">This User don't have any followers.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <div class="user-card">
            <span id="userStatus{{ $user->id }}" class="badge {{ $user->ban ? 'bg-danger' : 'bg-success' }}">
                {{ $user->ban ? 'Banned' : 'Active' }}
            </span>

            <button type="button" class="btn btn-{{ $user->ban ? 'success' : 'danger' }}" id="userBanButton" value="{{ $user->id }}">
                {{ $user->ban ? 'Unban' : 'Ban' }}
            </button>
        </div>
    </div>
</div>

<h3>{{$user->name}}'s Blogs</h3>
<div id="blog-container">
    @forelse ($user->blogs as $blog)
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white  fs-4 d-flex justify-content-between">
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
                    </ul>
                </div>

            </div>
            <div class="card-body  text-center">
                {!! $blog->content !!}
            </div>
            <div class="card-footer d-flex justify-content-between">
                <div class="user">

                </div>
                <div class="conversation">
                    <a href="{{route('admin.converstaions', encrypt($blog->id))}}">see conversations
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
                    <span id="likeCount{{ $blog->id }}">
                        {{ $blog->likedUsers()->count() }} Likes
                    </span>
                </div>
                <div class="blog-card">
                    <span id="blogStatus{{ $blog->id }}" class="badge {{ $blog->ban ? 'bg-danger' : 'bg-success' }}">
                        {{ $blog->ban ? 'Banned' : 'Active' }}
                    </span>

                    <button type="button" class="btn btn-{{ $blog->ban ? 'success' : 'danger' }}" id="banBlogButton" value="{{ $blog->id }}">
                        {{ $blog->ban ? 'Unban' : 'Ban' }}
                    </button>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center ">NoData</p>
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
        document.getElementById('toggleFollowingFollowers').addEventListener('change', function() {
            var followingSection = document.getElementById('following');
            var followersSection = document.getElementById('followers');

            if (this.checked) {
                // Show followers, hide following
                followersSection.style.display = 'block';
                followingSection.style.display = 'none';
            } else {
                // Show following, hide followers
                followingSection.style.display = 'block';
                followersSection.style.display = 'none';
            }
        });
        $(document).on('click', '#banBlogButton', function (e) {
            e.preventDefault();

            var blogId = $(this).val();
            var button = $(this);
            var statusTextElement;

            $.ajax({
                url: "{{ route('admin.banunbanblog') }}",
                method: 'POST',
                data: {
                    blog_id: blogId,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.message === 'banned') {
                            button.text('Unban');
                        } else {
                            button.text('Ban');
                        }

                        statusTextElement = $('#blogStatus' + response.blogId);
                        statusTextElement.text(response.blogStatus);
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

        $(document).on('click', '#userBanButton', function (e) {
            e.preventDefault();

            var userId = $(this).val();
            var button = $(this);
            var statusTextElement;

            $.ajax({
                url: "{{ route('admin.banunbanuser') }}",
                method: 'POST',
                data: {
                    user_id: userId,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.message === 'banned') {
                            button.text('Unban');
                        } else {
                            button.text('Ban');
                        }

                        statusTextElement = $('#userStatus' + response.userId);
                        statusTextElement.text(response.userStatus);
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
