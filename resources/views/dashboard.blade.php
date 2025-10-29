@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container relative">
        @livewire('dashboard.patient-stats', ['user' => $user])

        @if ($user->department_id == DepartmentsEnum::DOC->value)
            @livewire('doctor.waiting-patients', ['user' => $user])
        @endif

        @if ($user->department_id == DepartmentsEnum::NUR->value)
            @livewire('nursing.vital-list')
        @endif

        @if ($user->department_id == DepartmentsEnum::IT->value)
            @livewire('it.statistics')
            @livewire('it.departments')
        @endif

        @if ($user->department_id == DepartmentsEnum::LAB->value)
            @livewire('lab.waiting-patients', ['user' => $user])
        @endif

        @if (in_array($user->department_id, [DepartmentsEnum::PHA->value, DepartmentsEnum::DIS->value]))
            @livewire('phm.waiting-patients')
        @endif

        @if ($user->department_id == DepartmentsEnum::NHI->value)
            @livewire('nhi.pending-authorizations')
        @endif

        @if ($user->department_id == DepartmentsEnum::RAD->value)
            @livewire('rad.waiting-patients')
        @endif
    </div>
@endsection

@push('scripts')
    <script defer>
        $(document).ready(() => {
            window.axios.get("/sanctum/csrf-cookie");
            $("table#prescriptions").DataTable();
        })
    </script>
@endpush

