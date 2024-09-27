<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}

    <p class="text-xl bold">Vitals</p>
    <p>Blood pressure: {{ $visit->vitals[0]?->blood_pressure }} mmHg</p>
    <p>Weight: {{ $visit->vitals[0]?->weight }} kg</p>
    <p>Temperature: {{ $visit->vitals[0]?->temperature }} &deg;C</p>
    <p>Pulse: {{ $visit->vitals[0]?->pulse }} bpm</p>
    <p>Respiration: {{ $visit->vitals[0]?->respiration }} c/m</p>

    <div class="py-2"></div>
    <p class="text-xl bold">Notes</p>
    <form wire:submit.prevent="addNote">
        <div class="form-group">
            <label for="">Add Note</label>
            <textarea wire:model.live.debounce.500ms="note" id="" class="form-control" rows="5" required="required"></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-blue">Add Note</button>
        </div>
    </form>

    <div class="py1"></div>
    @foreach ($visit->notes as $note)
        <div class="bg-gray-100 p-2">
            <p>{{ $note->note }}</p>
            <p class="text-xs">Consultant: {{ $note->consultant->name }}</p>
            <p class="text-red-700 text-xs">{{ $note->created_at }}</p>
        </div>
    @endforeach

    <div class="py-2"></div>
    <livewire:doctor.add-presciption :visit="$visit" />

    <div class="py-2"></div>

    <p class="text-xl bold">Tests</p>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Result</th>
                <th>Unit</th>
                <th>Ref. range</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visit->tests as $test)
                <tr wire:key="test:{{ $test->name }}">
                    <td colspan="3"><b>{{ $test->name }}</b></td>
                    <td class="text-xs">
                        @if ($test->results == null && $test->name != 'ROUTINE ANTENATAL TESTS')
                            <button class="btn text-red-500 underline"
                                wire:click="removeTest({{ $test->id }})">Cancel
                                Request</button>
                        @endif
                    </td>
                </tr>
                @forelse ($test->results ?? [] as $result)
                    <tr wire:key="test:{{ $test->name }}">
                        <td>{{ $result->description }}</td>
                        <td>{{ $result->result }}</td>
                        <td>{{ $result->unit }}</td>
                        <td>{{ $result->reference_range }}</td>
                    </tr>
                @empty
                    <tr wire:key="test:{{ $test->name }}">
                        <td colspan="4">No result provided.</td>
                    </tr>
                @endforelse
            @endforeach
        </tbody>
    </table>

    <div class="py-1 w-1/2">
        <p>Search here to add a test</p>
        <livewire:product-search @selected='addTest($event.detail.id)' departmentId='5' />
    </div>

    <div class="py-2"></div>

    <p class="text-xl bold">Scans</p>
    <div class="py-1 w-1/2">
        <p>Search here to add a scan</p>
        <livewire:product-search @selected='addScan($event.detail.id)' departmentId='7' />
    </div>

    @foreach ($visit->radios as $scan)
        <div class="p-2 bg-gray-200">
            <p><b>{{ $scan->name }}</b></p>
            <p>{{ $scan->comment ?? 'No comment' }}</p>
            <p>{{ $scan->path ? '' : 'No result provided' }}</p>
        </div>
    @endforeach


    <div class="py-3"></div>
    <form action="{{ route('doctor.treat-anc', ['visit' => $visit]) }}" method="post">
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
        <div class="form-group">
            <label>Next Visit Date:</label>
            <input type="date" wire:model="return_visit" class="input">
        </div>
        <button class="btn btn-blue">Submit</button>
    </form>
</div>
