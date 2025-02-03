<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Blog</title>
    <link rel="stylesheet" href="{{asset('bootstrap/bootstrap.min.css')}}">
</head>
<body class="bg-dark text-white">
        @yield('main')
    <script src="{{asset('js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('bootstrap/bootstrap.bundle.min.js')}}"></script>
    @yield('scripts')
</body>
</html>
