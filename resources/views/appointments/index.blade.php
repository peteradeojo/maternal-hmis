@extends('layouts.app')
@section('title', 'Appointments')

@section('content')
    <div class="container">
        <div class="card">
            <p class="card-header">Appointments</p>

            <table id="table" class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Date</th>
                        {{-- <th></th> --}}
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const table = $("#table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('api.records.appointments') }}"
                },
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    }, {
                        data: ({
                                patient,
                                id
                            }) =>
                            `<a href="{{ route('records.appointments.show', ':id') }}" class='link'>${patient.name}</a>`
                            .replace(':id', id),
                    },
                    {
                        data: 'patient.phone'
                    },
                    {
                        data: (row) => parseDateFromSource(row.appointment_date)
                    },
                ],
                rowId: 'id',
            })

            const pluse = (i) => {
                return `<div class='p-2'>
                    <p>${JSON.stringify(i)}</p>
                    </div>`;
            };

            table.on('click', 'tbody td.dt-control', function(e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                } else {
                    row.child(pluse(row.data())).show();
                }
            });
        });
    </script>
@endpush
