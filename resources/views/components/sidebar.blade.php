<nav class="navbar-nav no-print w-full">
    <div class="bg-white p-2 text-center">
        <img src="https://ui-avatars.com/api/?name={{ session(config('app.generic_doctor_id')) ?? auth()->user()->name }}"
            alt="" class="rounded-full w-12 m-auto">
        <p x-show="aside" class="font-bold">{{ session(config('app.generic_doctor_id')) ?? auth()->user()->name }}</p>
        <p x-show="aside">{{ auth()->user()->department->name }}</p>
    </div>

    <ul class="nav">
        @foreach ($routes as $d => [$name, $icon])
            <li class="nav-item" :class="aside ? 'py-1' : 'py-2 text-lg'">
                <a href="{{ $d }}" title="{{ $name }}"
                    class="nav-link hover:text-blue-300 duration-200"
                    :class="!aside ? 'justify-center transform hover:scale-125' : ''">
                    <i title="{{ $name }}" class="fa {{ $icon }}"></i>
                    <span x-show="aside">{{ $name }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
