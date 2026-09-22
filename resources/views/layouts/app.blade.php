@php
    $routes = authorizedRoutes();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
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

    @stack('styles')
</head>

<body class="h-dvh grid place-items-center" x-init="chat_open = localStorage.getItem('chat_open', '0') == 1;" x-data="{
    chat_open: false,
    setChat(value) {
        localStorage.setItem('chat_open', value == true ? 1 : 0);
        this.chat_open = value;
    },
    chat_selected: false,
    selectChat(value) {
        this.chat_selected = value;
        setupChat();
        if (value != false) {
            localStorage.setItem('opened_chat', JSON.stringify(value));
        } else {
            localStorage.setItem('opened_chat', false);
        }
    },
}">
    <x-loader />

    <div id="app-content" class="h-dvh hidden place-items-center" x-data="{ aside: false, current_location: '{{ session('current_location_code') }}' }"
        @closeModal.window="removeGlobalModal" x-init="aside = (localStorage.getItem('aside') || 'true') === 'true'">
        {{-- Navigations --}}
        {{-- Mobile nav --}}
        <div class="sticky top-0 w-full z-[50] sm:hidden">
            <div class="px-2 py-4 bg-green-400 flex w-full items-center justify-between">
                <div>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-x-3 hover:text-blue-600">
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
                    <li class="p-2">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    @foreach ($routes as $map)
                        <li class="p-2" x-data="{ open: false }" x-on:click="open = !open">
                            <div class="flex-center gap-x-2">
                                <i class="fa {{ $map['label'][1] }}"></i>
                                <span>{{ $map['label'][0] }}</span>
                            </div>
                            <ul x-show="open" x-transition class="ps-2">
                                @foreach ($map['routes'] as $n => $l)
                                    <li class="p-2">
                                        <a href="{{ @$l[0] }}">{{ @$n }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                    <li class="p-2">
                        <a href="{{ route('user-profile') }}">Settings</a>
                    </li>
                    <li class="p-2">
                        <a href="{{ route('logout') }}">Log out</a>
                    </li>
                </ul>
            </div>
        </div>
        {{-- Large Nav --}}
        <aside
            class="hidden sm:block z-50 fixed left-0 transition-[width] duration-[500ms] h-screen bg-gray-800 overflow-auto"
            x-cloak :class="{ 'w-[16%]': aside, 'w-[5%]': !aside }" x-transition>
            <div class="bg-white p-2 text-center">
                <img src="https://ui-avatars.com/api/?name={{ session(config('app.generic_doctor_id')) ?? auth()->user()->name }}"
                    alt="" class="rounded-full w-12 m-auto">
                <p x-show="aside" class="font-bold">
                    {{ session(config('app.generic_doctor_id')) ?? auth()->user()->name }}</p>
                <p x-show="aside">{{ auth()->user()->department->name }}</p>
            </div>
            @include('components.sidebar')
        </aside>
        <main
            class="w-screen bg-blue-100 min-h-screen overflow-y-auto transition-[margin] duration-[500ms] printable print:ml-0"
            x-cloak x-bind:class="aside ? 'sm:ml-[16%]' : 'sm:ml-[5%]'">
            <div class="flex-center p-2 justify-end gap-x-4 print:hidden">

                {{-- Location Switcher  --}}
                <form method="POST" action="/location/switch">
                    @csrf
                    <select name="location_code" class="pr-8 py-0" x-model="current_location">
                        <option value="IMMIGRATION">Immigration</option>
                        <option value="ADEBAYO">Adebayo</option>
                    </select>
                    <button>Switch</button>
                </form>
                {{-- End Location Switcher --}}

                <a href="{{ route('dropbox') }}">
                    <i class="fab fa-dropbox text-2xl"></i>
                </a>
                <p class="text-black sm:px-8">Version: {{ env('APP_VERSION', '0.0.1') }}</p>
            </div>
            <div class="sm:px-8 sm:py-4">
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
        <div id="global-overlay" x-on:keyup.escape.window="removeGlobalModal"
            class="fixed inset-0 bg-black/40 hidden z-50">
        </div>
        <div id="global-modal"
            class="fixed top-0 right-0 h-dvh overflow-y-auto min-w-fit sm:min-w-[50%] max-w-[100vw] bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50">
            <div class="p-4 border-b shadow-sm flex justify-between items-center sticky bg-white top-0">
                <h2 class="text-lg font-semibold modal-title" id="global-modal-title">Modal Title</h2>
                <button id="closeGlobalModal" class="text-gray-600">&times;</button>
            </div>
            <div class="px-4 pt-2 h-fit modal-body container" id="global-modal-content">
                <p>Modal content goes here.</p>
            </div>
        </div>
    </div>

    <div id="notifications" class="fixed top-4 right-4 flex flex-col gap-2 z-[1000]"></div>

    <button x-cloak x-show="!chat_open" class="fixed btn bg-primary bottom-4 right-8"
        @click="setChat(true)">Chat</button>

    <div x-cloak id="chat-box" x-show="chat_open"
        class="fixed h-3/4 bottom-4 right-8 rounded border w-[400px] z-[1000] flex flex-col">
        <div class="p-4 bg-white border-b shrink-0">
            Chat Messages
            <button class="btn" @click="setChat(false)">&times;</button>
        </div>

        <div x-show="chat_selected != false" class="p-4 bg-white flex-1 min-h-0 flex flex-col">
            <div class="border-b">
                <button class="btn text-xl" @click="chat_selected = false">&lt;</button>
                <span x-text="chat_selected?.name"></span>
                <input type="hidden" id="chat-receiver" />
            </div>
            <ul class="flex-1 overflow-y-auto" id="chat-msgs"></ul>
            <input type="text" class="form-control" placeholder="Enter your text" id="chat-text-input" />
        </div>

        <ul id="list-of-users" x-show="chat_selected == false" class="bg-white flex-1 min-h-0 flex flex-col">
        </ul>
    </div>

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
            document.querySelector("#app-content").classList.add("md:flex");
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
                    const alpineRoot = document.querySelector('body[x-data]');

                    const myId = {{ auth()->user()->id }};
                    Echo.channel('department.{{ auth()->user()->department_id }}').listen('.GroupUpdate', (e) => {
                        displayNotification(e);
                    });

                    Echo.private(`user.${myId}`).listen('.UserEvent', (e) => {
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

                    const newMsg = (msg, color) => {
                                const el = $(`<li class='flex flex-col gap-y-1 p-2 ${color}'>
                                    <span>${msg.message}</span>
                                    <span class='text-xs'>${msg.time}</span>
                                </li>`);

                                $("#chat-msgs").append(el);
                    }

                    Echo.private(`chat.${myId}`).listenForWhisper('chat', (e) => {
                            if (Alpine.$data(alpineRoot).chat_selected?.id == e.from) {
                                newMsg(e, 'bg-blue-400 border-2 rounded');
                            } else {
                                displayNotification({
                                    message: `You've received a text: ${e.message}`,
                                    bg: ['bg-red-600', 'text-white'],
                                    options: {mode: 'both'},
                                });
                            }
                    });

                        const sendChatMsg = () => {
                            const value = $("#chat-text-input").val();
                            const to = $("#chat-receiver").val();
                            if (!value || !to) {
                                return;
                            }
                            var msg = {
                                message: value,
                                from: myId,
                                time: new Date(),
                            };

                            const a = Echo.private(`chat.${to}`).whisper('chat', msg);

                                newMsg(msg, 'bg-gray-400 border-2 rounded');
                            $("#chat-text-input").val('');
                        };

                        $("#chat-text-input").on('keyup', (e) => {
                            if (e.key == 'Enter') {
                                e.preventDefault();
                                sendChatMsg();
                            }
                        });

                        axios.get('/api/active-users').then(({
                            data
                        }) => {
                            data.forEach((user) => {
                                const el = $(`<li data-id="${user.id}" data-name="${user.name}" data-dept="${user.department.name}" class='p-2 flex flex-col hover:bg-gray-400'">
                        <span>${user.name}</span>
                        <span class='text-xs'>${user.department.name}</span>
                        </li>`);

                                el.on('click', function() {
                                    const alpineRoot = document.querySelector(
                                        'body[x-data]'); // adjust selector if body isn't the root
                                    Alpine.$data(alpineRoot).selectChat({
                                        id: user.id,
                                        name: user.name,
                                        department: user.department.name,
                                    });
                                    $("#chat-receiver").val(user
                                        .id);
                                });

                                $("#list-of-users").append(el);
                            });
                        });
                    });

                const setupChat = () => {
                    // Echo.join(`chat.${id}`);
                    $("#chat-msgs").html("");
                }
    </script>
    @stack('scripts')
</body>

</html>
