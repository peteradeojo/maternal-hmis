@php
    $routes = departmentRoutes()[auth()->user()->department_id];
@endphp

<nav class="navbar-nav">
    <div class="navbar-brand">
        <img src="{{ asset('favicon-3.png', app()->isProduction()) }}" alt="" width="30">
        <a href="/">Dashboard</a>
    </div>

    <ul class="nav">
        @foreach ($routes as $d => $name)
            <li class="nav-item">
                <a href="{{ $d }}" class="nav-link">{{ $name }}</a>
            </li>
        @endforeach
    </ul>
</nav>