<div>
    <x-overlay-modal id="history" title="History of Present Pregnancy">
    </x-overlay-modal>
    <x-overlay-modal id="physical">
        <livewire:doctor.antenatal.physical-exam :profile="$profile" />
    </x-overlay-modal>
    <x-overlay-modal id="tests">
        <div>
            <p class="font-semibold text-lg">Lab Tests</p>

            @if ($profile->tests->count() > 0)
                <div class="grid gap-y-2">
                    @foreach ($profile->tests as $test)
                        <div class="p-2 bg-green-100">
                            <p class="font-semibold">{{ $test->name }}</p>

                            @forelse ($test->results ?? [] as $r)
                                <div class="grid grid-cols-2 px-2 bg-green-100 justify-between items-center">
                                    <p class="font-semibold">{{ $r->description }}:</p>
                                    <p>{{ $r->result }}</p>
                                </div>
                            @empty
                                <p>No result added yet.</p>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            @else
                <button class="btn btn-sm bg-gray-100" wire:click="initLabTests">No tests requested. Click here to
                    request
                    tests for this
                    patient.</button>
            @endif
        </div>
    </x-overlay-modal>

    <div class="w-1/2 grid gap-y-2">
        <button class="border border-black px-4 py-2 w-full text-xl font-semibold flex justify-between items-center"
            @click="$dispatch('open-history')">
            <span>Obstetric History</span>
            <i class="fa fa-plus"></i>
        </button>
        <button class="border border-black px-4 py-2 w-full text-xl font-semibold flex justify-between items-center"
            @click="$dispatch('open-physical')">
            <span>Physical Examination</span>
            <i class="fa fa-plus"></i>
        </button>
        <button class="border border-black px-4 py-2 w-full text-xl font-semibold flex justify-between items-center"
            @click="$dispatch('open-tests')">
            <span>Lab Tests</span>
            <i class="fa fa-plus"></i>
        </button>
    </div>

    <div class="py-2">
        <div class="flex justify-between items-center">
            <p class="font-semibold text-lg">Visit Logs</p>
            <button class="px-4 py-2 bg-blue-400 text-white" @click="$dispatch('open-anc-log')"><i
                    class="fa fa-plus"></i></button>
        </div>
        <x-anc-log class="max-w-full overflow-x-auto py-4" :profile="$profile" :visit="$visit" />
    </div>

    <button @click="$dispatch('open-modal')" class="btn">Open</button>

    <x-modal id="modal" x-cloak>
        <div class="w-full border p-2">
            <p class="font-semibold text-lg">Presenting Complaints</p>
            <div>
                <form>
                    <div class="form-group">
                        <label>Complaint</label>
                        <textarea name="complaint" class="form-control"></textarea>
                    </div>
                    <div class="form-group">

                    </div>
                </form>
            </div>
        </div>
    </x-modal>
</div>
