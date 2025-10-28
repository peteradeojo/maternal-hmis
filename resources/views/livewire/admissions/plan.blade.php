<div>
    {{-- Stop trying to control. --}}


    <div class="py-2 px-2 bg-gray-200 grid gap-y-4">
        <p class="bold">Indication for Admission</p>
        <input type="text" name="indication" id="" placeholder="Indication for Admission" wire:model="indication"
            value="{{ $admission?->plan->indication }}">

        <livewire:doctor.add-presciption :visit="$visit" :dispatch="true"
            @prescription_selected="addPrescription($event.detail.product)" :title="'Add Treatment Plan'" />

        <p class="text-lg bold">Added Plans</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Dosage</th>
                    <th>Duration</th>
                    <th>Frequency</th>
                    <th></th>
                </tr>
            </thead>
            @forelse ($this->plans as $i => $plan)
                <tr>
                    <td>{{ $plan['name'] }}</td>
                    <td>{{ $plan['dosage'] }}</td>
                    <td>{{ $plan['duration'] }}</td>
                    <td>{{ $plan['frequency'] }}</td>
                    <td>
                        <button wire:click="removePlanItem({{ $i }})"
                            class="btn btn-sm btn-red text-white">&times;</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">You haven't added any plans</td>
                </tr>
            @endforelse
        </table>

        <div class="grid grid-cols-2 gap-x-4">
            <div>
                <p class="bold">Tests</p>
                <livewire:dynamic-product-search :departmentId="5" @selected="addTest($event.detail)" />

                <ul class="list-disc list-inside pt-1">
                    {{-- @dump($tests) --}}
                    @foreach ($tests as $selectedTest)
                        <li class="flex justify-between items-center">
                            @unless ($selectedTest->results)
                                <span>{{ $selectedTest['name'] }}</span>
                                <a href="#" wire.prevent wire:click="removeTest({{ $selectedTest['id'] }})"
                                    class="btn btn-sm btn-red">Remove</a>
                            @else
                                <a href="{{ route('doctor.show-admission', $admission) }}" target="_blank"
                                    class="link">{{ $selectedTest['name'] }} - View Result</a>
                            @endunless
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="bold">Investigations</p>
                <livewire:dynamic-product-search :departmentId="7" @selected="addInvestigation($event.detail)" />

                <ul class="list-disc list-inside pt-1">
                    @foreach ($investigations as $i)
                        <li class="flex justify-between items-center">
                            <span>{{ $i['name'] }}</span>
                            <a href="#" wire.prevent wire:click="removeInvestigation({{ $i['id'] }})"
                                class="btn btn-sm btn-red">Remove</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- <p class="text-xl bold">Surgery</p>
        <input type="text" name="procedure" id="" placeholder="Procedure to be undergone"
            wire:model="surgery"> --}}

        {{-- <p class="bold">Operation Note</p>
        <textarea rows="3" class="form-control" wire:model="operationNote"></textarea> --}}

        <div class="py-1"></div>
        <p>Admission Note / More </p>
        <textarea rows="3" wire:model="admissionNote" class="form-control"></textarea>

        <button class="btn btn-secondary w-1/3" wire:click="savePlan">Submit</button>
        <div class="pb-4"></div>
    </div>
</div>
