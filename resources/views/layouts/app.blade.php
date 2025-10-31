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

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link
        href="https://cdn.datatables.net/v/dt/dt-2.3.4/b-3.2.5/b-colvis-3.2.5/b-html5-3.2.5/b-print-3.2.5/r-3.0.7/datatables.min.css"
        rel="stylesheet" integrity="sha384-HqTYeA3lyfNdehjeLkXVLdK3rVP01dsvAMQW/oV6M0a6+8Tht7YtWjP/sWP89O0j"
        crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/css/app.scss'])
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
    <aside id="navigation" class="hidden sm:block z-50 w-2/12 fixed left-0 h-screen bg-gray-800">
        <div class="sticky top-0">
            @include('components.sidebar')
        </div>
    </aside>

    <main class="w-screen sm:ml-[16.666667%] bg-blue-100 min-h-screen sm:p-8 overflow-y-auto">
        <div class="p-1">
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

    {{-- Global modal for displaying content --}}
    <div id="global-overlay" class="fixed inset-0 bg-black/40 hidden z-50"></div>
    <div id="global-content-slide"
        class="fixed top-0 right-0 h-dvh overflow-y-auto max-w-[600px] w-1/2 bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50">

        <div class="p-4 border-b flex justify-between items-center sticky bg-white top-0">
            <h2 class="text-lg font-semibold" id="global-modal-title">Modal Title</h2>
            <button id="closeGlobalModal" class="text-gray-600">&times;</button>
        </div>
        <div class="px-4 py-8 h-fit" id="global-modal-content">
            <p>Modal content goes here.</p>
        </div>
    </div>

    <div id="notifications" class="fixed top-4 right-4 flex flex-col gap-2 z-[1000]"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
        integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script
        src="https://cdn.datatables.net/v/dt/dt-2.3.4/b-3.2.5/b-colvis-3.2.5/b-html5-3.2.5/b-print-3.2.5/r-3.0.7/datatables.min.js"
        integrity="sha384-N+pTNAj6u3zQeBQuZo/qd20fG6LAD0KVj49eFU9robOJpS7LYXJn/vy7zoXayWW6" crossorigin="anonymous">
    </script>
    @vite(['resources/js/app.js', 'resources/js/util.js'])
    @livewireScripts

    <script>
        $(document).ready(() => {
            function displayNotification(data) {
                if (Notification.permission === 'granted') {
                    const n = new Notification('New Notification', {
                        body: data.message,
                    });
                }

                const el = document.createElement(`div`);
                el.textContent = data.message;
                el.classList.add(...(data.bg), 'app-notification');

                document.querySelector("#notifications").appendChild(el);

                if (data.close_modal) {
                    removeGlobalModal();
                }

                setTimeout(() => {
                    el.classList.add("fade-out");
                }, 3000);

                setTimeout(() => {
                    el.remove();
                }, 3300);
            }

            Echo.channel('department.{{ auth()->user()->department_id }}').listen('NotificationSent', (e) => {
                displayNotification(e.message);
            });
            Echo.private('user.{{ auth()->user()->id }}').listen('.UserEvent', (e) => {
                displayNotification(e);
            });

            // Check for notification permission
            if (Notification.permission !== 'granted') {
                Notification.requestPermission().then(function(result) {
                    if (result === 'granted') {
                        console.log('Notifications permission granted.');
                        const n = new Notification('Notifications enabled', {
                            body: 'You will receive notifications when they arrive.',
                        });
                    }
                });
            }
        });
    </script>
    <script src="{{ asset('js/util.js') }}"></script>

    @stack('scripts')
</body>

</html>
