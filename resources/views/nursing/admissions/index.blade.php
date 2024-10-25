@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">
            <div class="card-header">Admissions</div>
        </div>
        <div class="pb-1"></div>
        <div class="body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Ward</th>
                        <th>Admitted On</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admissions as $a)
                        <tr>
                            <td><a href="{{ route('nurses.admissions.show', $a) }}">{{ $a->patient?->name }}</a></td>
                            <td>{{ $a->ward?->name }}</td>
                            <td>{{ $a->created_at->format('Y-m-d h:i A') }}</td>
                            <td>
                                @unless ($a->ward)
                                    <a href="{{ route('nurses.admissions.assign-ward', $a) }}" class="link">Assign To Ward</a>
                                @else
                                    <a href="{{ route('nurses.admissions.show', $a) }}" class="link">View</a>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
