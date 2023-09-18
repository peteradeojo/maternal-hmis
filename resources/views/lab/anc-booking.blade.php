@extends('layouts.app')
@section('title', $profile->patient->name)
@section('content')
    <div class="card py px">
        <div class="header">
            <p class="card-header">{{ $profile->patient->name }}</p>
            <p><b>Card number: </b>{{ $profile->patient->card_number }}</p>
            <p><b>Gender: </b>{{ $profile->patient->gender_value }}</p>
            <p><b>Phone number: </b>{{ $profile->patient->phone }}</p>
            <p><b>Age: </b>{{ $profile->patient->dob?->diffForHumans(syntax: 1) }}</p>
        </div>
        <div class="body py">
            @foreach ($errors->all() as $error)
                <div class="p-1 bg-red text-white">{{ $error }}</div>
            @endforeach
            <form action="" method="post">
                @csrf
                <div class="row">
                    @foreach ($tests as $i => $test)
                        <div class="col-4 pr-1 form-group">
                            <label>{{ $test }}</label>
                            <input type="text" name="tests[{{ $test }}]" id="" class="form-control"
                                value="{{ $profile->tests[$test] ?? null }}">
                        </div>
                    @endforeach
                </div>
                <div class="pt-1 form-group">
                    <label><input type="checkbox" name="completed" /> Is Completed?</label><br />
                    <button class="mt-1 btn btn-blue" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
