<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <div>
        <div class="md:grid lg:grid-cols-3">
            <div class="form-group">
                <p>Gravidity</p>
                <input type="text" class="input w-full" wire:model="bookingForm.gravida" required />
                @error('bookingForm.gravida')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <p>Parity</p>
                <input type="text" class="input w-full" wire:model="bookingForm.parity" required />
            </div>
            <div class="form-group">
                <p>Presentation</p>
                <input type="text" class="input w-full" wire:model="bookingForm.presentation" required />
            </div>
            <div class="form-group">
                <p>Lie</p>
                <input type="text" class="input w-full" wire:model="bookingForm.lie" required />
            </div>
            <div class="form-group">
                <p>Height of Fundus</p>
                <input type="text" class="input w-full" wire:model="bookingForm.fundal_height" required />
            </div>
            <div class="form-group">
                <p>Fetal Heart Rate</p>
                <input type="text" class="input w-full" wire:model="bookingForm.fetal_heart_rate" required />
            </div>
            <div class="form-group">
                <p>Relationship of Presenting Part to Pelvis</p>
                <input type="text" class="input w-full" wire:model="bookingForm.presentation_relationship"
                    required />
            </div>
        </div>
    </div>

    <div class="py-2"></div>

    <p class="text-xl bold">Tests</p>
    <livewire:dynamic-product-search :departmentId=5 @selected="addTest($event.detail)" />

    <div class="p-1 bg-gray-100">
        @include('doctors.components.test-results', ['tests' => $profile->tests])
    </div>

    <div class="py-2"></div>

    <p class="text-xl bold">Scans</p>
    <livewire:dynamic-product-search :departmentId=7 @selected="addScan($event.detail)" />

    <div class="p-1 bg-gray-100">
        {{-- @include('doctors.components.test-results', ['tests' => $profile->tests]) --}}
        <ul class="sp-list">
            @forelse ($visit->imagings as $scan)
                <li>
                    <p>{{ $scan->name }}</p>
                    <p class="text-red hover:underline cursor-pointer" wire:click="removeScan({{ $scan->id }})">
                        Cancel</p>
                </li>
            @empty
                <li>No scan requested yet.</li>
            @endforelse
        </ul>
    </div>

    <div class="py-2"></div>
    <p class="text-xl bold">Treatments</p>
    <livewire:dynamic-product-search :departmentId=4 @selected="addPrescription($event.detail.id)" />
    <div class="py-1"></div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Dosage</th>
                <th>Duration</th>
                <th>Frequency</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if ($selected_treatment)
                <tr>
                    <td>{{ $selected_treatment->name }}</td>
                    <td>
                        <input type="text" wire:model='treatment_dosage' />
                        @error('treatment_dosage')
                            <br />
                            <small class="text-red">{{ $message }}</small>
                        @enderror
                    </td>
                    <td>
                        <input type="text" wire:model='treatment_duration' />
                        @error('treatment_duration')
                            <br />
                            <small class="text-red">{{ $message }}</small>
                        @enderror
                    </td>
                    <td>
                        <select name="frequency" wire:model="treatment_frequency">
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
                        @error('treatment_frequency')
                            <br />
                            <small class="text-red">{{ $message }}</small>
                        @enderror
                    </td>
                    <td>
                        <button class="btn btn-sm bg-blue-500" wire:click="savePrescription">&check;</button>
                        <button class="btn btn-sm bg-red-500" wire:click="cancelPrescription">&times;</button>
                    </td>
                </tr>
            @endif

            @forelse ($visit->prescriptions as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->dosage }}</td>
                    <td>{{ $t->duration }}</td>
                    <td>{{ $t->frequency }}</td>
                    <td>
                        <button class="btn-sm btn bg-red-400"
                            wire:click="removePrescription({{ $t->id }})">&times;</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No treatment</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="py-3"></div>

    <button wire:click="submitBooking" class="btn btn-blue">Submit</button>
    <div class="py-5"></div>
</div>
