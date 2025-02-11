@extends('admin.layout')
@section('content')
    <h1>reports</h1>
    {{-- {{dd($dataTable)}} --}}
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Reason</th>
                <th scope="col">Reported By</th>
                <th scope="col">Content Type</th>
                <th scope="col">Content</th>
                <th scope="col">status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $report->reason }}</td>
                    <td><a href="{{ route('admin.userprofile', encrypt($report->user->id)) }}">{{ $report->user->name }}</a></td>
                    <td>{{ class_basename($report->reportable_type) }}</td>
                    <td>

                        @if ($report->reportable_type === 'App\Models\Blog')
                            <a href="{{ route('admin.converstaions', encrypt($report->reportable_id)) }}" target="_blank">
                                {{ $report->reportable->title }}
                            </a>
                        @elseif ($report->reportable_type === 'App\Models\User')
                            <a href="{{ route('admin.userprofile', encrypt($report->user_id)) }}" target="_blank">
                                {{ $report->reportable->name }}
                            </a>
                        @elseif ($report->reportable_type === 'App\Models\Comment')

                            @if ($report->reportable && !$report->reportable->comment)
                                <span class="text-danger">Comment Was Removed</span>
                            @elseif ($report->reportable)
                                {{ Str::limit($report->reportable->comment, 50) }}
                            @else
                                <span class="text-danger">No content available</span>
                            @endif

                        @endif
                    </td>
                    <td>
                        @if ($report->reportable_type === 'App\Models\Comment' && !$report->reportable)
                            resolved
                        @else
                            {{$report->status}}
                        @endif
                    </td>
                    <td>

                        @if ($report->reportable_type === 'App\Models\Comment')
                            @if ($report->reportable && !$report->reportable->comment)
                                <button class="btn btn-success">Removed Comment</button>
                            @elseif ($report->reportable)
                                <button type="button" class="btn btn-danger" id="deleteComment" data-id="{{ encrypt($report->reportable_id) }}">
                                    Delete comment
                                </button>
                            @else
                                <span class="text-danger">Report Handled</span>
                            @endif
                        @else
                            <a href="{{route('admin.handlereport', encrypt($report->id))}}"><button class="btn btn-warning">Handle Report</button></a>
                        @endif

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No reports found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- {{ $dataTable->table() }} --}}
@endsection

@section('scripts')
{{-- {{ $dataTable->scripts(attributes: ['type' => 'module']) }} --}}
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
