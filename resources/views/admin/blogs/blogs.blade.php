@extends('admin.layout')
@section('content')
    <h1>Blogs</h1>

    <div class="row row-cols-1 row-cols-md-4 g-4">
        @forelse ($blogs as $blog)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $blog->title }}</h5>
                        <p class="card-text">{!! \Str::limit($blog->content, 100) !!}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="blogStatus{{ $blog->id }}" class="badge {{ $blog->ban ? 'bg-danger' : 'bg-success' }}">
                                {{ $blog->ban ? 'Banned' : 'Active' }}
                            </span>
                            <button type="button" class="btn btn-{{ $blog->ban ? 'success' : 'danger' }}" id="banButton" value="{{ $blog->id }}">
                                {{ $blog->ban ? 'Unban' : 'Ban' }}
                            </button>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content center">
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
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center" role="alert">
                    No blogs found.
                </div>
            </div>
        @endforelse
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '#banButton', function (e) {
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
    </script>
@endsection
