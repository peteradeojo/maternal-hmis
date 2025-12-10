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

                        <select class="form-control"
                            wire:change="updateProfile({{ $i }}, $event.target.value)">
                            <option value="RETAIL">RETAIL</option>
                            <option value="NHIS">NHIS</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" wire:change="compute" wire:model="prescriptions.{{ $i }}.quantity"
                            id="" class="form-control" />
                    </td>
                    <td>
                        {{ config('app.currency') }}
                        {{ number_format($t['quantity'] * TreatmentService::getPrice($t['item_id'], $t['profile']) ?? 0) }}
                    </td>
                    <td>{{ $t['balance'] }}</td>
                    <td>
                        <input type="checkbox" @checked($t['status'] != Status::cancelled)
                            wire:change="setLineStatus({{ $i }}, $event.target.checked)" />
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td align="right">NGN {{ number_format($totalAmt) }}</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td>
                    <button class="btn bg-blue-400 text-white" wire:click.prevent="saveToBill">Save bill <i
                            class="fa fa-save"></i></button>

                    @if ($bill && ($is_admission or $bill->status == Status::PAID->value))
                        <button class="btn bg-red-500 text-white">Dispense <i class="fa fa-send"></i></button>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>


    {{-- @dump($prescriptions[0]) --}}

    <livewire:doctor.add-presciption :visit="$doc->event" :dispatch="true" :display="false"
        @prescription_selected="addLine($event.detail.product)" />
</div>
