<div x-data="{
    bill: @entangle('bill'),
}" wire:poll.5s>
    <p>Bill No: {{ $bill->bill_number }}</p>
    <p>Patient: {{ $bill->patient->name }} ({{ $bill->patient->card_number }})</p>

    <div class="py-2">
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $entry)
                    <tr>
                        <td>{{ $entry['description'] }}</td>

                        <td>{{ $entry['status'] }}</td>

                        <td class="flex justify-between items-center">
                            @if ($entry['saved'])
                                <span>{{ number_format($entry['amount']) }}
                                    @if ($entry['tag'] == 'drug')
                                        <small>({{ $entry['total_price'] }})</small>
                                    @endif
                                </span>
                                <button wire:click="edit({{ $i }})"
                                    class="btn btn-sm bg-green-500 text-white"><i class="fa fa-pencil"></i></button>
                            @else
                                <input type="number" class="form-control"
                                    wire:blur ="save({{ $i }})"
                                    wire:keyup.enter.stop="save({{ $i }})"
                                    wire:model="items.{{ $i }}.total_price" />
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td></td>
                    <td class="flex justify-end">
                        <button wire:click="updateBillDetailsAmt" class="btn bg-blue-400 text-white"
                            @if ($currentHash == $initHash) disabled="disabled" @endif><i class="fa fa-save"></i>
                            Save</button>
                    </td>
                </tr>

                <tr class="font-bold">
                    <td>Total</td>
                    <td>{{ number_format(collect($items)->sum('amount')) }}</td>
                </tr>
                <tr class="font-bold">
                    <td>Paid</td>
                    <td>{{ number_format($bill->paid) }}</td>
                </tr>
                <tr class="font-bold">
                    <td>Balance</td>
                    <td>{{ number_format(collect($items)->sum('amount') - $bill->paid) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($bill->balance > 0)
        <div>
            <form wire:submit.prevent="pay">
                <div class="grid grid-cols-2 gap-x-4">
                    <div class="form-group">
                        <label>Payment method</label>
                        <select class="form-control" wire:change="methodAdjusted" wire:model="method" required>
                            <option disabled selected>Select a payment method</option>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank/Transfer</option>
                            <option value="card">Card/POS</option>
                            <option value="insurance">Insurance</option>
                            <option value="waived">Waive amount</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <div class="flex gap-x-2">
                            <input type="number" step="0.01" wire:model="amount" required class="form-control" />
                            <button type="submit" class="btn bg-blue-400 text-white"><i
                                    class="fa fa-save"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <div class="py-2 grid gap-y-2">
        <p class="text-lg">Payments</p>

        @forelse ($bill->payments as $pm)
            <div class="p-2 bg-gray-200">
                <p>{{ ucfirst($pm->payment_method) }}: {{ $pm->amount }}</p>
                <p>{{ $pm->created_at->format('Y-m-d h:i A') }}</p>
            </div>
        @empty
            <p>No payment made.</p>
        @endforelse
    </div>
</div>
