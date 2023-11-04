@extends('layouts.app')

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
@endsection
