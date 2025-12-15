@extends('layouts.app')

@section('content')
    <div class="container grid gap-y-4">
        <div class="card p-4 bg-white">
            <p class="basic-header">Stock Take</p>
        </div>

        <div class="card p-4 bg-white">
            <livewire:inventory.stock-take :_take="$take" />
        </div>
    </div>
@endsection
