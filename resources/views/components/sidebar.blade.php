@php
    $routes = departmentRoutes()[auth()->user()->department_id];
@endphp

<nav class="navbar-nav no-print">
    <div class="navbar-brand">
        <img src="{{ asset('favicon-3.png') }}" alt="" width="30">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </div>

    <ul class="nav">
        @foreach ($routes as $d => $name)
            <li class="nav-item">
                <a href="{{ $d }}" class="nav-link">{{ $name }}</a>
            </li>
            @endforeach
            <li class="nav-item">
                <a href="{{ route('user-profile') }}" class="nav-link">Profile</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link">Logout</a>
            </li>
    </ul>
</nav>
