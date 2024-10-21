<div>
    {{-- Stop trying to control. --}}


    <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
        <p><b>Treatment Plan</b></p>
        <ul class="list-disc px-3 text-sm">
            @forelse ($visit->prescriptions as $pres)
                <li>{{ $pres }}</li>
            @empty
                <li>No presciption</li>
            @endforelse
        </ul>

        <div class="py-4"></div>

        <livewire:doctor.add-presciption :visit="$visit" :dispatch="true"
            @prescription_selected="addPrescription($event.detail.product)" :title="'Add Treatment Plan'" />


        <div class="pt-5"></div>
        <p>Added Plans</p>
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

        <div class="py-1"></div>
        <p>Admission Note / Special cosideration / More </p>
        <textarea rows="5" class="form-control"></textarea>

        <button class="btn bg-blue-600 text-white" wire:click="savePlan">Submit</button>
    </div>
</div>
