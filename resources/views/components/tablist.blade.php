@props([
    'active' => 0
])

<div x-data="{
        active: {{ $active }},
        select(i) { this.active = i }
    }" wire:ignore>
    <nav class="flex border-b">
        @foreach($slot as $i => $tab)
            <a href="#"
               @click.prevent="select({{ $i }})"
               :class="active === {{ $i }} ? 'active-tab' : 'default-tab'"
               class="px-4 py-2"
            >
                {{-- {{ $tab->attributes->get('title') }} --}}

                @dump($tab)
            </a>
        @endforeach
    </nav>

    <div class="tab-content mt-4">
        @foreach($slot as $i => $tab)
            <div x-show="active === {{ $i }}" x-cloak>
                {{ $tab }}
            </div>
        @endforeach
    </div>
</div>
