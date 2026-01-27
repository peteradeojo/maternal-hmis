<div>
    {{-- Stop trying to control. --}}
    <div class="py-2 px-2 bg-gray-200 grid gap-y-4">
        <p class="bold">Indication for Admission</p>
        <x-input-text name="indication" wire:model="indication" placeholder="Indication for Admission"
            class="form-control" />

        <livewire:doctor.add-prescription :visit="$admission->plan" :dispatch="true"
            @prescription_selected="addPrescription($event.detail.product)" :display="true" />

        <div class="grid grid-cols-2 gap-x-4">
            <div>
                <p class="bold">Tests</p>
                {{-- <livewire:dynamic-product-search :departmentId="5" @selected="addTest($event.detail)" /> --}}
                <livewire:doctor.add-test :event="$admission->plan" @tests-added="$refresh" />

                {{-- <ul class="list-disc list-inside pt-1">
                    @foreach ($tests as $selectedTest)
                        <li class="flex justify-between items-center">
                            @unless ($selectedTest->results)
                                <span>{{ $selectedTest['name'] }}</span>
                                <button wire.prevent wire:click="removeTest({{ $selectedTest['id'] }})"
                                    class="btn btn-sm btn-red">Remove</button>
                            @else
                                <a href="{{ route('doctor.show-admission', $admission) }}" target="_blank"
                                    class="link">{{ $selectedTest['name'] }} - View Result</a>
                            @endunless
                        </li>
                    @endforeach
                </ul> --}}

                @include('doctors.components.test-results', [
                    'tests' => $admission->plan->valid_tests->merge($admission->visit->valid_tests)->merge($admission->visit->visit->valid_tests)->merge($admission->valid_tests),
                    'cancellable' => false,
                ])
            </div>
            <div>
                <p class="bold">Investigations</p>
                <livewire:doctor.add-scan :event="$admission->plan" />

                <ul class="list-disc p-1">
                    @foreach ($investigations as $inv)
                        <li class="flex-center justify-between">
                            <span>{{ $inv->name }}</span>
                            <button wire.prevent wire:click="removeInvestigation({{ $inv->id }})"
                                class="btn btn-sm btn-red">Remove</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <p>Admission Note / More</p>
        <x-input-textarea wire:model="admissionNote" class="form-control" row="3" name="admissionNote" />

        <button class="btn btn-secondary w-1/3" wire:click="savePlan">Submit</button>
        <div class="pb-4"></div>
    </div>
</div>
