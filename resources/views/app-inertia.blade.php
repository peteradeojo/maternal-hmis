@php
    $routes = authorizedRoutes();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow" />
    <!--<title>Inertia</title>-->
    @vite('resources/js/app.js')

    <link href="{{ asset('datatables/datatables.min.css') }}" rel="stylesheet" />

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
    <x-inertia::head />
</head>

<body class="h-dvh grid place-items-center">
    <!--<x-inertia::app />-->
    @inertia()
</body>

</html>
