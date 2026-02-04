<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <div class="flex-center gap-x-2 justify-end mb-4">
        @unless ($take->status == Status::closed)
            <button wire:click="save" class="btn bg-blue-500 text-white">Save <i class="fa fa-save"></i></button>
            <button wire:click="approve" @class([
                'bg-black' => $take->status == Status::completed,
                'bg-green-500' =>
                    $take->status != Status::completed && $take->status != Status::closed,
                'btn text-white',
            ])>
                @if ($take->status != Status::completed)
                    Approve
                @else
                    Unapprove
                @endif
                <i class="fa fa-check"></i>
            </button>
            <button wire:click="apply" class="btn bg-red-500 text-white">Apply <i class="fa fa-save"></i></button>
        @endunless
    </div>

    @unless ($take->status == Status::closed)
        <livewire:inventory-product-search @handle-select="addItem($event.detail.product)" />
    @endunless

    <table class="table py-4 my-2">
        <thead>
            <tr>
                <th>Name</th>
                <th>Cost price</th>
                <th>System Value</th>
                <th>Count value</th>
                <th>Discrepancy</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($counted as $i => $_count)
                <tr wire:key="{{ $_count['id'] }}">
                    <td>{{ $_count['name'] }} ({{ $_count['base_unit'] }})</td>
                    <td>{{ $_count['unit_cost'] }}</td>
                    <td>{{ $_count['system'] }}</td>
                    <td class="p-2">
                        <input wire:blur="$refresh" wire:model="counted.{{ $i }}.quantity" type="number"
                            class="form-control p-0.5" min=0 @readonly($take->status == Status::closed) required />
                    </td>
                    <td>{{ $_count['quantity'] - $_count['system'] }}</td>
                    <td>
                        @if ($take->status == Status::active)
                            <button wire:click="removeItem({{ $i }}, {{ $_count['id'] }})"
                                class="btn btn-sm bg-red-500 text-white">&times;</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td>No items added.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
