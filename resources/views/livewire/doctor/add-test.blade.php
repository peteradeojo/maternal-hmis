<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <livewire:dynamic-product-search departmentId='5' @selected="save($event.detail)"
        @selected_temp="save($event.detail)" />
</div>
