@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card py px">
            <div class="header card-header">
                {{ $patient->name }}
            </div>
            <div class="body">
                <form action="" method="post">
                    @csrf
                    <h2 class="mt-1">Profile</h2>
                    {{-- @livewire('lmp-form') --}}

                    <h2 class="mt-1">Card Type</h2>
                    <select name="card_type" required>
                        @foreach (AncCardEnum::cases() as $v)
                            <option value="{{ $v->value }}">{{ $v->name }}</option>
                        @endforeach

                    </select>

                    <h2 class="mt-1">Spouse Information</h2>
                    @include('records.components.spouse-form')

                    <div class="form-group">
                        <button class="btn btn-blue">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
