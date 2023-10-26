@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header">Prescriptions</div>
        <div class="body mt-2">
            @include('components.phm-prescriptions-table')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("table#prescriptions").DataTable();
    </script>
@endpush
