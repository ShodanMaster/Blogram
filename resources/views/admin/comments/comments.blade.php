@extends('admin.layout')
@section('content')
    <h1>Comments</h1>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="commentsTable">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Comment</th>
                    <th scope="col">Commented By</th>
                    <th scope="col">blog</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script>

        $(document).ready(function () {

            var table = $('#commentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.getcomments') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'comment', name: 'Comment' },
                    { data: 'commented_by', name: 'Commented By' },
                    { data: 'blog', name: 'blog' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    {
                        targets: 2,
                        data: 'commented_by',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // If the content_type is an object, convert it to a string
                                if (typeof data === 'object') {
                                    data = JSON.stringify(data);
                                }

                                // Render it as HTML if needed
                                return $('<div>').html(data).text();  // Ensure HTML tags are interpreted correctly
                            }
                            return data; // For other cases, return raw text
                        }
                    },
                    {
                        targets: 3,
                        data: 'blog',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // If the content_type is an object, convert it to a string
                                if (typeof data === 'object') {
                                    data = JSON.stringify(data);
                                }

                                // Render it as HTML if needed
                                return $('<div>').html(data).text();  // Ensure HTML tags are interpreted correctly
                            }
                            return data; // For other cases, return raw text
                        }
                    },
                ]
            });


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
                                        table.draw();
                                    });
                                } else if (response.status == 404) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Not Found',
                                        text: response.message,
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        table.ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        table.ajax.reload();
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
        });
    </script>
@endsection
