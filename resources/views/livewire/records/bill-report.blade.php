<div>
    {{-- Stop trying to control. --}}
    <div class="py-3">
        <p class="text-xl bold">Tests Taken</p>
        @php
            $thisTotal = 0;
        @endphp

        <table class="table">
            <thead>
                <tr>
                    <th>Test</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tests as $i => $test)
                    <tr>
                        <td>{{ $test['product']['name'] }}</td>
                        <td class="flex justify-between items-center">
                            <span>
                                {{ $test['product']['amount'] ?? '0.00' }}
                            </span>
                            {{-- @if ($test['saved'] ?? true)
                                <span>
                                    <button wire:click="removeItem({{ $i }}, 'tests')"
                                        class="btn bg-red-500 text-white"><i class="fa fa-trash"></i></button>
                                    <button wire:click="editItem({{ $i }}, 'tests')"
                                        class="btn bg-green-500 text-white"><i class="fa fa-pencil"></i></button>
                                </span>
                            @else
                                <input type="number" wire:keyup.enter="saveItem({{ $i }}, 'tests')"
                                    wire:model="tests.{{ $i }}.product.amount"
                                    value="{{ $test['product']['amount'] }}" required />
                                <button wire:click="saveItem({{ $i }}, 'tests')"
                                    class="btn btn-sm bg-blue-400 text-white"><i class="fa fa-save"></i></button>
                            @endif --}}
                        </td>
                        @php
                            $thisTotal += $test['product']['amount'];
                        @endphp
                    </tr>
                @endforeach

                <tr>
                    <td class="bold">Subtotal</td>
                    <td class="text-right bold">{{ number_format($thisTotal, 2) }}</td>
                </tr>
                @php
                    $grandTotal += $thisTotal;
                @endphp
            </tbody>
        </table>

        <div class="p-3">
            <p class="text-lg font-semibold">Add more tests</p>
            <livewire:dynamic-product-search :departmentId="5" @selected="addItem($event.detail.id, 'tests')" />
        </div>
    </div>

    <div class="py-3">
        @php
            $thisTotal = 0;
        @endphp
        <p class="text-xl bold">Radiology</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Procedure</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scans as $i => $img)
                    <tr>
                        <td>{{ $img['product']['name'] }}</td>
                        <td class="flex items-center justify-between">
                            <span>{{ $img['product']['amount'] ?? '0.00' }}</span>
                            {{-- @if ($img['saved'] ?? true)
                                <span>
                                    <button wire:click="removeItem({{ $i }}, 'scans')"
                                        class="btn btn-sm bg-red-500 text-white">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <button wire:click="editItem({{ $i }}, 'scans')"
                                        class="btn btn-sm bg-green-400 text-white">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                </span>
                            @else
                                <input type="number" wire:model="scans.{{ $i }}.amount"
                                    value="{{ $img['amount'] }}"
                                    wire:keyup.enter="saveItem({{ $i }}, 'scans')" />
                                <button wire:click="saveItem({{ $i }}, 'scans')"
                                    class="btn btn-sm bg-blue-400 text-white">
                                    <i class="fa fa-save"></i>
                                </button>
                            @endif --}}
                        </td>
                        @php
                            $thisTotal += $img['product']['amount'];
                        @endphp
                    </tr>
                @endforeach

                <tr>
                    <td class="bold">Subtotal</td>
                    <td class="text-right bold">{{ number_format($thisTotal, 2) }}</td>
                </tr>
                @php
                    $grandTotal += $thisTotal;
                @endphp
            </tbody>
        </table>

        <div class="p-3">
            <p class="text-lg font-semibold">Add more Scans</p>
            <livewire:dynamic-product-search :departmentId="7" @selected="addItem($event.detail.id, 'scans')" />
        </div>
    </div>

    <div class="py-3">
        @php
            $thisTotal = 0;
        @endphp
        <p class="text-xl bold">Treatments</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($drugs as $i => $img)
                    {{-- @dump($img) --}}
                    <tr>
                        <td>{{ $img['product']['name'] }} {{ $img['data']['dosage'] }} {{ $img['data']['duration'] }}
                            (days)
                        </td>
                        <td class="flex items-center justify-between">
                            <span>{{ $img['data']['amount'] ?? 0 }}</span>
                        </td>
                        @php
                            $thisTotal += $img['data']['amount'] ?? 0;
                        @endphp
                    </tr>
                @endforeach

                <tr>
                    <td class="bold">Subtotal</td>
                    <td class="text-right bold">{{ number_format($thisTotal, 2) }}</td>
                </tr>
                @php
                    $grandTotal += $thisTotal;
                @endphp
            </tbody>
        </table>

        <div class="p-3">
            <p class="text-lg font-semibold">Add more drugs</p>
            <livewire:billing.add-prescription @selected="addDrug($event.detail)" />
        </div>
    </div>

    <div class="py-3">
        <p class="bold text-xl">Others</p>

        @unless ($visit->status == Status::closed->value)
            <div class="py-2">
                <livewire:dynamic-product-search @selected="addItem($event.detail.id, 'others')"
                    @selected_temp="addNewItem($event.detail.name)" />
            </div>
        @endunless
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($others as $i => $item)
                    <tr>
                        <td>{{ $item['product']['name'] }}</td>
                        {{-- <td>{{ number_format($item->amount, 2) }}</td> --}}
                        <td class="no-print flex items-center justify-between gap-x-4">
                            @if ($item['saved'] ?? false)
                                <span>{{ number_format($item['product']['amount']) }}</span>
                                <span>
                                    <button wire:click.prevent="removeItem({{ $i }}, 'others')"
                                        class="btn btn-sm bg-red-600 text-white"><i class="fa fa-trash"></i></button>
                                    <button wire:click.prevent="editItem({{ $i }}, 'others')"
                                        class="btn btn-sm bg-green-400"><i class="fa fa-pencil"></i></button>
                                </span>
                            @else
                                <input type="number"
                                    wire:keyup.enter.prevent="saveItem({{ $i }}, 'others')"
                                    wire:blur="saveItem({{ $i }}, 'others')"
                                    wire:model="others.{{ $i }}.product.amount" class="form-control"
                                    value="{{ $item['product']['amount'] }}" step="1" required />
                                <button wire:click.prevent="saveItem({{ $i }}, 'others')" href="#"
                                    class="btn btn-sm bg-blue-400 text-white"><i class="fa fa-save"></i></button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No other bills</td>
                    </tr>
                @endforelse
                <tr>
                    <td class="bold">Subtotal</td>
                    <td>{{ number_format($others_amt, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="py-2">
            <p><b>Total: </b> {{ number_format($grandTotal + $others_amt) }}</p>
        </div>
    </div>

    <div class="flex justify-end sticky bottom-0 pb-4">
        <button wire:click="saveBill" class="btn bg-blue-400 text-white"><i class="fa fa-plus"></i> Create Bill</button>
    </div>
</div>
