@php
    $routes = departmentRoutes()[auth()->user()->department_id];
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=100, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" integrity="sha512-Z/def5z5u2aR89OuzYcxmDJ0Bnd5V1cKqBEbvLOiUNWdg9PQeXVvXLI90SE4QOHGlfLqUnDNVAYyZi8UwUTmWQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    {{-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="//cdn.datatables.net/v/dt/jq-3.7.0/dt-2.1.8/b-3.1.2/fc-5.0.3/r-3.0.3/sp-2.3.3/datatables.min.css"
        rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @vite(['resources/css/app.css', 'resources/css/app.scss', 'resources/js/app.js', 'resources/js/util.js'])
</head>

<body class="md:flex grid-cols-12">
    {{-- Navigations --}}

    {{-- Mobile nav --}}
    <div class="fixed w-full z-[1000] sm:hidden">
        <div class="px-2 py-4 bg-green-400 flex w-full items-center justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-x-3 link">
                    <img src="{{ asset('favicon-3.png') }}">
                    Home
                </a>
            </div>
            <button id="nav-burger" class="border border-black w-[30px] h-[30px] rounded">
                <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="p-2 hidden bg-green-800 text-white" id="mobile-nav-list">
            <ul>
                @foreach ($routes as $d => $name)
                    <li class="p-2">
                        <a href="{{ $d }}" class="nav-link">{{ $name }}</a>
                    </li>
                @endforeach
                <li class="p-2">
                    <a href="{{ route('user-profile') }}" class="nav-link">Profile</a>
                </li>
                <li class="p-2">
                    <a href="{{ route('logout') }}" class="nav-link">Logout</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Large Nav --}}
    <aside id="navigation" class="col-span-3 hidden md:block md:fixed md:w-[300px]">
        @include('components.sidebar')
    </aside>

    <div class="h-20 sm:hidden"></div>
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

    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/font-awesome.js') }}"></script>
    @livewireScripts

    <script src="{{ asset('js/util.js') }}"></script>
    @stack('scripts')
</body>

</html>
