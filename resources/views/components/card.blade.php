@props(['border' => 'blue-500', 'color' => 'blue-500'])

<div class="bg-white rounded-lg shadow p-6 border-l-4 border-{{ $border }}">
    <div class="flex items-center">
        @isset($title)
            <span class="text-{{ $color }}">
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
