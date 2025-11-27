<div class="pb-8">
    <x-patient-profile :patient="$visit->patient" />

    <p class="text-lg font-semibold">Encounter Details</p>
    <p>Date: {{ $visit->created_at->format('Y-m-d h:i A') }}</p>

    <x-reports.encounter.complaints :visit="$visit" />
    <x-reports.encounter.examinations :visit="$visit" />
    <x-reports.encounter.notes :visit="$visit" />
    <x-reports.encounter.diagnoses :visit="$visit" />
    <x-reports.encounter.tests :visit="$visit" />
    <x-reports.encounter.scans :visit="$visit" />
    <x-reports.encounter.prescriptions :visit="$visit" />
    <x-reports.encounter.admission :visit="$visit" />
</div>
