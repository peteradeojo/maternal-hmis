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
                @foreach ($visit->tests as $test)
                    <tr>
                        <td>{{ $test->name }}</td>
                        <td>{{ $test->describable?->amount ?? '0.00' }}</td>
                        @php
                            $thisTotal += $test->describable?->amount;
                        @endphp
                    </tr>
                @endforeach

                @foreach ($visit->visit->tests as $test)
                    <tr>
                        @php
                            $thisTotal += $test->describable?->amount;
                        @endphp
                        <td>{{ $test->name }}</td>
                        <td>{{ $test->describable?->amount ?? '0.00' }}</td>
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
                @foreach ($visit->imagings as $img)
                    <tr>
                        <td>{{ $img->name }}</td>
                        <td>{{ $img->describable?->amount ?? '0.00' }}</td>
                        @php
                            $thisTotal += $img->describable?->amount;
                        @endphp
                    </tr>
                @endforeach

                @foreach ($visit->visit->radios as $test)
                    <tr>
                        @php
                            $thisTotal += $test->describable?->amount;
                        @endphp
                        <td>{{ $test->name }}</td>
                        <td>{{ $test->describable?->amount ?? '0.00' }}</td>
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
    </div>

    <div class="py-3">
        @php
            $thisTotal = 0;
        @endphp
        <p class="text-xl bold">Treatments</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Procedure</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($visit->prescriptions as $img)
                    <tr>
                        <td>{{ $img->name }}</td>
                        <td>{{ $img->describable?->amount ?? '0.00' }}</td>
                        @php
                            $thisTotal += $img->describable?->amount;
                        @endphp
                    </tr>
                @endforeach

                @foreach ($visit->visit->prescriptions as $test)
                    <tr>
                        @php
                            $thisTotal += $test->describable?->amount;
                        @endphp
                        <td>{{ $test->name }}</td>
                        <td>{{ $test->describable?->amount ?? '0.00' }}</td>
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
    </div>

    <div class="py-3">
        <p class="bold text-xl">Others</p>
        <div class="py-3"></div>
        <livewire:product-search @selected="addItem($event.detail.id)" />
        <div class="py-3"></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($others as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ number_format($item->amount, 2) }}</td>
                        <td class="no-print"><a wire:click.prevent="removeItem({{ $item->id }})" href="#"
                                class="text-red-600 underline">&times;</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No other bills</td>
                    </tr>
                @endforelse
                <tr>
                    <td class="bold">Subtotal</td>
                    <td>{{ number_format($otherAmt, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
