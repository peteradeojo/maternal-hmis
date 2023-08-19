@extends('layouts.app')
@section('title', 'Patients')

@section('content')
    <div class="container">
        <div class="card py px">
            <h2 class="my">Patients</h2>
            <table id="patients">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Card Number</th>
                        <th>Gender</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const table = new DataTable('#patients', {
            ajax: {
                url: "//",
            }
        });
    </script>
@endpush
