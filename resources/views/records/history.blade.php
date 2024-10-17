@extends('layouts.app')
@section('title', 'History')

@section('content')
    <div class="card">
        <div class="header">History</div>
        <div class="body py-4 px-1">
            <livewire:records.visit-search />
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script defer>
        $("#table").DataTable({
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('records.get-history') }}",
                dataSrc: 'data',
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'patient.name'
                },
                {
                    data: 'patient.card_number'
                },
                {
                    data: 'patient.category.name'
                },
                {
                    data: 'visit.type'
                },
                {
                    data: ({
                        created_at
                    }) => new Date(created_at).toLocaleString()
                },
                {
                    data: (row) =>
                        `<a href="/records/visit-history/${row.id}" class="text-blue-600 underline">View</a>`
                },
            ]
        })
    </script>
@endPushOnce
