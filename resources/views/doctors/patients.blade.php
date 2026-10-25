@extends('layouts.app')

@section('content')
    <livewire:doctor.waiting-patients :user="auth()->user()" />
@endsection
