@extends('admin.layout')
@section('content')
    <h1>Users</h1>

    <table class="table table-striped table-hover">
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
                    <td>{{ $user->name }}</td>
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
    </table>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '#banButton', function (e) {
            e.preventDefault();

            var userId = $(this).val();
            var button = $(this);
            var statusTextElement;

            $.ajax({
                url: "{{ route('admin.banunban') }}",
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
