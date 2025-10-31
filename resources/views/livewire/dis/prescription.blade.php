<div>
    @php
        $editable = $doc->treatments->contains(fn($t) => $t->status !== Status::quoted->value);
    @endphp
    <form wire:submit.prevent="save"
        action="{{ route('dis.get-prescription') }}?type={{ $type }}&id={{ $id }}" method="post"
        x-data="{
            items: @entangle('items'),
            quoteDone: @entangle('quoteDone'),
            get totalAmount() {
                return this.items.reduce((a, b) => {
                    if (b.available) {
                        return parseFloat(a) + parseFloat(b.amount || 0);
                    }
                    return a;
                }, 0);
            }
        }">
        @csrf
        <table class="table table-list" id="p-table">
            <thead>
                <tr>
                    <th>Prescription</th>
                    <th>Available</th>
                    <th>Amount ({{ config('app.currency') }})</th>
                </tr>
            </thead>
            <tbody>
                {{-- @php
                    $total = 0;
                @endphp --}}
                {{-- @foreach ($items as $i => $t)
                    <tr>
                        <td>{{ $t->name }} {{ $t->dosage }} {{ $t->frequency }} {{ $t->duration }}</td>
                        <td><input type="checkbox" wire:model="items.{{ $i }}.available"
                                data-id="{{ $t->id }}" class="availability"
                                @if ($t->available) checked @endif
                                @unless ($editable) disabled @endunless>
                        </td>
                        <td>
                            <input type="number" name="amount[{{ $t->id }}]"
                                wire:model="items.{{ $i }}.amount" step="0.01" min="0"
                                data-id="{{ $t->id }}" class="amount form-control" value="{{ $t->amount }}"
                                @unless ($editable) readonly @endunless />
                        </td>
                    </tr>
                @endforeach --}}

                <template x-for="(t, i) in items" :key="i">
                    <tr>
                        <td x-text="`${t.name} ${t.dosage} ${t.frequency} ${t.duration}`"></td>
                        <td>
                            <input type="checkbox" x-model="t.available"
                                :disabled="!@js($editable)" />
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" x-model.number="t.amount"
                                class="form-control" :readonly="!@js($editable)" />
                        </td>
                    </tr>
                </template>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td x-text="totalAmount"></td>
                </tr>
                <tr>
                    @unless ($doc->all_prescriptions_available)
                        <td colspan="2">Are you done with this quote? <input type="checkbox" x-model="quoteDone" />
                        </td>
                    @endunless
                </tr>
            </tfoot>
        </table>

        @if ($editable)
            <div class="form-group flex justify-end">
                <button class="btn bg-blue-400 hover:bg-green-400 text-white">Save <i class="fa fa-save"></i></button>
            </div>
        @endif
    </form>
</div>
