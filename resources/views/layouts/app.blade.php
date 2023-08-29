<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100, initial-scale=1.0">
    <title>@yield('title')</title>

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" integrity="sha512-Z/def5z5u2aR89OuzYcxmDJ0Bnd5V1cKqBEbvLOiUNWdg9PQeXVvXLI90SE4QOHGlfLqUnDNVAYyZi8UwUTmWQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    @vite(['resources/css/app.css', 'resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
    <aside id="navigation">
        @include('components.sidebar')
    </aside>
    <main>
        <h1>@yield('heading')</h1>
        @yield('content')
    </main>
    <aside id="noticeboard">
        <h3>Notifications</h3>
        <div class="container">
            @yield('noticeboard')
        </div>
    </aside>

    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}
    @livewireScripts
    @stack('scripts')
</body>

</html>
