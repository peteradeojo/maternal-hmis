@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container relative grid gap-y-4">
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

        {{-- @if (in_array($user->department_id, [DepartmentsEnum::PHA->value, DepartmentsEnum::DIS->value]))
            @livewire('phm.waiting-patients')
        @endif --}}

        @if ($user->department_id == DepartmentsEnum::NHI->value)
            @livewire('nhi.pending-authorizations')
        @endif

        @if ($user->department_id == DepartmentsEnum::RAD->value)
            @livewire('rad.waiting-patients')
        @endif

        @if ($user->department_id == DepartmentsEnum::PHA->value)
            <div class="card p-4 bg-white my-3">
                <div class="grid grid-cols-5 gap-4">
                    <a href="{{ route('phm.inventory') }}" class="p-2 border-2 border-blue-400 grid place-items-center">
                        <p>Inventory <i class="fa fa-list"></i></p>
                    </a>

                    <a href="{{ route('phm.inventory.purchases') }}"
                        class="p-2 border-2 border-blue-400 grid place-items-center">
                        <p>Purchases <i class="fa fa-dollar"></i></p>
                    </a>
                    {{-- <div class="p-2 border-2 border-blue-400 grid place-items-center">
                        <p>Stock History <i class="fa fa-clock text-blue-400"></i></p>
                    </div> --}}
                    <div class="p-2 border-2 border-blue-400 grid place-items-center">
                        <p>Transfer <i class="fa fa-arrow-right text-blue-500"></i></p>
                    </div>
                    <a href="{{ route('phm.inventory.suppliers') }}"
                        class="p-2 border-2 border-blue-400 grid place-items-center">
                        <p>Suppliers <i class="fa fa-arrow-right text-blue-500"></i></p>
                    </a>
                    {{-- <a href="{{ route('phm.inventory.bulk-import') }}" class="p-2 border-2 border-blue-400 grid place-items-center">
                        <p>Import <i class="fa fa-gears text-blue-500"></i></p>
                    </a> --}}
                    <a href="{{ route('phm.inventory.stock-take') }}"
                        class="p-2 border-2 border-blue-400 grid place-items-center">
                        <p>Stock Take <i class="fa fa-check text-blue-500"></i></p>
                    </a>
                </div>
            </div>
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
