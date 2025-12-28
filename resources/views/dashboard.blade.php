@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container relative grid gap-y-4">
        @livewire('dashboard.patient-stats', ['user' => $user])

        @role('doctor')
        @livewire('doctor.waiting-patients', ['user' => $user])
        @endrole

        @role('nurse')
        @livewire('nursing.vital-list')
        @endrole

        @role('admin')
        @livewire('it.statistics')
        @livewire('it.departments')
        @endrole

        @role('lab')
        @livewire('lab.waiting-patients', ['user' => $user])
        @endrole


        @role('billing')
        @livewire('nhi.pending-authorizations')
        @endrole

        @role('radiology')
        @livewire('rad.waiting-patients')
        @endrole

        @role('pharmacy')
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
                {{-- <a href="{{ route('phm.inventory.bulk-import') }}"
                    class="p-2 border-2 border-blue-400 grid place-items-center">
                    <p>Import <i class="fa fa-gears text-blue-500"></i></p>
                </a> --}}
                <a href="{{ route('phm.inventory.stock-take') }}"
                    class="p-2 border-2 border-blue-400 grid place-items-center">
                    <p>Stock Take <i class="fa fa-check text-blue-500"></i></p>
                </a>
            </div>
        </div>
        @endrole
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
