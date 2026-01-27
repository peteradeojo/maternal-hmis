@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">
            {{ $admission->patient->name }} ({{ $admission->ward->name }})
        </div>
        <div class="body py">
            {{-- @dump($treatments) --}}
            <p class="bold pb-1">Confirm administration for the following medication for {{ $admission->patient->name }}?</p>
            <form action="?confirm" method="post">
                @csrf
                @foreach ($treatments as $i => $t)
                    <p>{{ $i + 1 }}. {{ $t->item?->name ?? $t->description }}
                        {{ $t->route }}
                        {{ $t->dosage }}
                        {{ $t->frequency }}
                        {{ $t->duration }}</p>
                    <input type="hidden" name="treatments[]" value="{{ $t->id }}">
                @endforeach

                <div class="form-group">
                    <button type="submit" class="btn btn-red">Confirm</button>
                </div>
            </form>

        </div>
    </div>
@endsection
