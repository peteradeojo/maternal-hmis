<div wire:poll.5000ms>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <div class="py-2">
        @for ($i = 0; $i < $visit->patient->visits->count(); $i++)
            @php
                $v = $visit->patient->visits[$i];
            @endphp
            <div class="border-2 border-red-300 p-1">
                Date: {{ $v->created_at->format('Y-m-d h:i A') }}

                <div class="pt-1"></div>
                <p><b>Notes</b></p>
                @forelse ($v->notes->slice(0,10) as $note)
                    <div class="py-1 px-1 bg-gray-100 grid gap-y-1">
                        <p>{{ $note->note }}</p>
                        <div class="text-xs">
                            <p class=" text-red">Consultant: {{ $note->consultant->name }}</p>
                            <p>{{ $note->created_at->format('Y-m-d h:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <p>No notes added</p>
                @endforelse

                <div class="py-2"></div>

                <p><b>Diagnoses</b></p>
                @forelse ($v->diagnoses as $dia)
                    <div class="py-1 px-1 bg-gray-100 grid gap-y-1">
                        <p class="text-sm"><b>Diagnosed: </b>{{ $dia->diagnoses }}</p>
                        <div class="text-xs">
                            <p class="text-red">Consultant: {{ $dia->made_by->name }}</p>
                            <p>{{ $dia->created_at->format('Y-m-d h:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="p-1 bg-gray-100">No diagnosis has been made during this visit.</p>
                @endforelse

                <div class="py-2"></div>

                <p><b>Tests</b></p>
                @forelse ($v->tests as $rtest)
                    <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
                        <p class="text-sm"><b>{{ $rtest->name }}</b></p>
                        <div class="text-xs">
                            {{-- <p class=" text-red">Result: {{ $rtest->results ?? "Not provided" }}</p> --}}
                            {{-- <p>{{ $rtest->created_at->format('Y-m-d h:i A') }}</p> --}}
                            @if (count($rtest->results ?? []) > 0)
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Result</th>
                                            <th>Unit</th>
                                            <th>Ref. Range</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rtest->results ?? [] as $r)
                                            {{-- <p class="py-1"><b>{{ $r->description }}: </b> {{ $r->result }}</p> --}}
                                            <td>{{ $r->description }}</td>
                                            <td>{{ $r->result }}</td>
                                            <td>{{ $r->unit }}</td>
                                            <td>{{ $r->reference_range }}</td>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No result yet</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p>No tests requested for this visit</p>
                @endforelse

                @if ($visit->imagings?->count() > 0)
                    <div class="py-2"></div>
                    <p><b>Scans</b></p>
                    @foreach ($visit->imagings as $img)
                        <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
                            <p><b class="text-sm">{{ $img->name }}</b>:
                                <br>
                                <small>Result: {{ $img->path ?? 'No result' }}</small>
                                <br>
                                <small>Comment: {{ $img->comment ?? 'No comment' }}</small>
                            </p>
                        </div>
                    @endforeach
                @endif
            </div>
        @endfor
    </div>

    <div class="modal hide"></div>
</div>
