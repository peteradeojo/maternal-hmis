@props(['id'])

<table id="{{ $id }}" class="w-full text-sm text-left text-gray-800">
    @isset($thead)
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            {{ $thead }}
        </thead>
    @endisset

    @isset($tbody)
        {{ $tbody }}
    @endisset
</table>
