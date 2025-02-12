@extends('admin.layout')
@section('content')
    <h1>Users</h1>

    {{-- <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Registered Through</th>
                <th scope="col">Restricted</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><a href="{{route('admin.userprofile', encrypt($user->id))}}">{{ $user->name }}</a></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->google_id)
                            Google
                        @else
                            Registration
                        @endif
                    </td>
                    <td>
                        <div class="user-card">
                            <span id="userStatus{{ $user->id }}" class="badge {{ $user->ban ? 'bg-danger' : 'bg-success' }}">
                                {{ $user->ban ? 'Banned' : 'Active' }}
                            </span>

                            <button type="button" class="btn btn-{{ $user->ban ? 'success' : 'danger' }}" id="banButton" value="{{ $user->id }}">
                                {{ $user->ban ? 'Unban' : 'Ban' }}
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table> --}}

    <table class="table table-striped table-hover" id="usersTable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Registered Through</th>
                <th scope="col">Restricted</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

@section('scripts')
    <script>

        $(document).ready(function () {

            var table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.getusers') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'Name' }, // 'name' column with HTML link
                    { data: 'email' },
                    { data: 'Registration Through', name: 'Registration Through' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],


                columnDefs: [
                    {
                        targets: 1, // The index of the 'content_type' column
                        data: 'name',
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
        });

        $(document).on('click', '#banButton', function (e) {
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
