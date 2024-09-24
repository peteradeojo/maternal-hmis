<div>
    <div class="card relative">
        <p class="bold header card-header">Add Prescription</p>

        @if ($visit->diagnoses->count() == 0)
            <p class="p-3">No diagnosis has been made during this visit. Please add a diagnosis.</p>
        @else
            <div class="p-1">
                <livewire:product-search departmentId='4' @selected="addPrescription($event.detail.id)" />
                <div class="py-2"></div>
                <table id="drugs-table" class="table">
                    <thead>
                        <tr>
                            <th>Prescription</th>
                            <th>Dosage</th>
                            <th>Duration</th>
                            <th>Frequency</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($selections)
                            <tr>
                                <form wire:submit="saveRequest" wire:key="{{ $selections->id }}">
                                    <td>{{ $selections->name }}</td>
                                    <td>
                                        <input type="text" wire:model="requestForm.dosage" name="dosage"
                                            value="1" />
                                        <div>
                                            @error('requestForm.dosage')
                                                <span class="error text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" wire:model="requestForm.duration" name="duration"
                                            value="1" />
                                        <div>
                                            @error('requestForm.duration')
                                                <span class="error text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <select name="frequency" wire:model="requestForm.frequency">
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
                                    <td class="flex gap-x-3 items-center">
                                        <button type="submit" wire:click="saveRequest"
                                            class="btn btn-sm bg-blue-200 hover:bg-blue-500">&check;</button>
                                        <button type="button" class="btn btn-sm bg-red-200 hover:bg-red-500"
                                            wire:click.prevent="cancel">&times;</button>
                                    </td>
                                </form>
                            </tr>
                        @endif

                        @if ($updating)
                            <tr>
                                <form wire:submit="saveRequest" wire:key="{{ $updating->id }}">
                                    <td>{{ $updating->name }}</td>
                                    <td>
                                        <input type="text" wire:model="requestForm.dosage" name="dosage"
                                            value="1" />
                                        <div>
                                            @error('requestForm.dosage')
                                                <span class="error text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" wire:model="requestForm.duration" name="duration"
                                            value="1" />
                                        <div>
                                            @error('requestForm.duration')
                                                <span class="error text-xs text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </td>
                                    <td>
                                        <select name="frequency" wire:model="requestForm.frequency">
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
                                    <td class="flex gap-x-3 items-center">
                                        <button type="submit" wire:click="saveUpdate({{ $updating->id }})"
                                            class="btn btn-sm bg-blue-200 hover:bg-blue-500">&check;</button>
                                        <button type="button" class="btn btn-sm bg-red-200 hover:bg-red-500"
                                            wire:click.prevent="cancelEdit">&times;</button>
                                    </td>
                                </form>
                            </tr>
                        @endif

                        @foreach ($visit->prescriptions as $prescription)
                            <tr>
                                <td>{{ $prescription->name }}</td>
                                <td>{{ $prescription->dosage }}</td>
                                <td>{{ $prescription->duration }}</td>
                                <td>{{ $prescription->frequency }}</td>
                                <td>
                                    <button class="btn btn-sm bg-green-300 hover:bg-green-600 hover:text-white"
                                        wire:click="edit({{ $prescription->id }})">Edit</button>
                                    <button class="btn btn-sm bg-red-300 hover:text-white hover:bg-red-600"
                                        wire:click="deleteRequestItem({{ $prescription->id }})">&times;</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
