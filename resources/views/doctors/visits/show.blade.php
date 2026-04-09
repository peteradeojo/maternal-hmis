@extends('layouts.app')

@section('content')
    <div class="container p-4 bg-white">
        <button class="report btn btn-green btn-sm no-print">Download Report</button>
        <a class="btn bg-blue-500 text-white no-print" href="{{route('generate-visit-report', $visit)}}">View Report</a>
        <div class="body">
            <x-patient-profile :patient="$visit->patient" />

            <div class="py-2">
                <p><b>Date:</b> {{ $visit->created_at->format('Y-m-d h:i A') }} </p>
                <p><b>Visit Type:</b> {{ $visit->type }} </p>
            </div>
        </div>

        <x-reports.encounter.vitals :visit="$visit" />
        <x-reports.encounter.complaints :visit="$visit" />
        <x-reports.encounter.examinations :visit="$visit" />
        <x-reports.encounter.notes :visit="$visit" />
        <x-reports.encounter.diagnoses :visit="$visit" />
        <x-reports.encounter.tests :visit="$visit" />
        <x-reports.encounter.scans :visit="$visit" />
        <x-reports.encounter.prescriptions :visit="$visit" />
        <x-reports.encounter.admission :visit="$visit" />
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $('.btn.report').on('click', async function(e) {
                const res = await axios.get("{{ route('generate-visit-report', $visit) }}", {
                    Accept: 'application/json'
                });
            });
        });
    </script>
@endpush
