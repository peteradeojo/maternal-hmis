<div x-data="{
    bill: @entangle('bill'),
}">
    <p>Bill No: {{ $bill->bill_number }}</p>
    <p>Patient: {{ $bill->patient->name }} ({{ $bill->patient->card_number }})</p>

    <div class="py-2">
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bill->entries as $entry)
                    <tr>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $entry->total_price }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td>Total</td>
                    <td>{{ number_format($bill->amount) }}</td>
                </tr>
                <tr>
                    <td>Paid</td>
                    <td>{{ number_format($bill->paid) }}</td>
                </tr>
                <tr>
                    <td>Balance</td>
                    <td>{{ number_format($bill->balance) }}</td>
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
