<div class="grid gap-y-2">
    <div>
        <button @click="$dispatch('open-order')" class="btn bg-green-500 text-white">Order A Test</button>
    </div>

    @foreach ($tests as $test)
        <livewire:lab.test :test="$test" />
    @endforeach

    <x-overlay-modal id="order" title="Order Test">
        <p>Add a test</p>
        <livewire:dynamic-product-search @selected="addTest($event.detail)" :departmentId="5" />
    </x-overlay-modal>
</div>
