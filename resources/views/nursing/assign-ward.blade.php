@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">{{ $patient->name }}</div>
        <div class="body">
            <p><b>Admitted by:</b> {{ $admission->plan->user->name }}</p>
            <p><b>Date/Time:</b> {{ $admission->created_at }}</p>

            <p class="bold">Admission Plan</p>

            @include('components.admission-plan', ['data' => $admission])
        </div>
    </div>
    </div>
    <div class="pt-2"></div>
    <div class="card py px">
        <div class="header">Assign Ward</div>
        <form action="" method="post">
            @csrf
            <div class="form-group">
                <select name="ward" class="form-control">
                    @foreach ($wards as $ward)
                        <option value="{{ $ward->id }}" @if ($ward->available_beds == 0)
                            disabled
                        @endif >{{ $ward->name }} ({{ $ward->beds - $ward->filled_beds }})
                            ({{ $ward->type }})
                        </option>
                    @endforeach
                </select>
                <button class="mt-1 btn btn-blue" type="submit">Submit</button>
            </div>
        </form>
    </div>
@endsection
