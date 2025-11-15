<div class="px-2">
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="py-2">
        <x-anc-log :visit="$visit" :profile="$visit->profile" />
    </div>

    <div class="py-2 grid grid-cols-3 gap-x-2">
        {{-- Presentation --}}
        <div>
            <div class="flex-center gap-x-4 pb-2">
                <p class="text-lg font-semibold">Presentation</p>
                <button @click="$dispatch('open-visit-complaints')" class="px-1 py-0.5 btn-blue text-white"><i
                        class="fa fa-plus"></i></button>
            </div>

            <table class="table bordered">
                <tr>
                    <th>Presentation</th>
                    <th>Duration</th>
                    <td></td>
                </tr>
                @forelse ($visit->complaints ?? [] as $complaint)
                    <tr>
                        <td>{{ $complaint->name }}</td>
                        <td>{{ $complaint->duration }}</td>
                        <td>
                            <button wire:click="removeComplaint({{ $complaint->id }})"
                                class="text-red-500 text-sm">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            No complaints recorded.
                        </td>
                    </tr>
                @endforelse
            </table>
        </div>
        {{-- Notes --}}
        <div>
            <div class="flex-center gap-x-4 pb-2">
                <p class="text-xl bold">Notes</p>
                <button class="px-1 py-0.5 btn-blue" data-target="#anc-visit-notes-modal"
                    @click="$dispatch('open-anc-visit-notes-modal')">
                    <i class="fa fa-notes-medical"></i>
                </button>
            </div>

            <div>
                @forelse ($visit->notes as $note)
                    <div class="bg-gray-100 p-2">
                        <p>{{ $note->note }}</p>
                        <p class="text-xs">Consultant: {{ $note->consultant->name }}</p>

                        <div class="flex-center justify-between text-xs">
                            <p class="text-red-700">{{ $note->created_at }}</p>
                            <button wire:click="removeNote({{ $note->id }})" class="text-red-700">Delete</button>
                        </div>
                    </div>
                @empty
                    <p class="py-1">No notes added yet. Add a note to view here</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-x-4 items-start">
        <div class="p-2 border border-black">
            <div class="flex-center hover:bg-gray-50 gap-x-4 p-2" @click="$dispatch('open-anc-visit-tests-modal')">
                <p class="text-lg font-bold">Tests</p>
                <i class="fa fa-plus"></i>
            </div>

            <div class="p-2">
                @include('doctors.components.test-results', ['tests' => $visit->tests])
            </div>
        </div>
        <div class="p-2 border border-black" x-data="{ open: true }">
            <div class="flex-center hover:bg-gray-50 gap-x-4 p-2" @click="$dispatch('open-anc-treatments')">
                <p class="text-lg font-bold">Treatments</p>
                <i class="fa fa-plus"></i>
            </div>

            <div class="p-2">
                <table class="table bordered">
                    <thead>
                        <tr>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visit->treatments as $t)
                            <tr>
                                <td>{{ $t }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td>No treatments</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="p-2">
        <button @click="$dispatch('open-admit')" class="btn bg-red-500 text-white">Start Admission</button>
    </div>

    <x-modal id="anc-treatments">
        <livewire:doctor.add-presciption :visit="$visit" @treatments_updated="addedTreatment" />
    </x-modal>
    <x-modal id="anc-visit-tests-modal">
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
    </x-modal>
    <x-modal id="anc-visit-notes-modal">
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
    </x-modal>
    <x-modal id="visit-complaints">
        <p class="font-semibold">Complaints</p>

        <form wire:submit.prevent="takeComplaint">
            <div class="form-group">
                <label>Complaint</label>
                {{-- <input wire:model="complaint" type="text" name="complaint" class="form-control" required /> --}}
                <x-input-text required wire:model="complaint" class="form-control" name="complaint" />
            </div>
            <div class="form-group">
                <label>Duration</label>
                <x-input-text wire:model="complaint_duration" name="complaint_duration" class="form-control" />
            </div>
            <div class="form-group">
                <button class="btn bg-green-500 text-white">Submit <i class="fa fa-save"></i></button>
            </div>
        </form>
    </x-modal>
</div>

@script
    <script>
        $(document).ready(function() {
            initTab(document.querySelector('#anc-visit-tests-2'));;
        })
    </script>
@endscript
