<div>
    <div class="relative">
        <p class="bold">{{ $title ?? 'Add Prescription' }}</p>

        <div x-data>
            <livewire:inventory-product-search @handle-select="addPrescription($event.detail.product.item)" />
        </div>

        @if ($display || $selections || $updating)
            <table id="drugs-table" class="table max-w-full">
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
                    @if (!empty($selections))
                        <tr>
                            <form wire:submit.prevent="saveRequest" wire:keyup.escape.stop="cancel"
                                wire:key="{{ $selections?->id ?? $selections->name }}">
                                <td>{{ $selections->name }} ({{ $selections->weight }} {{ $selections->si_unit }})</td>
                                <td>
                                    <input type="text" autofocus name="dosage" wire:model="requestForm.dosage" wire:change="getCount"
                                        wire:keyup.enter.prevent="saveRequest" name="dosage" value="1" required />
                                    {{-- <div>
                                        @error('requestForm.dosage')
                                            <span class="error text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div> --}}
                                </td>
                                <td>
                                    <select name="frequency" wire:change="getCount" wire:model="requestForm.frequency" required>
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
                                    <input type="number" wire:change="getCount" wire:model="requestForm.duration" name="duration"
                                        wire:keyup.enter.prevent="saveRequest" value="1" required />
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
                                        wire:click.prevent="cancel"><i class="fa fa-delete"></i></button>
                                </td>
                            </form>
                        </tr>
                        <tr>
                            <td>{{ $count !== 0 ? "Units to dispense: $count" : "Unable to compute quantity for prescription. Please check dosage and frequency." }}</td>
                            <td>
                                @if ($count && $count > $selections->balance)
                                <p class="text-red-500 text-sm">
                                    <i class="fa fa-exclamation"></i>
                                    Insufficient inventory
                                </p>
                                @endif
                            </td>
                            <td @class(['text-red-500 font-bold' => isset($count) && $count > $selections->balance])>
                                Available: {{ $selections->balance }} {{str()->plural($selections->base_unit ?? 'unit', $selections->balance)}}
                            </td>
                        </tr>
                    @endif

                    {{-- @if ($updating)
                        <tr>
                            <form wire:submit.prevent="saveRequest" wire:key="{{ $updating->name }}">
                                <td>{{ $updating->name }}</td>
                                <td>
                                    <input type="text" wire:model.live="requestForm.dosage" name="dosage"
                                        value="1" />
                                    <div>
                                        @error('requestForm.dosage')
                                            <span class="error text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </td>
                                <td>
                                    <select name="frequency" wire:model.live="requestForm.frequency">
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
                                    <input type="number" wire:model="requestForm.duration" name="duration"
                                        value="1" />
                                    <div>
                                        @error('requestForm.duration')
                                            <span class="error text-xs text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </td>
                                <td class="flex gap-x-3 items-center">
                                    <button type="button" wire:click="saveUpdate({{ $updating->id }})"
                                        class="btn btn-sm bg-blue-200 hover:bg-blue-500">&check;</button>
                                    <button type="button" class="btn btn-sm bg-red-200 hover:bg-red-500"
                                        wire:click.prevent="cancelEdit">&times;</button>
                                </td>
                            </form>
                        </tr>
                    @endif --}}

                    @if ($display)
                        @forelse ($visit->prescription?->lines ?? [] as $prescription)
                            <tr>
                                <td>{{ $prescription->item?->name ?? $prescription->description }}</td>
                                <td>{{ $prescription->dosage }}</td>
                                <td>{{ $prescription->frequency }}</td>
                                <td>{{ $prescription->duration }}</td>
                                <td>
                                    {{-- <button type="button"
                                        class="btn btn-sm bg-green-300 hover:bg-green-600 hover:text-white"
                                        wire:click="edit({{ $prescription->id }})">Edit</button> --}}
                                    <button type="button"
                                        class="btn btn-sm bg-red-300 hover:text-white hover:bg-red-600"
                                        wire:click="deleteRequestItem({{ $prescription->id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No treatment added.</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        @endif
    </div>
</div>
