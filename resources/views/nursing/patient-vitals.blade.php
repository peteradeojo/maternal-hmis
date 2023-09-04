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
                <div class="form-group">
                    <label for="temperature">Temperature (&deg;C)</label>
                    <input type="number" name="temperature" id="temperature" class="form-control"
                        value="{{ old('temperature') }}" step="0.1">
                </div>
                <div class="form-group">
                    <label for="pulse">Pulse</label>
                    <input type="number" name="pulse" id="pulse" class="form-control" value="{{ old('pulse') }}">
                </div>
                <div class="form-group">
                    <label for="respiratory_rate">Respiratory Rate</label>
                    <input type="number" name="respiratory_rate" id="respiratory_rate" class="form-control"
                        value="{{ old('respiratory_rate') }}">
                </div>
                <div class="form-group">
                    <label for="blood_pressure">Blood Pressure (mmHg)</label>
                    <input type="text" name="blood_pressure" id="blood_pressure" class="form-control"
                        value="{{ old('blood_pressure') }}">
                </div>
                <div class="form-group">
                    <label for="weight">Weight (kg)</label>
                    <input type="number" name="weight" id="weight" class="form-control" value="{{ old('weight') }}"
                        step="0.1">
                </div>
                <div class="form-group">
                    <label for="height">Height (cm)</label>
                    <input type="number" name="height" id="height" class="form-control" value="{{ old('height') }}"
                        step="0.1">
                </div>
                <div class="form-group">
                    <button class="btn btn-blue">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection