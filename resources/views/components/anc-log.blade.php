@props(['viewing' => false, 'visit', 'profile'])

@if ($visit)
    <x-overlay-modal id="anc-log" title="Log Visit">
        <form id="anc-log-form">
            @csrf
            <div class="grid grid-cols-3 gap-x-4 items-center">
                <div class="form-group">
                    <label>Follow up date</label>
                    <input type="text" class="form-control" readonly
                        value="{{ $visit?->created_at?->format('Y-m-d') }}" />
                </div>
                <div class="form-group">
                    <p><b>EGA:</b> {{ $profile->maturity($visit?->created_at, true) }}</p>
                </div>

                <span></span>

                <div class="form-group">
                    <label>Height of Fundus</label>
                    <x-input-text name="fundal_height" placeholder="Height of Fundus" class="form-control"
                        value="{{ $visit?->fundal_height }}" />
                </div>
                <div class="form-group">
                    <label>Presentation</label>
                    <x-input-text name="presentation" placeholder="Presentation" class="form-control"
                        value="{{ $visit?->presentation }}" />
                </div>
                <div class="form-group">
                    <label>Relationship of presenting part to birth</label>
                    <x-input-text name="presentation_relationship" placeholder="Relation" class="form-control"
                        value="{{ $visit?->presentation_relationship }}" />
                </div>

                <div class="form-group">
                    <label>FHR</label>
                    <x-input-text name="fetal_heart_rate" placeholder="FHR" class="form-control"
                        value="{{ $visit?->fetal_heart_rate }}" />
                </div>
                <div class="form-group">
                    <label for="">Oedema</label>
                    <x-input-text name="edema" class="form-control" placeholder="Oedema"
                        value="{{ $visit?->edema }}" />
                </div>
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label>IPT <input type="checkbox" @if ($visit?->ipt) checked @endif
                                name="ipt" /></label>
                    </div>
                    <div class="form-group">
                        <label>TT <input type="checkbox" checked="{{ $visit?->tt ? 'checked' : false }}"
                                name="tt" /></label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="">Note</label>
                <x-input-textarea name="note" class="form-control" value="{{ $visit?->note }}" />
            </div>
            <div class="form-group">
                <label for="">Next appointment</label>
                <input type="date" name="return_visit" class="form-control"
                    value="{{ date('Y-m-d', strtotime('+2 weeks')) }}" required />
            </div>
            <div class="form-group">
                <button class="float-end px-2 py-1 bg-green-400">Save <i class="fa fa-save"></i></button>
            </div>
        </form>
    </x-overlay-modal>
@endif

<div>
    @if ($visit)
        <button class="btn bg-blue-400 text-white" @click="$dispatch('open-anc-log')">
            Log visit<i class="fa fa-plus"></i>
        </button>
    @endif

    <table class="table" id="table">
        <thead>
            <tr>
                <th></th>
                <th>Date</th>
                <th>EGA</th>
                <th>Fundal Height</th>
                <th>Presentation</th>
                <th>Weight (kg)</th>
                <th>BP (mmHg)</th>
                <th>PCV</th>
                <th>Glucose </th>
                <th>Protein</th>
                <th>Next Appt.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($profile->ancVisits as $v)
                @continue(empty($v->visit))

                {{-- @dump($v->visit->tests) --}}
                <tr>
                    <td class="text-center">
                        <button data-id="{{ $v->id }}" class="btn expand-i">
                            <i class="fa text-blue-700 fa-magnifying-glass"></i>
                        </button>
                    </td>
                    <td>{{ $v->created_at->format('Y-m-d') }}</td>
                    <td>{{ $profile->maturity($v->created_at) }}</td>
                    <td>{{ $v->fundal_height }}</td>
                    <td>{{ $v->presentation }}</td>
                    <td>{{ $v->visit?->vitals?->weight }}</td>
                    <td>{{ $v->visit?->vitals?->blood_pressure }}</td>
                    <td>{{ $v->visit?->getTestResults('PCV', 'PCV') }}</td>
                    <td>{{ $v->visit?->getTestResults('Urinalysis', ['glucose', 'pro%glu']) }}</td>
                    <td>{{ $v->visit?->getTestResults('Urinalysis', ['protein', 'pro%glu']) }}</td>
                    <td>{{ $v->return_visit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@pushOnce('scripts')
    <script>
        $(document).ready(function() {
            asyncForm(document.querySelector("#anc-log-form"),
                "{{ route('doctor.treat-anc', $visit?->id ?? ':not-foun') }}", (e,
                    data) => {
                    displayNotification({
                        message: 'Saved',
                        bg: ['bg-blue-400', 'text-white'],
                        options: {
                            mode: 'in-app',
                        }
                    });
                });

            $(document).on("click", ".expand-i", function(e) {
                const id = $(e.currentTarget).data().id;
                useGlobalModal((a) => {
                    a.find(MODAL_TITLE).text('Visit Log');

                    axios.get("{{ route('doctor.anc-log', ':id') }}".replace(':id', id)).then((
                        res) => {
                        a.find(MODAL_BODY).html(res.data);
                    }).catch(err => {
                        a.find(MODAL_CONTENT).html(err.response.data);
                    });
                });
            });
        });
    </script>
@endPushOnce
