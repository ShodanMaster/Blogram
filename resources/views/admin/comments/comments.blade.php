@extends('admin.layout')
@section('content')
    <h1>Comments</h1>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">comment</th>
                <th scope="col">Commented By</th>
                <th scope="col">blog</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($comments as $comment)
            @if ($comment->blog)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$comment->comment}}</td>
                    <td><a href="{{route('admin.userprofile', encrypt($comment->user->id))}}">{{ $comment->user->name }}</a></td>
                    <td><a href="{{route('admin.converstaions', encrypt($comment->blog->id))}}">see blog</a></td>
                    <td>
                        <button type="button" class="btn btn-danger" id="deleteComment" data-id="{{ encrypt($comment->id) }}">
                            Delete Comment
                        </button>
                    </td>
                </tr>
            @endif
            @empty
                <tr>
                    <td colspan="5" class="text-center">No comments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '#deleteComment', function (e) {
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
                text: 'Do you want to delete this Comment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.deletecomment') }}",
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
    </script>
@endsection
