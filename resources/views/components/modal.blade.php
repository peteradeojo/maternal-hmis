@props(['id'])

<div class="modal hide" id="{{ $id }}">

    <div class="content p-3 bg-white">
        {{ $slot }}
    </div>
</div>
