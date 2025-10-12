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

    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @vite(['resources/css/app.css', 'resources/css/app.scss', 'resources/js/app.js', 'resources/js/util.js'])
</head>

<body class="md:flex">
    {{-- Navigations --}}

    {{-- Mobile nav --}}
    <div class="sticky top-0 w-full z-[1000] sm:hidden">
        <div class="px-2 py-4 bg-green-400 flex w-full items-center justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-x-3 link">
                    <img src="{{ asset('favicon-3.png') }}">
                    Home
                </a>
            </div>
            <div class="flex gap-x-3 items-center">
                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt=""
                    class="rounded-full w-10 m-auto">
                <button id="nav-burger" class="border border-black px-2 rounded">
                    <i class="fa fa-bars"></i>
                </button>
            </div>
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
    <aside id="navigation" class="hidden sm:block z-50 w-2/12 fixed top-0 left-0 h-screen bg-gray-800 text-white">
        @include('components.sidebar')
    </aside>

    {{-- <div class="h-20 sm:hidden"></div> --}}
    <main class="w-screen sm:ml-[16.666667%] bg-gray-400 min-h-screen p-2 sm:p-6 overflow-y-auto">
        <div class="p-2 hidden sm:flex items-center justify-end">
            <div class="text-center">
                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt=""
                    class="rounded-full w-12 m-auto">
                <p>{{ auth()->user()->name }}</p>
                <small>{{ auth()->user()->department->name }}</small>
            </div>
        </div>
        <div class="p-4">
            @if (session('error'))
                <p>{{ session('error') }}</p>
            @endif
            @if (isset($errors))
                @foreach ($errors->all() as $error)
                    <p class="notice">{{ $error }}</p>
                @endforeach
            @endif
            @yield('content')
        </div>
    </main>

    {{-- <aside id="noticeboard" class="col-span-3 sm:max-w-[20%] p-1">
        <h3>Notifications</h3>
        <div class="px">
            @livewire('get-notifications', ['user' => auth()->user()])
        </div>
    </aside> --}}

    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/font-awesome.js') }}"></script>
    @livewireScripts

    <script src="{{ asset('js/util.js') }}"></script>
    @stack('scripts')
</body>

</html>
