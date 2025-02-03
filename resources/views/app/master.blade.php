@extends('app.layout')
@section('main')
    @include('app.nav')
    <div class="container mt-5">
        @yield('content')
    </div>
@endsection
