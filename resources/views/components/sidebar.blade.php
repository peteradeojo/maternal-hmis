<nav class="navbar-nav no-print w-full">
    <div class="bg-white p-2">
        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt="" class="rounded-full w-12">
        <p class="font-bold">{{ auth()->user()->name }}</p>
        <p>{{ auth()->user()->department->name }}</p>
    </div>

    {{-- <div class="navbar-brand">
        <img src="{{ asset('favicon-3.png') }}" alt="" width="30">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </div> --}}

    <ul class="nav">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
        </li>
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
