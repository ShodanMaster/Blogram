@extends('admin.layout')
@section('content')
    <h1>Admin</h1>

    <div class="row">
        <div class="col-md-3 mb-4">
            <a href="{{route('admin.users')}}" class="text-decoration-none">
                <div class="card bg-success text-white text-center shadow-lg">
                    <div class="card-body py-4">
                        <i class="align-middle" data-feather="user" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-white">{{ $users }}</h4>
                        <p class="card-title text-white">Users</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4">
            <a href="{{route('admin.blogs')}}" class="text-decoration-none">
                <div class="card bg-info text-white text-center shadow-lg">
                    <div class="card-body py-4">
                        <i class="align-middle" data-feather="edit" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-white">{{ $blogs }}</h4>
                        <p class="card-title text-white">Blogs</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4">
            <a href="{{route('admin.comments')}}" class="text-decoration-none">
                <div class="card bg-warning text-white text-center shadow-lg">
                    <div class="card-body py-4">
                        <i class="align-middle" data-feather="list" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-white">{{ $comments }}</h4>
                        <p class="card-title text-white">Comments</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 mb-4">
            <a href="{{route('admin.reports')}}" class="text-decoration-none">
                <div class="card bg-danger text-white text-center shadow-lg">
                    <div class="card-body py-4">
                        <i class="align-middle" data-feather="alert-triangle" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-white">{{ $reports }}</h4>
                        <p class="card-title text-white">Reports</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

@endsection
