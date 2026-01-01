<div>
    <x-patient-profile :patient="$admission->patient" />

    <livewire:doctor.add-prescription :visit="$admission->plan" :canDelete="false" />

    <livewire:admissions.review :admission="$admission" />
</div>
