@extends('app.master')
@section('content')
    <div class="card bg-dark">
        <div class="card-header bg-secondary text-white text-center fs-4">
            Change Password
        </div>
        <form action="{{route('passwordchange')}}" method="post">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="currentpassword" class="form-label text-white">Current PassWord</label>
                    <input type="password" class="form-control" name="currentpassword" id="currentpassword">
                    @error('currentpassword')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label text-white">New Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                            @error('password')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label text-white">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                            @error('password_confirmation')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn-secondary">change</button>
            </div>
        </form>
    </div>
@endsection
