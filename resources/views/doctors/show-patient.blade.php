@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header">{{ $patient->name }}</div>
    </div>
@endsection
