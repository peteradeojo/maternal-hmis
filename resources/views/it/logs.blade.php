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
        <pre id="log" class="border-yellow-500 text-white"></pre>
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
                const {
                    context,
                    level_name,
                    channel,
                    message,
                    datetime,
                    extra: {
                        user,
                        department,
                        exception,
                    },
                    extra
                } = ev;

                let HTML =
                    `<span class="text-${COLORS[level_name]}">[User: ${extra.user} | Department: ${extra.department}]</span>
<span class="text-${COLORS[level_name] || 'white'}">[${new Date(datetime).toLocaleString()}] ${channel}.${level_name}: ${message}<span>`;

                if (exception && exception.length > 0) {
                    HTML += `<span>Trace:</span>`
                    for (let index = 0; index < Math.min(exception.length, 20); index++) {
                        HTML += `<span class='text-${COLORS[level_name] || 'white'}'>
${exception[index].class}${exception[index].type}${exception[index].function}
${exception[index].file}:${exception[index].line}</span>`
                    }
                }

                logs.innerHTML += HTML;

                // logs.scroll
                logs.scroll(0, logs.scrollHeight);
            });
        });
    </script>
@endpush
