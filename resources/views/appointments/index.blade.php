@extends('layouts.app')
@section('title', 'Appointments')

@section('content')
    <div class="container">
        <x-back-link />
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
                            `<a href="{{ route('records.patient', ':id') }}" class='link'>${patient.name} (${patient.card_number})</a>`
                            .replace(':id', patient.id),
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

            const format = async ({
                id
            }) => {
                if (!id) return;
                const res = await axios.get("{{ route('records.appointments.show', ':id') }}?mini".replace(
                    ':id', id));
                return res.data;
            };

            table.on('click', 'tbody td.dt-control', async function(e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                } else {
                    try {
                        const data = await format(row.data());
                        row.child(data).show();
                    } catch (err) {
                        notifyError(err);
                        row.child(err.message).show();
                    }
                }
            });

            $(document).on('click', '.subcheckin', function(e) {
                useGlobalModal((a) => {
                    a.find(MODAL_TITLE).html('Patient Appointment Check-In');
                    const row = table.row(e.target.closest('tr'));
                    const {
                        patient, id
                    } = row.data();

                    axios.get(`{{ route('records.start-visit', ':id') }}?appointment=${id}`.replace(':id', patient
                        .id)).then((res) => {
                            a.find(MODAL_CONTENT).html(res.data);
                        }).catch((err) => console.error(err));
                });
            });
        });
    </script>
@endpush
