<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" integrity="sha512-Z/def5z5u2aR89OuzYcxmDJ0Bnd5V1cKqBEbvLOiUNWdg9PQeXVvXLI90SE4QOHGlfLqUnDNVAYyZi8UwUTmWQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    @vite(['resources/css/app.css', 'resources/css/app.scss', 'resources/js/app.js', 'resources/js/util.js'])
</head>

<body class="md:flex grid-cols-12">
    <aside id="navigation" class="col-span-3 md:fixed md:w-[300px]">
        @include('components.sidebar')
    </aside>
    <main class="col-span-6 bg-gray-400 md:ml-[300px] md:w-2/3 p-4 min-h-[100dvh]">
        @if (session('error'))
            <p>{{ session('error') }}</p>
        @endif
        @if (isset($errors))
            @foreach ($errors->all() as $error)
                <p class="notice">{{ $error }}</p>
            @endforeach
        @endif
        @yield('content')
    </main>

    <aside id="noticeboard" class="col-span-3 md:max-w-[20%] p-1">
        <h3>Notifications</h3>
        <div class="px">
            @livewire('get-notifications', ['user' => auth()->user()])
        </div>
    </aside>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"
        integrity="sha512-uKQ39gEGiyUJl4AI6L+ekBdGKpGw4xJ55+xyJG7YFlJokPNYegn9KwQ3P8A7aFQAUtUsAQHep+d/lrGqrbPIDQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @livewireScripts

    <script src="{{ asset('js/util.js') }}"></script>
    @stack('scripts')
</body>

</html>
