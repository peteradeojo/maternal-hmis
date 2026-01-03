<nav class="navbar-nav no-print w-full bg-[#333] block">
    <ul class="nav overflow-y">
        @foreach ($routes as $d => [$name, $icon])
            <li class="nav-item border-b-2 last-of-type:border-b-0" :class="aside ? 'py-1' : 'py-2 text-lg'">
                <a href="{{ $d }}" title="{{ $name }}"
                    class="flex-center gap-x-2 hover:text-blue-300 duration-200 p-[4px] text-[#fff]"
                    :class="!aside ? 'justify-center transform hover:scale-125' : ''">
                    <i title="{{ $name }}" class="fa {{ $icon }}"></i>
                    <span x-show="aside">{{ $name }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
