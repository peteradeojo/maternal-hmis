@extends('layouts.app')
@section('title', "Bills | {$patient->name}")

@section('content')
    <x-back-link />
    <div class="grid gap-y-4">
        <div class="p-4 bg-white">
            <x-patient-profile :patient="$patient" />
        </div>

        @foreach ($patient->visits->where('status', Status::active) as $visit)
            <div class="p-2 bg-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p>ID: #{{ $visit->id }}</p>
                        <p>Type: {{ $visit->type }}</p>
                        <p>Date: {{ $visit->created_at }}</p>
                    </div>
                    @isset($visit->admission)
                        <p class="text-red-600">
                            <i>
                                <i class="fa fa-circle-exclamation"></i>
                                Patient was admitted during this visit.
                            </i>
                        </p>
                    @endisset
                </div>
                <div class="py-2 flex gap-x-4">
                    <a href="#" data-visit="{{ $visit->id }}" class="btn bg-green-400 get-bill">Create
                        Bill</a>

                    @if ($visit->bills->count() > 0)
                        <a href="{{ route('billing.view-bills', $visit) }}" class="btn text-white bg-blue-400">View
                            bills</a>
                    @endif
                    <button href="#" data-visit="{{ $visit->id }}"
                        class="btn bg-red-600 check-out text-white">Check out</button>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(() => {
            $(document).on('click', '.check-out', (e) => {
                const visitId = $(e.currentTarget).data('visit');

                $(e.currentTarget).attr('disabled', true);

                axios.post(
                        "{{ route('api.records.check-out', ':v') }}".replace(':v', visitId)
                    )
                    .then((res) => {
                        const {message, status, ok} = res.data;
                        if (!ok) {
                            notifyAction(message);
                            return;
                        }

                        notifySuccess(message);
                        window.location.href = "{{route('billing.index')}}";
                    })
                    .catch((err) => {
                        notifyError(err.message);
                    }).finally(() => $(e.currentTarget).attr('disabled', false));
            });

            $(document).on('click', '.get-bill', (e) => {
                e.preventDefault();
                const el = e.currentTarget;
                const visitId = $(el).data('visit');

                $(el).attr('disabled', true);
                axios.get("{{ route('records.show-history', ':visit') }}".replace(':visit', visitId)).then((
                    res) => {
                    useGlobalModal((a) => {
                        a.find(MODAL_TITLE).text(`Bill for #${visitId}`);
                        a.find(MODAL_CONTENT).html(res.data);
                    });

                }).catch((err) => {
                    notifyError("An error occurred: " + err.message);
                }).finally(() => $(el).attr('disabled', false));

                e.stopPropagation();
            });
        });
    </script>
@endpush
