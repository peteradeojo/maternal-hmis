@props(['content' => ''])

<div x-data="{ open: false }" class="relative inline-block">
    <div x-on:mouseenter="open = true" x-on:mouseleave="open = false" x-on:focus="open = true" x-on:blur="open = false"
        class="inline-block cursor-pointer">
        {{ $slot }}
    </div>

    {{-- Tooltip content --}}
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-10"
        class="absolute start-1/2 z-10 -ms-20 flex w-40 flex-col items-center justify-center pb-0.5 will-change-transform pointer-events-none">
        <div
            class="flex-none rounded-lg bg-zinc-900 px-2.5 py-2 text-center text-xs font-semibold text-zinc-50 dark:bg-zinc-700 shadow-lg">
            {{ $content }}
        </div>

        {{-- Triangle --}}
        {{-- <div class="h-0 w-0 flex-none border-e-4 border-s-4 border-t-4 border-e-transparent border-s-transparent border-t-zinc-900 dark:border-t-zinc-700"
            aria-hidden="true"></div> --}}
    </div>
</div>
