@props(['id'])

<div id="{{ $id }}" x-data="{ open: false }" x-on:open-{{ $id }}.window="open = true"
    @keyup.escape.window="open = false" :class="{ 'hide': !open, 'modal': true }" {{ $attributes }}
    @click.self="open=false" x-on:close-{{ $id }}.window="open = false">

    <div class="content px-3 bg-white" x-show="open" x-transition>
        <div class="w-full flex justify-end">
            <button class="btn text-lg" @click.stop="open = false">&times;</button>
        </div>
        {{ $slot }}
    </div>
</div>
