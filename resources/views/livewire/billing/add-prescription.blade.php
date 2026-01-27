<div>
    {{-- In work, do what you enjoy. --}}
    {{-- <livewire:dynamic-product-search :departmentId="4" :category="'PHARMACY'" @selected.stop="addDrug($event.detail)"
        @selected_temp.stop="addDrug($event.detail)" /> --}}
    <livewire:inventory-product-search @handle-select.stop="addDrug($event.detail.product.item)" />

    @if ($selection)
        <table class="table">
            <thead>
                <tr>
                    <th>Prescription</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration (in days)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <form wire:submit.prevent="saveRequest" wire:keyup.escape.stop="cancel"
                        wire:key="{{ $selection['id'] ?? $selection['name'] }}">
                        <td>{{ $selection['name'] }}</td>
                        <td>
                            <input type="text" wire:change="getCount" autofocus wire:model="selection.dosage" name="dosage" value="1" />
                            <div>
                                @error('requestForm.dosage')
                                    <span class="error text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </td>
                        <td>
                            <select name="frequency" wire:change="getCount" wire:model="selection.frequency">
                                <option disabled="disabled" selected>Select Frequency</option>
                                <option value="stat">stat</option>
                                <option value="od">once daily</option>
                                <option value="bd">bd</option>
                                <option value="tds">tds</option>
                                <option value="qds">qds</option>
                                <option value="hourly">hourly</option>
                                <option value="weekly">weekly</option>
                                <option value="night">at night</option>
                                <option value="immediately">Immediately</option>
                                <option value="needed">as needed</option>
                                <option value="other">Others</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" wire:change="getCount" wire:model="selection.duration" name="duration" value="1"
                                autocomplete="off" />
                            <div>
                                @error('requestForm.duration')
                                    <span class="error text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </td>
                        <td class="flex gap-x-3 items-center">
                            <button type="button" wire:click.prevent="saveRequest"
                                class="btn btn-sm bg-blue-200 hover:bg-blue-500">&check;</button>
                            <button type="button" class="btn btn-sm bg-red-200 hover:bg-red-500"
                                wire:click.prevent="cancel">&times;</button>
                        </td>
                    </form>
                </tr>
                <tr>
                    <td>
                        {{ $count > 0 ? "Units dispensed: " . $count : "Error" }}
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endif
</div>
