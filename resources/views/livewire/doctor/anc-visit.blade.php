<div class="px-2">
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="py-2">
        <div class="flex gap-x-4 items-center">
            <p class="text-xl bold">Notes</p>
            <button class="modal-trigger btn btn-sm btn-blue" data-target="#anc-visit-notes-modal">
                Add Note
            </button>
        </div>

        <div class="">
            @forelse ($visit->notes as $note)
                <div class="bg-gray-100 p-2">
                    <p>{{ $note->note }}</p>
                    <p class="text-xs">Consultant: {{ $note->consultant->name }}</p>
                    <p class="text-red-700 text-xs">{{ $note->created_at }}</p>
                </div>
                <div class="py-3"></div>
            @empty
                <p class="py-1">No notes added yet. Add a note to view here</p>
            @endforelse
        </div>
    </div>

    <form action="{{ route('doctor.treat-anc', ['visit' => $visit]) }}" method="post" id="follow-up-form"
        class="grid grid-cols-3 gap-x-2">
        @csrf
        <div class="form-group">
            <label>Height of Fundus</label>
            <input type="text" name="fundal_height" class="form-control" />
        </div>
        <div class="form-group">
            <label>Presentation</label>
            <input type="text" name="presentation" class="form-control" />
        </div>
        <div class="form-group">
            <label>Lie</label>
            <input type="text" name="lie" class="form-control" />
        </div>
        <div class="form-group">
            <label>Relationship of presenting part to pelvis</label>
            <input type="text" name="presentation_relationship" class="form-control" />
        </div>
        <div class="form-group">
            <label>Foetal Heart Rate</label>
            <input type="text" name="fetal_heart_rate" class="form-control" />
        </div>

        <div class="py-2"></div>
        <livewire:doctor.add-presciption :visit="$visit" />

        <div class="py-2"></div>

        <div class="col-span-full">
            <div class="flex justify-between pb-2 items-center">
                <p class="text-xl bold">Tests</p>
                <button type="button" class="modal-trigger btn btn-sm btn-blue"
                    data-target="#anc-visit-tests-modal">Add
                    Investigation</button>
            </div>
            @include('doctors.components.test-results', ['tests' => $visit->visit->tests])
            <div class="flex justify-between pt-2 items-center">
                <p class="text-xl bold">Scans</p>
            </div>
        @empty($visit->radios)
            <p>No scan requested.</p>
        @else
            @foreach ($visit->radios as $scan)
                <div class="p-2 bg-gray-200">
                    <p><b>{{ $scan->name }}</b></p>
                    <p>{{ $scan->comment ?? 'No comment' }}</p>
                    <p>{{ $scan->path ? '' : 'No result provided' }}</p>
                    @unless ($scan->comment || $scan->path)
                        <span class="text-xs cursor-pointer text-red-600 hover:underline"
                            wire:click="removeScan({{ $scan->id }})">Cancel Request</span>
                    @endunless
                </div>
            @endforeach
        @endempty

        <div class="form-group">
            <label>Next Visit Date:</label>
            <input type="date" wire:model="return_visit" class="input">
        </div>
    </div>
</form>
<button class="btn btn-blue" form="follow-up-form">Submit</button>

<div class="modal hide" id="anc-visit-tests-modal">
    <div class="content p-3 bg-white">
        <p class="bold text-xl">Add Investigation</p>

        <div id="anc-visit-tests-2" data-tablist="#anc-visit-tests">
            @include('components.tabs', ['options' => ['Test', 'Investigation']])

            <div id="anc-visit-tests">
                <div class="tab">
                    <div class="py-1 w-1/2">
                        <p>Search here to add a test</p>
                        <livewire:dynamic-product-search @selected='addTest($event.detail.id)' departmentId='5' />
                    </div>
                </div>
                <div class="tab">
                    <div class="py-1 w-1/2">
                        <p>Search here to add a scan</p>
                        <livewire:dynamic-product-search @selected='addScan($event.detail.id)' departmentId='7' />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal hide" id="anc-visit-notes-modal">
    <div class="content p-3 bg-white">
        <p class="text-xl bold">Add Note</p>
        <form wire:submit.prevent="addNote">
            <div class="form-group">
                <label for="">Add Note</label>
                <textarea wire:model.debounce.500ms="note" id="" class="form-control" rows="5" required="required"
                    @click.stop></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-blue">Add Note</button>
            </div>
        </form>
    </div>
</div>
</div>
