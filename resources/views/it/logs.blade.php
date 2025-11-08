{{-- <!DOCTYPE html>
<html>

<head>
    <title>Live Logs</title>

    @vite(['resources/css/app.scss', 'resources/css/app.css'])
    <style>
        body {
            font-family: monospace;
            background: #111;
            color: #0f0;
            padding: 10px;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    <h2>Laravel Log Stream</h2>
    <pre id="log"></pre>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @vite(['resources/js/app.js', 'resources/js/util.js'])
    @livewireScripts
    <script>
        $(document).ready(() => {
            // Echo.private("logs").listen('.LogEvent', function(e) {
            //     console.log(e);
            // });
        });
    </script>
</body>

</html> --}}


@extends('layouts.app')
@section('title', 'Log stream')

@push('styles')
    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            padding: 0;
            margin: 0;
        }
    </style>
@endpush

@section('content')
    {{-- <div class="bg-white p-4">
        <p class="text-lg font-semibold">Laravel Log Stream</p>
    </div> --}}

    <div class="bg-black p-4 overflow-y-auto max-h-[90dvh]">
        <pre id="log" class="border-yellow-500 text-white">

        </pre>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const logs = document.getElementById("log");

            const COLORS = {
                INFO: 'green-100',
                ERROR: 'red-400',
                EMERGENCY: 'red-600',
                DEBUG: 'yellow-400',
            };

            Echo.private("logs").listen('.Log', function(ev) {
                // logs.textContent +=
                //     `[${new Date(ev.datetime).toLocaleString()}] ${ev.channel}.${ev.level_name}: ${ev.message}\n`;
                logs.innerHTML += `<span class="text-${COLORS[ev.level_name] || 'white'}">[${new Date(ev.datetime).toLocaleString()}] ${ev.channel}.${ev.level_name}: ${ev.message}<span>\n`;

                // logs.scroll
                logs.scroll(0,logs.scrollHeight);
            });
        });
    </script>
@endpush
