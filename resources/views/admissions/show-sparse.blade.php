<div>
    <x-patient-profile :patient="$admission->patient" />
    @include('components.reports.encounter.diagnoses', ['visit' => $admission->visit])
    @include('components.reports.encounter.admission', ['visit' => $admission->visit])
</div>
