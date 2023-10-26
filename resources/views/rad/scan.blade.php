@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header">{{ $doc->patient->name }}</div>
        <hr>
        <div class="body py-2">
            @foreach ($doc->radios as $r)
            @endforeach
        </div>
    </div>
@endsection
