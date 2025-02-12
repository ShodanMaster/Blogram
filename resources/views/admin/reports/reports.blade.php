@extends('admin.layout')
@section('content')
    <h1>reports</h1>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="reportsTable">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Reason</th>
                    <th scope="col">Reported By</th>
                    <th scope="col">Content Type</th>
                    <th scope="col">Content</th>
                    <th scope="col">Status</th>
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
    var table = $('#reportsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.getreports') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'reason', name: 'reason' },
            { data: 'reported', name: 'reported' },
            { data: 'content_type', name: 'content_type' },
            { data: 'content', name: 'content'  }, // This column contains HTML
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
            // { data: 'content', name: 'content', orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: 2, // The index of the 'content_type' column
                data: 'reported',
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
                targets: 4, // The index of the 'content' column
                data: 'content',
                render: function(data, type, row) {
                    if (type === 'display') {
                        // If the content is an object, convert it to a string
                        if (typeof data === 'object') {
                            data = JSON.stringify(data);
                        }

                        // Render it as HTML if needed
                        return $('<div>').html(data).text();  // Ensure HTML tags are interpreted correctly
                    }
                    return data; // For other cases, return raw text
                }
            }
        ]
    });
});


$(document).on('click', '#deleteComment', function (e) {
    e.preventDefault();

    var commentId = $(this).data('id');

    var formData = new FormData();
    formData.append('id', commentId);

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
                url: "{{ route('admin.deletecomment') }}", // Update route if needed
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
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
                        });
                    }
                },
                error: function() {
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
