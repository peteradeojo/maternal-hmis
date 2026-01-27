<div>
    <button wire:click="$refresh">Reload</button>

    <table class="table table-list">
        <thead>
            <tr>
                <th>Prescription</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration (days)</th>
                <th>Unit price</th>
                <th>Dispensed</th>
                <th></th>
                <th></th>
                <th>Available Qty</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prescriptions as $i => $t)
                <tr wire:key="line-{{ $t['id'] }}"
                    @if ($t['quantity'] > $t['balance']) class="text-red-600 font-semibold" @endif>
                    <td>{{ $t['name'] }}</td>
                    <td>{{ $t['dosage'] }}</td>
                    <td>{{ $t['frequency'] }}</td>
                    <td>{{ $t['duration'] }}</td>
                    <td>{{ number_format(TreatmentService::getPrice($t['item_id'], $t['profile']) ?? 0) }}

                        <select class="form-control" wire:model="prescriptions.{{ $i }}.profile"
                            wire:change="updateProfile({{ $i }}, $event.target.value)" @readonly($doc->status == Status::closed || $t['status'] == Status::completed)>
                            <option value="RETAIL">RETAIL</option>
                            <option value="NHIS">NHIS</option>
                        </select>
                    </td>
                    <td>{{ $t['dispensed'] }}</td>
                    <td>
                        <input type="number" wire:change="compute"
                            wire:model="prescriptions.{{ $i }}.quantity" id="" class="form-control"
                            @readonly($doc->status == Status::closed || $t['status'] == Status::completed) />
                    </td>
                    <td>
                        {{ config('app.currency') }}
                        {{ number_format(($t['quantity'] + $t['dispensed']) * $t['price']) }}
                    </td>
                    <td>{{ $t['balance'] }}</td>
                    <td>
                        <input type="checkbox" @checked($t['status'] != Status::blocked)
                            wire:change="setLineStatus({{ $i }}, $event.target.checked)"
                            @disabled($doc->status == Status::closed || $t['status'] == Status::completed) @readonly($doc->status == Status::closed || $t['status'] == Status::completed) />
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td align="right">NGN {{ number_format($totalAmt) }}</td>
            </tr>
            @if ($doc->status == Status::active)
                <tr>
                    <td colspan="4"></td>
                    <td>
                        <button class="btn bg-blue-400 text-white" wire:click.prevent="saveToBill">Save bill <i
                                class="fa fa-save"></i></button>
                        <button class="btn bg-red-500 text-white" wire:click="dispense">Dispense <i
                                class="fa fa-send"></i></button>
                    </td>
                </tr>
            @endif
        </tfoot>
    </table>

    @if ($doc->status == Status::active)
        <livewire:doctor.add-prescription :visit="$doc->event" :dispatch="true" :display="false"
            @prescription_selected="addLine($event.detail.product)" />
    @endif

    <x-modal id="dispense-confirm">
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th></th>
                    <th>Quantity</th>
                    <th>Balance</th>
                    <th>New balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dispensing as $p)
                    <tr>
                        <td>{{ $p['description'] }}</td>
                        <td>{{ str()->plural($p['unit']) }}</td>
                        <td>{{ $p['quantity'] }}</td>
                        <td>{{ $p['qty_on_hand'] }}</td>
                        <td>{{ $p['left'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>
                            No items being dispensed.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="py-4">
            <button wire:click="confirmDispense" class="btn bg-red-400 text-white">Confirm <i
                    class="fa fa-save"></i></button>
            <button wire:click="confirmDispense(true)" class="btn bg-red-500 text-white">Confirm & Close <i
                    class="fa fa-save"></i></button>
        </div>
    </x-modal>
</div>
