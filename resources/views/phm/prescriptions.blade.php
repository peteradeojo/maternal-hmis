@extends('layouts.app')
@section('title', 'Pending prescriptions')

@section('content')
    <div class="card py px">
        <div class="card-header">Prescriptions</div>
        <div class="body mt-2">
            <table id="prescriptions">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Card Number</th>
                        <th>Gender</th>
                        <th>Phone No.</th>
                        <th>Date</th>
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
        $(document).ready(() => {
            function loadPrescription(e) {
                try {
                    e.preventDefault();
                    const el = e.currentTarget;
                    const [type, id] = el.dataset.event.split(":") || [];

                    useGlobalModal((a) => {
                        a.find(".modal-title").text("Prescription");

                        axios.get("{{ route('dis.get-prescription') }}?type=:type&id=:id".replace(':type',
                                    type)
                                .replace(':id', id))
                            .then((response) => {
                                a.find(".modal-body").html(response.data);
                            }).catch((err) => {
                                a.find(".modal-body").html(err.response.data);
                                displayNotification({
                                    message: "An error occurred while loading the prescription.",
                                    bg: ["bg-red-500", "text-white"],
                                    type: 'in-app'
                                });
                            });
                    });
                } catch (error) {
                    displayNotification({
                        message: "An error occurred while loading the prescription.",
                        bg: "bg-red-500 text-white".split(" "),
                        type: 'in-app'
                    });
                }
            };

            $("table#prescriptions").DataTable({
                serverSide: true,
                ajax: '{{ route('dispensary.api.prescriptions.data') }}',
                columns: [{
                        data: 'patient.name',
                        name: 'patient.name'
                    },
                    {
                        data: 'patient.card_number',
                        name: 'patient.card_number'
                    },
                    {
                        data: 'patient.gender'
                    },
                    {
                        data: 'patient.phone'
                    },
                    {
                        data: (row) => new Date(row.created_at).toLocaleDateString('en-CA', {
                            minute: '2-digit',
                            hour: '2-digit',
                        }),
                    },
                    {
                        data: (row) =>
                            `<a data-event="${row.event_name}:${row.event_id}" href="#" class='link view-prescription'>View</a>`,
                        orderable: false,
                        searchable: false
                    },
                ],
                responsive: true,
                ordering: false,
            });

            $(document).on('click', '.view-prescription', loadPrescription);

        });
    </script>
@endpush
