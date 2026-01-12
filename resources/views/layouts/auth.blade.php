<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-green-100 w-dvw h-dvh">
    <main class="grid place-items-center w-full h-full">
        @yield('content')
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
        integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

</body>

</html>
