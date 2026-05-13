@php
    $routes = authorizedRoutes();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Portal</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=100, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow" />
    <title>@yield('title', env('APP_NAME'))</title>

    {{-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet"> --}}

    <link href="{{ asset('datatables/datatables.min.css') }}" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/css/app.scss'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>
    <x-loader />

    <div class="layout-shell hidden" id="app-content">
        <aside class="sidebar">
            <div className="px-4 py-4 border-b border-white/10">
                <div x-data='{aside: true}'>
                    @include('components.sidebar')
                </div>
            </div>
        </aside>

        <div class="main-area">
            <main class="page-content">@yield('content')</main>
        </div>
    </div>

    <div id="notifications" class="fixed top-4 right-4 flex flex-col gap-2 z-[1000]"></div>

    <script src="{{ asset('/datatables/datatables.min.js') }}"></script>

    @if (str_ends_with(request()->host(), '.lan'))
        <!-- Privacy-friendly analytics by Plausible -->
        <script async src="https://analytics.maternalchildhosp.com/js/pa-cCAS5cmshMRHQhNZzPKZ5.js"></script>
        <script>
            window.plausible = window.plausible || function() {
                (plausible.q = plausible.q || []).push(arguments)
            }, plausible.init = plausible.init || function(i) {
                plausible.o = i || {}
            };
            plausible.init()
        </script>
    @elseif (str_ends_with(request()->host(), 'maternalchildhosp.com'))
        <!-- Privacy-friendly analytics by Plausible -->
        <script async src="https://analytics.maternalchildhosp.com/js/pa-j72StbS78esaf_3yXCg9m.js"></script>
        <script>
            window.plausible = window.plausible || function() {
                (plausible.q = plausible.q || []).push(arguments)
            }, plausible.init = plausible.init || function(i) {
                plausible.o = i || {}
            };
            plausible.init()
        </script>
    @endif

    @vite(['resources/js/util.js'])
    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector("#page-loader").remove();
            document.querySelector("#app-content").classList.remove("hidden", "place-items-center");
            // document.querySelector("#app-content").classList.add("md:flex");
        });
    </script>

    <script>
        window.updateNote = function(id, note) {
            return axios.put("{{ route('doctor.admissions.update-note', ':id') }}".replace(':id', id), {
                note,
            }).then((res) => {
                notifySuccess("Note saved!");
            }).catch((err) => {
                console.error(err);
                notifyError(err.response.data.message);
            });
        }
    </script>

    <script>
        $(document).ready(() => {
            Echo.channel('department.{{ auth()->user()->department_id }}').listen('.GroupUpdate', (e) => {
                displayNotification(e);
            });

            Echo.private('user.{{ auth()->user()->id }}').listen('.UserEvent', (e) => {
                displayNotification(e);
            });

            // Check for notification permission
            if (Notification.permission !== 'granted') {
                Notification.requestPermission().then(function(result) {
                    if (result === 'granted') {
                        const n = new Notification('Notifications enabled', {
                            body: 'You will receive notifications when they arrive.',
                        });
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
