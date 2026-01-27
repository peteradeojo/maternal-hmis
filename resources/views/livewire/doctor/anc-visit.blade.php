<div class="px-2">
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="py-2">
        <x-anc-log :visit="$visit" :profile="$visit->profile" />
    </div>

    <livewire:doctor.treat :visit="$visit->visit" />
</div>
