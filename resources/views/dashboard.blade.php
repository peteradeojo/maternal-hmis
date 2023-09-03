@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container">
        @livewire('dashboard.patient-stats', ['user' => $user])

        <div class="card">
            @if ($user->department_id == DepartmentsEnum::DOC->value)
                @livewire('doctor.waiting-patients', ['user' => $user])
            @endif

            @if ($user->department_id == DepartmentsEnum::NUR->value)
                @livewire('nursing.vital-list')
            @endif

            @if ($user->department_id == DepartmentsEnum::IT->value)
                {{-- @livewire('pharmacy.prescription-list') --}}
                @include('it.departments')
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("#waitlist-table")?.DataTable();
    </script>
@endpush

@push('scripts')
    <script>
        $("#vitals-table").DataTable();
    </script>
@endpush
