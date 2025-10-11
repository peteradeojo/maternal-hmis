@extends('layouts.app')

@section('content')
    <div class="card p-1">
        <div class="header">
            <p class="card-header bold">{{ $data->patient->name }}</p>
            <p>Ward: {{ $data->ward?->name ?? 'Unassigned' }}</p>
        </div>
        <div class="body p-3">
            <p class="bold">Admission Plan</p>

            <table class="table">
                <tbody>
                    <tr>
                        <td>Admitted by:</td>
                        <td>{{ $data->plan->user->name }}</td>
                    </tr>
                    <tr>
                        <td>Admitted on:</td>
                        <td>{{ $data->created_at->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td>Ward</td>
                        <td>{{ $data->ward?->name ?? 'Not yet assigned to ward.' }}</td>
                    </tr>
                    <tr>
                        <td>Indication</td>
                        <td>{{ $data->plan->indication ?? 'No indication' }}</td>
                    </tr>
                    <tr>
                        <td>Treatment Plan</td>
                        <td>
                            <ul class="list-disc list-inside">
                                @forelse ($data->plan->treatments ?? [] as $treatment)
                                    <li class="list-item">{{ $treatment }}</li>
                                @empty
                                    <li>No plan</li>
                                @endforelse
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>Tests</td>
                        <td>
                            @include('doctors.components.test-results', ['tests' => $data->tests])
                        </td>
                    </tr>
                    <tr>
                        <td>Investigations</td>
                        <td>
                            <ul class="list-disc list-inside">
                                @forelse ($data->plan->scans ?? [] as $treatment)
                                    <li class="list-item">{{ $treatment }}</li>
                                @empty
                                    <li>No scan</li>
                                @endforelse
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td>Note</td>
                        <td>{{ $data->plan->note ?? 'nil' }}</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{route('doctor.show-admission-plan', $data)}}" class="link">View Plan</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
