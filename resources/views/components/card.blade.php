{{--
Slots: title, icon, footer
--}}

@props(['border' => 'border-blue-500', 'color' => 'text-blue-500'])

<div class="bg-white rounded-lg shadow p-6 border-l-4 {{ $border }}">
    <div class="flex items-center">
        @isset($title)
            <span class="{{ $color }}">
                {{ $title }}
            </span>
        @endisset

        @isset($icon)
            <div class="p-3 rounded-full bg-blue-100 text-{{ $color }} mr-4">
                {{ $icon }}
            </div>
        @endisset
        <div>
            {{ $slot }}
        </div>
    </div>

    @isset($footer)
        <div class="mt-4">
            <span class="text-{{ $color }} hover:text-{{ $color }} font-medium">
                {{ $footer }}
            </span>
            {{-- <a href="{{ route('iam.roles') }}"
            class="text-{{ $color }} hover:text-{{ $color }} text-sm font-medium">Manage
            Roles &rarr;</a> --}}
        </div>
    @endisset
</div>
