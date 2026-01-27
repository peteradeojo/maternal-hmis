<div x-data="{ open: false }" class="border px-4 py-2" @click="$dispatch('closeTabs');open = !open" x-bind:class="open ? '' : 'cursor-pointer'" @foo="open = false">
    <p>{{ $visit->readable_visit_type }} - {{ $visit->created_at?->format('Y-m-d') }} #{{$visit->id}}</p>

    <template x-if="!open">
        <p class="text-xs">Click to view more</p>
    </template>
    <div x-transition x-show="open">
        {{ $slot }}
    </div>
</div>
