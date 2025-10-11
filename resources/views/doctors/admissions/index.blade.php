@extends('layouts.app')

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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admissions as $a)
                        <tr>
                            <td>{{ $a->patient->name }}</td>
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
                            <td>
                                <a class="link" href="{{ route('doctor.show-admission', $a) }}">View</a>
                            </td>
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
        $("#admissions").DataTable();
    });
</script>
@endpush
