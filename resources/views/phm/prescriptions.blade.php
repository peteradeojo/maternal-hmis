@extends('layouts.app')
@section('title', 'Pending prescriptions')

@section('content')
    <div class="grid gap-y-4">
        <div class="card p-1">
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
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card p-1">
            <div class="basic-header">Reverse Lookup</div>

            <table id="reverse-lookup" class="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Card Number</th>
                        <th>Gender</th>
                        <th>Category</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
                    const bill = el.dataset.bill;

                    useGlobalModal((a) => {
                        a.find(".modal-title").text("Prescription");
                        a.find(MODAL_BODY).html(`@include('components.spinner')`)

                        axios.get("{{ route('dis.get-prescriptions', ':id') }}".replace(':id', bill))
                            .then((response) => {
                                a.find(".modal-body").html(response.data);
                            }).catch((err) => {
                                a.find(".modal-body").html(`<p>An error occurred.</p>`);
                                displayNotification({
                                    message: "An error occurred while loading the prescription: " +
                                        err.message,
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
                        data: ({
                                patient,
                                id
                            }) =>
                            `<a class="link" href="{{ route('dis.get-prescriptions', ':id') }}">${patient.name}</a>`
                            .replace(':id', id), //'patient.name',
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
                ],
                // order: [[4, 'desc']],
                responsive: true,
                ordering: false,
            });

            $("table#reverse-lookup").DataTable({
                serverSide: true,
                ajax: {
                    url: "{{route('phm-api.reverse-lookup')}}"
                },
                columns: [
                    {
                        data: ({id, patient}) =>  `<a href="{{route('dis.get-prescriptions', ':id')}}" class="link">${patient.name}</a>`.replace(':id', id),
                    },
                    {
                        data: 'patient.card_number',
                    },
                    {
                        data: 'patient.gender',
                    },
                    {
                        data: 'patient.category.name',
                    },
                    {
                        data: (row) => parseDateFromSource(row.created_at),
                    },
                ],
            });

            // $(document).on('click', '.view-prescription', loadPrescription);

        });
    </script>
@endpush
