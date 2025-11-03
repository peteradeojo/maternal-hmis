@props(['id', 'title'])

<div x-cloak x-data="{ open: false }" x-on:open-{{ $id }}.window="open = true"
    x-on:close-{{ $id }}.window="open = false" x-on:keyup.escape.window="open = false"
    class="fixed inset-0 z-50 flex justify-end" x-show="open">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 transition-opacity duration-300" @click="open = false" x-show="open"
        x-transition.opacity></div>

    <!-- Sliding panel -->
    <div class="relative h-dvh overflow-y-auto w-1/2 bg-white shadow-xl transition-transform duration-300"
        :class="open ? 'translate-x-0' : 'translate-x-full'"

        x-transition:enter="transform transition ease-in duration-300" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-300"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">

        <div class="p-4 border-b flex justify-between items-center sticky bg-white top-0">
            <h2 class="text-lg font-semibold" id="modal-title-{{ $id }}">
                {{ $title ?? 'Title' }}
            </h2>
            <button @click="open = false" class="text-gray-600">&times;</button>
        </div>
        <div class="px-4 py-8" id="modal-body-{{ $id }}">
            {{ $slot }}
        </div>
    </div>
</div>
