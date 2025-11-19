@extends('layouts.app')
@section('title', 'Wards')

@section('content')
    <div class="card p-1">
        <div class="header">
            <div class="card-header bold">Admissions</div>
        </div>
        <div class="body py-2">
            <table class="table" id="admissions">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Card Number</th>
                        <th>Ward</th>
                        <th>Status</th>
                        <th>Date Admitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admissions as $a)
                        <tr>
                            <td><a href="{{ route('doctor.show-admission', $a) }}" class="link">{{ $a->patient->name }}</a>
                            </td>
                            <td>{{ $a->patient->card_number }}</td>
                            <td>
                                @if ($a->ward)
                                    {{ $a->ward->name }}
                                @else
                                    <span class="text-gray-500">Unassigned</span>
                                @endif
                            </td>
                            <td>{{ Status::tryFrom($a->status)->name }}</td>
                            <td>{{ $a->created_at->format('Y-m-d h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#admissions").DataTable({
                responsive: true,
            });
        });
    </script>
@endpush
