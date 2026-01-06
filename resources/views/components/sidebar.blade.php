<nav class="navbar-nav no-print w-full bg-[#333] block">
    <ul class="nav overflow-y">
        <li class="nav-item border-b-2" :class="aside ? 'py-1' : 'py-2 text-lg'">
            <a href="{{ route('dashboard') }}"
                class="flex-center gap-x-2 hover:text-blue-300 duration-200 p-[4px] text-[#fff]"
                :class="!aside ? 'justify-center transform hover:scale-125' : ''">
                <i class="fa fa-home"></i>
                <span x-show="aside">Dashboard</span>
            </a>
        </li>

        @foreach ($routes as $map)
            <li class="p-1 nav-item border-b-2" x-data="{ open: false }">
                <div class="flex-center gap-x-2 cursor-pointer text-white hover:text-blue-300 duration-200"
                    x-on:click="open = !open">
                    <i class="fa {{@$map['label'][1]}}"></i>
                    <span>{{ @ucfirst($map['label'][0]) }}</span>
                    <i class="fa ml-auto" :class="{ 'fa-caret-right': !open, 'fa-caret-down': open, }"></i>
                </div>

                <ul x-transition x-show="open && aside" class="ps-4">
                    @foreach ($map['routes'] as $label => $routeInfo)
                        <li class="p-2">
                            <a href="{{ @$routeInfo[0] }}"
                                class="flex-center gap-x-2 text-white hover:text-blue-300 duration-200">
                                <i class="fa {{ @$routeInfo[1] }}"></i>
                                <span>{{ @$label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach

        <li class="nav-item border-b-2 last-of-type:border-b-0" :class="aside ? 'py-1' : 'py-2 text-lg'">
            <a href="{{ route('user-profile') }}"
                class="flex-center gap-x-2 hover:text-blue-300 duration-200 p-[4px] text-[#fff]"
                :class="!aside ? 'justify-center transform hover:scale-125' : ''">
                <i class="fa fa-gear"></i>
                <span x-show="aside">Settings</span>
            </a>
        </li>
        <li class="nav-item" :class="aside ? 'py-1' : 'py-2 text-lg'">
            <a href="{{ route('logout') }}"
                class="flex-center gap-x-2 hover:text-blue-300 duration-200 p-[4px] text-[#fff]"
                :class="!aside ? 'justify-center transform hover:scale-125' : ''">
                <i class="fa fa-sign-out"></i>
                <span x-show="aside">Log out</span>
            </a>
        </li>

    </ul>
</nav>
