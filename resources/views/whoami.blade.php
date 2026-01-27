@extends('layouts.auth')

@section('content')
<div class="container w-full md:w-1/2">
    <p class="basic-header">Tell us your name?</p>
    <form action="" method="post">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <x-input-text name="whoami" class="form-control" required />
        </div>
        <div class="form-group">
            <button class="btn bg-green-800 text-white">Submit</button>
        </div>
    </form>
</div>
@endsection
