@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">{{ $patient->name }}</div>
        <div class="body">
            <div class="pt-1"></div>
            <div class="row start">
                <p class="col-6"><b>Diagnoses:</b> {{ join(',', $admission->admittable->diagnoses->map(function ($d) {
                    return $d->diagnoses;
                })->toArray()) }}</p>
                <p class="col-6"><b>Date & Time:</b> {{ $admission->created_at->format('Y-m-d, h:i A') }}</p>
                <div class="col-6">
                    <div class="pt-2"></div>
                    <table class="table-list">
                        <thead>
                            <tr>
                                <th>Treatment Plan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admission->admittable->treatments as $t)
                                <tr>
                                    <td>({{ $t->route }}) {{ $t->name }} {{ $t->dosage }} {{ $t->frequency }} {{ $t->duration }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                        <option value="{{ $ward->id }}">{{ $ward->name }} ({{ $ward->beds - $ward->filled_beds }}) ({{ $ward->type }})</option>
                    @endforeach
                </select>
                <button class="mt-1 btn btn-blue" type="submit">Submit</button>
            </div>
        </form>
    </div>
@endsection
