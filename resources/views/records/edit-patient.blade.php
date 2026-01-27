{{-- @extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header">{{ $patient->name }}</div>

        <form action="" method="post">
            @csrf
            <div class="form-group">
                <h2>Biodata</h2>
                @include('records.components.patient-form-basic', ['patient' => $patient])
            </div>
            <div class="form-group">
                <h2>Next of Kin</h2>
                @include('records.components.next-of-kin-form')
            </div>
            <div class="form-group"><button class="btn btn-red">Submit</button></div>
        </form>
    </div>
@endsection --}}


<div>
    <form action="{{ route('records.patient.edit', $patient) }}" method="post">
        @csrf
        <div class="form-group">
            <h2 class="text-lg font-semibold underline">Biodata</h2>
            @include('records.components.patient-form-basic', ['patient' => $patient])
        </div>
        <div class="form-group">
            <h2 class="text-lg font-semibold underline">Next of Kin</h2>
            @include('records.components.next-of-kin-form', ['patient' => $patient])
        </div>
        <div class="form-group flex justify-end sticky bottom-4">
            <button class="btn bg-red-500 text-white">Save <i class="fa fa-save"></i></button>
        </div>
    </form>
</div>
