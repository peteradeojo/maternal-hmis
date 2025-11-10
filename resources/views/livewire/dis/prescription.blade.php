<div x-on:quote-saved="window.location.href=''">
    @php
        $editable = !$quoteDone;
    @endphp
    <p>#{{ $bill->bill_number }}</p>
    <form wire:submit.prevent="save">
        @csrf
        <table class="table table-list" id="p-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration (in days)</th>
                    <th>Available</th>
                    <th>Amount ({{ config('app.currency') }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $t)
                    @if ($bill->status == Status::PAID->value and $t->amount == 0)
                        @continue
                    @endif

                    <tr>
                        <td>{{ $t->description }}</td>
                        <td>{{ $t->meta['data']['dosage'] }}</td>
                        <td>{{ $t->meta['data']['frequency'] }}</td>
                        <td>{{ $t->meta['data']['duration'] }}</td>
                        <td><input type="checkbox" wire:model="items.{{ $i }}.available"
                                data-id="{{ $t->id }}" class="availability"
                                @if ($t->available) checked @endif
                                @unless ($editable) disabled @endunless>
                        </td>
                        <td>
                            <input type="number" wire:model="items.{{ $i }}.amount" step="0.01"
                                min="0" data-id="{{ $t->id }}" class="amount form-control"
                                value="{{ $t->amount }}" @unless ($editable) readonly @endunless />
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td>{{ $items->sum('amount') }}</td>
                </tr>
                <tr>
                    {{-- @unless ($bill->entries->doesntContain(fn($b) => ($b->meta['available'] ?? false) == false)) --}}
                    <td colspan="2">Are you done with this quote? <input type="checkbox" wire:change="$refresh"
                            wire:model="quoteDone" />
                    </td>
                    {{-- @endunless --}}
                </tr>
            </tfoot>
        </table>

        <div class="form-group flex justify-end">
            <button class="btn bg-blue-400 hover:bg-green-400 text-white"
                @if ($bill->status == 6 && $quoteDone) disabled @endif>Save <i class="fa fa-save"></i></button>
        </div>
    </form>
</div>
