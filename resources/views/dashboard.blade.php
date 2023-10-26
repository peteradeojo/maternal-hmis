@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert bg-green py px" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @livewire('dashboard.patient-stats', ['user' => $user])

        {{-- <div class="card"> --}}
        @if ($user->department_id == DepartmentsEnum::DOC->value)
            @livewire('doctor.waiting-patients', ['user' => $user])
        @endif

        @if ($user->department_id == DepartmentsEnum::NUR->value)
            @livewire('nursing.vital-list')
        @endif

        @if ($user->department_id == DepartmentsEnum::IT->value)
            @livewire('it.departments')
        @endif

        @if ($user->department_id == DepartmentsEnum::LAB->value)
            @livewire('lab.waiting-patients', ['user' => $user])
        @endif

        @if (in_array($user->department_id, [DepartmentsEnum::PHA->value, DepartmentsEnum::DIS->value]))
            @livewire('phm.waiting-patients')
        @endif
        {{-- </div> --}}
    </div>
@endsection

@push('scripts')
    <script>
        $("#waitlist-table")?.DataTable();
        $("#vitals-table").DataTable();
        $("table#prescriptions").DataTable();
    </script>
@endpush

@push('scripts')
@endpush
