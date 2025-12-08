@props(['order', 'suppliers'])

{{--
This component heavily relies on accessing data via the global $store.
In the parent html, in a script tage, initialise the 'order' $store using
Alpine.store('order', {
    'purchases' => [] // or @js($data),
    'supplier_id' => null
})
--}}

<div x-data="{ in_lot: false }" {{ $attributes }}>
    <div class="form-group sm:w-1/2">
        <label>Supplier</label>
        <x-input-select x-init="$store.order.supplier_id = {{ $order->supplier->id ?? $suppliers->first()?->id }}" x-model="$store.order.supplier_id" name="supplier_id">
            <option disabled selected>Select supplier</option>
            @foreach ($suppliers as $sup)
                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
            @endforeach
        </x-input-select>
    </div>
    <div class="form-group">
        <label>Create lot for this purchase?
            <input type="checkbox" :checked="$store.order.in_lot" @change="$store.order.in_lot = $event.target.checked" />
        </label>
    </div>

    <template x-if="$store.order.in_lot">
        <div>
            <p class="basic-header">Lot Information</p>
            <div class="form-group">
                <label>Lot number</label>
                <x-input-text x-model x-model="$store.order.lot.lot_number" name="lot_number" class="form-control" required />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label>Manufacture date</label>
                    <x-input-date x-model="$store.order.lot.manufacture_date" name="manufacture_date" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Expiry date</label>
                    <x-input-date x-model="$store.order.lot.expiry_date" name="expiry_date" class="form-control" required />
                </div>
            </div>
        </div>
    </template>

    <div
        x-on:handle-select="$dispatch('inventory-product-selected', {product: $event.detail.product, purchases: $store.order.purchases})">
        <livewire:inventory-product-search :category="'DRUG'" />
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Unit</th>
                <th>Current Qty.</th>
                <th>Purchase Qty</th>
                <th>Cost Price</th>
                <th>Quantity received</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(line, index) in $store.order.purchases" x-key="line">
                <tr>
                    <td x-text="line.item.name"></td>
                    <td x-text="line.item.sku"></td>
                    <td>
                        <select x-init="!line.unit ? line.unit = 'tab' : line.unit = line.unit" x-model="line.unit" class="form-control" required>
                            @include('components.inventory.unit-options')
                        </select>
                    </td>
                    <td x-text="line.item.balance"></td>
                    <td class="max-w-[50px] p-1">
                        <input type="hidden" x-model="line.id" :name="`order[${index}][id]`" />
                        <input type="number" :name="'order[' + index + '][quantity]'"
                            class="w-full p-0.5 border-0 border-b outline-1 block" x-model="line.qty_ordered"
                            min="0" step="0.01" />
                    </td>
                    <td>
                        <input type="number" x-model="line.unit_cost" class="p-0.5 border-0 border-b block" required
                            :name="`order[${index}][cost]`" step="0.01" />
                    </td>
                    <td>
                        <input type="number" x-model="line.qty_received" class="p-0.5 border-0 border-b block" required
                            :name="`order[${index}][qty_received]`" step="0.01" />

                    </td>
                    <td>
                        <button type="button" @click="$store.order.purchases?.splice(index, 1)"
                            class="btn btn-sm bg-red-400 text-white"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            </template>
        </tbody>
        <tfoot>
            <tr>
                <td class="font-bold">Total</td>
                <td></td>
                <td></td>
                <td x-text="$store.order.purchases?.reduce((a,b) => Number(a) + Number(b.qty_ordered), 0)"></td>
                <td></td>
                <td
                    x-text="$store.order.purchases?.reduce((a,b) => Number(a) + Number(b.unit_cost) * Number(b.qty_received), 0).toFixed(2)">
                </td>
                <td
                    x-text="$store.order.purchases?.reduce((a,b) => Number(a) + Number(b.unit_cost) * Number(b.qty_ordered), 0).toFixed(2)">
                </td>
            </tr>
        </tfoot>
    </table>
</div>
