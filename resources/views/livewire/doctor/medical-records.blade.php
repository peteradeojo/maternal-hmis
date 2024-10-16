<div wire:poll.3000ms>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    @for ($i = 0; $i < $visit->patient->visits->count(); $i++)
        <div class="py-2">
            @php
                $v = $visit->patient->visits[$i]->visit;
            @endphp
            <div class="border-2 border-red-300 p-1">
                <div class="flex justify-between">
                    <p>Date: {{ $v->created_at->format('Y-m-d h:i A') }}</p>
                    <div class="flex gap-x-3">
                        @if ($i == 0)
                            <a href="{{ route('doctor.admit', ['visit' => $visit->visit]) }}"
                                class="btn btn-sm bg-green-500 text-white">Admit</a>
                            <button wire:click="close" wire:confirm="Are you done with this patient?"
                                class="btn btn-sm bg-blue-500 text-white">Close</button>
                        @endif
                    </div>
                </div>

                <div class="pt-1"></div>
                <p><b>History</b></p>
                <table class="table bg-gray-100 p-2">
                    <tr>
                        <th>Presentation</th>
                        <th>Duration</th>
                        <th></th>
                    </tr>
                    @forelse ($v->histories as $h)
                        <tr>
                            <td>{{ $h->presentation }}</td>
                            <td>{{ $h->duration }}</td>
                            <td class="w-1/12 text-center"><button wire:click="deleteHistory({{ $h->id }})"
                                    class="btn btn-sm btn-red">&times;</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No history</td>
                        </tr>
                    @endforelse

                </table>

                {{-- Examinations --}}
                <div class="pt-1"></div>
                <p><b>Examinations</b></p>

                <div class="p-1 bg-gray-100">
                    @unless ($visit->examination)
                        <p>No examination was conducted.</p>
                    @else
                        <div>
                            <p><b>General</b></p>
                            <p>{{ $visit->examination->general }}</p>
                        </div>

                        @foreach ($visit->examination->specifics as $k => $sp)
                            <div>
                                <p><b>{{ unslug($k, fn($str) => ucwords(str_replace('digital', '/', $str))) }}</b></p>
                                <p>{{ $sp }}</p>
                            </div>
                        @endforeach
                    @endunless
                </div>

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

                @include('doctors.components.test-results', ['tests' => $v->tests])

                @if ($visit->imagings?->count() > 0)
                    <div class="py-2"></div>
                    <p><b>Scans</b></p>
                    @foreach ($visit->imagings as $img)
                        <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
                            <p><b class="text-sm">{{ $img->name }}</b>:
                                <br>
                                <small>Result: @empty($img->path)
                                        No result
                                    @else
                                        <a href="#" data-target="#scan-result-modal"
                                            data-scanid="{{ $img->id }}"
                                            class="scan-result text-blue-500 underline">View Result</a>
                                    @endempty
                                </small>
                                <br>
                                <small>Comment: {{ $img->comment ?? 'No comment' }}</small>
                            </p>

                        </div>
                    @endforeach
                @endif

                <div class="py-2"></div>
                <p><b>Prescriptions & Treatments</b></p>

                <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
                    <ul class="list-disc px-3 text-sm">
                        @foreach ($visit->prescriptions as $pres)
                            <li>{{ $pres }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endfor
</div>

@script
    <script>
        document.querySelectorAll(".scan-result").forEach((trigger) => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();

                const {
                    target,
                    scanid
                } = e.target.dataset;
                const modal = document.querySelector(target);

                // Guard clause
                // if (!modal) return;

                modal.classList.remove("hide");

                fetch("{{ route('api.rad.scans.result', ['scan' => ':id']) }}".replace(":id", scanid))
                    .then((res) => {
                        res.json().then((data) => {
                            let el = '';
                            if (data.path.endsWith(".pdf")) {
                                el = `<iframe src="${data.path}" frameborder="0" width="100%" height="600"
                            allowfullscreen="true"></iframe>`;
                            } else {
                                el = `<img src='${data.path}' />`
                            }

                            modal.querySelector("#display").innerHTML = el;
                        });
                    }).catch((err) => {
                        console.error(err);
                    });
            });
        });
    </script>
@endscript
