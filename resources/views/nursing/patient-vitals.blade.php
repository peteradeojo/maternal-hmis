@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card py px">
            <div class="my">
                <h2>{{ $visit->patient->name }}</h2>
                <h3>{{ $visit->patient->card_number }}</h3>
            </div>

            <form action="" method="post">
                @foreach ($errors->all() as $message)
                    <p class="bg-red py px">{{ $message }}</p>
                @endforeach

                @csrf
                @include('nursing.components.vitals-form')
                <div class="form-group">
                    <button class="btn btn-blue">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
