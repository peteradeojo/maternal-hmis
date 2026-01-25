<div wire:poll.3000ms>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    @foreach ($visit->patient->visits->slice(0, 5) as $i => $visit)
        <div class="py-2">
            <div class="border-2 border-red-300 p-1">
                <p>Date: {{ $visit->created_at->format('Y-m-d h:i A') }}</p>

                <div class="pt-1"></div>
                <p><b>History</b></p>
                <table class="table bg-gray-100 p-2">
                    <tr>
                        <th>Presentation</th>
                        <th>Duration</th>
                        <th></th>
                    </tr>

                    @forelse ($visit->histories->merge($visit->visit->histories) as $h)
                        <tr>
                            <td>{{ $h->presentation }}</td>
                            <td>{{ $h->duration }}</td>
                            <td class="w-1/12 text-center"><button wire:click="deleteHistory({{ $h->id }})"
                                    class="btn btn-sm btn-red">&times;</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No history documented</td>
                        </tr>
                    @endforelse
                </table>

                <div class="py-2">
                    <p><b>Notes</b></p>
                    @forelse ($visit->notes as $note)
                        <x-editable-note :note="$note" />
                    @empty
                        <p>No notes added</p>
                    @endforelse
                </div>

                <div class="pb-2">
                    <p><b>Examinations</b></p>

                    <div class="px-1 bg-gray-100">
                        @unless ($visit->examination || $visit->visit->examination)
                            <p>No examination conducted.</p>
                        @else
                            @php
                                $exam = $visit->examination ?? $visit->visit->examination;
                            @endphp
                            <div>
                                <p><b>General</b></p>
                                <p>@nl2br($exam->general)</p>
                            </div>

                            @foreach ($exam->specifics as $k => $sp)
                                <div>
                                    <p><b>@nl2br(unslug($k, fn($str) => ucwords(str_replace('digital', '/', $str))))</b></p>
                                    <p>{{ $sp }}</p>
                                </div>
                            @endforeach
                        @endunless
                    </div>
                </div>

                <div class="py-2">
                    <p><b>Tests</b></p>
                    @include('doctors.components.test-results', [
                        'tests' => $visit->tests,
                        'cancellable' => true,
                    ])
                </div>

                @if ($visit->imagings?->count() > 0)
                    <div class="py-2">
                        <p><b>Scans</b></p>
                        @foreach ($visit->imagings as $img)
                            @unless (empty($img->results))
                                <x-overlay-modal id="scan-{{ $img->id }}" title="{{ $img->name }}">
                                    <p>{{ $img->getResults() }}</p>
                                </x-overlay-modal>
                            @endunless

                            <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
                                <p><b>{{ $img->name }}</b>:
                                    <br>
                                    <small>Result: @empty($img->results)
                                            No result
                                        @else
                                            <a href="#" data-target="#scan-result-modal"
                                                data-scanid="{{ $img->id }}"
                                                @click.prevent="$dispatch('open-scan-{{ $img->id }}')"
                                                class="scan-result text-blue-500 underline">View Result</a>
                                        @endempty
                                    </small>
                                    <br>
                                    <small>Comment: {{ $img->comment ?? 'No comment' }}</small>
                                </p>

                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="pb-2">
                    <p><b>Diagnoses</b></p>
                    @forelse ($visit->diagnoses as $dia)
                        <div class="py-1 px-1 bg-gray-100 grid gap-y-1">
                            <p><b>Diagnosed: </b>{{ $dia->diagnoses }}</p>
                            <div class="text-xs">
                                <p class="text-red">Consultant: {{ $dia->made_by->name }}</p>
                                <p>{{ $dia->created_at->format('Y-m-d h:i A') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="p-1 bg-gray-100">No diagnosis has been made during this visit.</p>
                    @endforelse
                </div>

                <div class="grid sm:grid-cols-2 gap-2">
                    <div class="py-2">
                        <p><b>Treatment Plan</b></p>

                        <div class="grid">
                            <ul class="grid gap-y-4">
                                @forelse ($visit->treatment_plans->where('status', Status::active) as $pres)
                                    <li class="bg-gray-100 p-2">
                                        <p>{{ $pres }}</p>
                                        <p><strong>Attending:</strong>
                                            {{ $pres->recorder?->name ?? $pres->user->name }}
                                        </p>
                                        <p><small>{{ $pres->created_at?->format('Y-m-d h:i A') }}</small></p>
                                    </li>
                                @empty
                                    <li>No treatment plans added.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="py-2">
                        <p><b>Prescriptions & Treatments</b></p>

                        <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
                            <ul class="list-disc px-3">
                                @forelse (($visit->prescription?->lines ?? []) as $pres)
                                    <li>{{ $pres }}</li>
                                @empty
                                    <li>No prescriptions provided.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                @if ($i == 0)
                    <div class="flex gap-x-2 py-2">
                        <button class="btn bg-primary" @click="$dispatch('open-closing')">Schedule Follow-Up</button>
                        <a href="#" @click.prevent="$dispatch('open-admit')"
                            class="btn bg-green-500 text-white">Admit this patient</a>
                        <button wire:click="close" wire:confirm="Are you done with this patient?"
                            class="btn bg-blue-500 text-white">Close</button>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

</div>
