@extends('app.layout')
@section('main')
    @include('app.nav')
    @include('app.createBlog')
    <div class="container mt-5">
        @yield('content')
    </div>
@endsection
