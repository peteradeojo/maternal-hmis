@extends('layouts.app')

@section('content')
    @include('doctors.components.history-report', ['visit' => $visit->visit])
@endsection
