<div>
    {{-- @dump($visit) --}}
    {{-- @dump($bills) --}}

    <div class="py-4">
        <p class="text-xl font-bold">Laboratory</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Test</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills['consultation']['lab']['items'] as $cTests)
                    <tr>
                        <td>{{ $cTests['description'] }}</td>
                        <td>{{ $cTests['total_amt'] }}</td>
                    </tr>
                @endforeach
                <tr class="font-bold">
                    <td>Total</td>
                    <td>{{ $bills['consultation']['lab']['total'] }}</td>
                </tr>
            </tbody>
        </table>
        {{-- <div class="p-3"> --}}
        {{--     <p class="text-lg font-semibold">Add more tests</p> --}}
        {{--     <livewire:dynamic-product-search :departmentId="5" @selected="addItem($event.detail.id, 'tests')" /> --}}
        {{-- </div> --}}
    </div>

    <div class="py-4">
        <p class="text-xl font-bold">Radiology</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills['consultation']['radio']['items'] as $cTests)
                    <tr>
                        <td>{{ $cTests['description'] }}</td>
                        <td>{{ $cTests['total_amt'] }}</td>
                    </tr>
                @endforeach
                <tr class="font-bold">
                    <td>Total</td>
                    <td>{{ $bills['consultation']['radio']['total'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="py-4">
        <p class="text-xl font-bold">Pharmacy</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills['consultation']['phm']['items'] as $cTests)
                    <tr>
                        <td>{{ $cTests['description'] }}</td>
                        <td>{{ $cTests['total_amt'] }}</td>
                    </tr>
                @endforeach
                <tr class="font-bold">
                    <td>Total</td>
                    <td>{{ $bills['consultation']['phm']['total'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="py-3">
        <p class="bold text-xl">Others</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills['consultation']['other'] as $i => $item)
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        {{-- <td>{{ number_format($item->amount, 2) }}</td> --}}
                        <td class="no-print flex items-center justify-between gap-x-4">
                            @if ($item['saved'] ?? false)
                                <span>{{ number_format($item['total_amt']) }}</span>
                                <span>
                                    <button wire:click.prevent="removeItem({{ $i }}, 'others')"
                                        class="btn btn-sm bg-red-600 text-white"><i class="fa fa-trash"></i></button>
                                    <button wire:click.prevent="editItem({{ $i }}, 'others')"
                                        class="btn btn-sm bg-green-400"><i class="fa fa-pencil"></i></button>
                                </span>
                            @else
                                {{-- <input type="number" --}}
                                {{--     wire:keyup.enter.prevent="saveItem({{ $i }}, 'others')" --}}
                                {{--     wire:blur="saveItem({{ $i }}, 'others')" --}}
                                {{--     wire:model="others.{{ $i }}.product.amount" class="form-control" --}}
                                {{--     value="{{ $item['product']['amount'] }}" step="1" required /> --}}
                                {{-- <button wire:click.prevent="saveItem({{ $i }}, 'others')" href="#" --}}
                                {{--     class="btn btn-sm bg-blue-400 text-white"><i class="fa fa-save"></i></button> --}}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No other bills</td>
                    </tr>
                @endforelse
                {{-- <tr> --}}
                {{--     <td class="bold">Subtotal</td> --}}
                {{--     <td>{{ number_format($others_amt, 2) }}</td> --}}
                {{-- </tr> --}}
            </tbody>
        </table>

        <div class="py-2">
            {{-- <p><b>Total: </b> {{ number_format($grandTotal + $others_amt) }}</p> --}}
        </div>
    </div>

    <div class="flex justify-end sticky bottom-0 pb-4">
        <button wire:click="saveBill" class="btn bg-blue-400 text-white"><i class="fa fa-plus"></i> Create Bill</button>
    </div>
</div>
