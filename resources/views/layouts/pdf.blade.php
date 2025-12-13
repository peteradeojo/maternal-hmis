{{-- <style>
    body {
        font-family: "Arial", Arial, Helvetica, sans-serif;
    }
</style>

<body>
    <div style="">
        <img style="float: left" height="50" src="{{'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('favicon-3.jpg')))}}">
        <p style="clear: both; font-weight: bolder; font-size: 2em; text-align:center; text-transform: uppercase">MATERNAL-CHILD SPECIALISTS' CLINICS, ADO-EKITI, ekiti state</p>
    </div>
    <span style="clear:both"></span>
    @yield('content')
</body> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite(['resources/css/app.css', 'resources/css/app.scss'])
</head>

<body>
    <main class="printable">
        <div class="max-w-full px-4 flex justify-between">
            <img src="{{ asset('favicon-3.jpg') }}" class="w-[50px]" />
            <p class="uppercase font-bold text-center text-2xl text-wrap">maternal-child specialists' clinics, ado-ekiti
            </p>
            <p></p>
        </div>
        <div class="py-8"></div>
        @yield('content')
    </main>
</body>

</html>
