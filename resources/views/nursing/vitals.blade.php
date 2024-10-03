@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">Vitals</div>

        <div class="py-2">
            <table id="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Card Number</th>
                        <th>Category</th>
                        <th>Date/Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let dataTable = $("#table").DataTable({
            language: {
                emptyTable: 'Loading',
            },
            columns: [{
                    data: 'patient.name'
                },
                {
                    data: 'patient.card_number'
                },
                {
                    data: 'patient.category.name'
                },
                {
                    data: ({
                        created_at
                    }) => new Date(created_at).toLocaleString('en-GB'),
                },
                {
                    data: (row) =>
                        `<a href="{{ route('nurses.patient-vitals', ':id') }}" class="link">Take Vitals</a>`
                        .replace(':id', row.id),
                },
            ],
            order: false,
        });

        function fetchUpdatedData() {
            return fetch("{{ route('api.nursing.vitals') }}")
                .then(response => response.json())
                .catch(error => console.error('Error fetching data:', error));
        }

        function updateDataTable(newData) {
            dataTable.clear();
            dataTable.rows.add(newData.data);
            dataTable.draw();
        }

        function getUpdate() {
            return fetchUpdatedData().then(newData => {
                updateDataTable(newData);
            });
        }

        function startPolling(interval = 5000) {
            setInterval(getUpdate, interval);
        }

        document.addEventListener('DOMContentLoaded', () => {
            getUpdate().then(() => {
                startPolling();
            });
        });
    </script>
@endpush
