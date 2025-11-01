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
    <script defer>
        $(document).ready(function() {
            function getVisits() {}

            let dataTable = $("#table").DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ route('api.nursing.vitals') }}",
                    headers: {
                        "Accept": "application/json",
                    }
                },
                language: {
                    emptyTable: 'No results',
                    searchPlaceholder: "Name, card number",
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
                        }) => new Date(created_at).toLocaleDateString('en-CA', {
                            minute: '2-digit',
                            hour: '2-digit',
                        }),
                    },
                    {
                        data: (row) =>
                            `<a href="#" data-visit="${row.id}" class="link load-vitals">Take Vitals</a>`
                    },
                ],
                order: [
                    [3, 'desc']
                ],
                ordering: false,
                responsive: true,
            });

            $(document).on('click', '.load-vitals', function(e) {
                e.preventDefault();
                let visitId = $(this).data('visit');

                axios.get("{{ route('nurses.patient-vitals', ':id') }}".replace(':id', visitId))
                    .then(res => {
                        useGlobalModal((a) => {
                            a.find(".modal-title").text("Patient Vitals");
                            a.find(".modal-body").html(res.data);
                        });
                    })
                    .catch(err => {
                        useGlobalModal((a) => {
                            a.find(".modal-title").text("Patient Vitals");
                            a.find(".modal-body").html(err.response.data);
                        });
                        // displayNotification({
                        //     message: `An error occurred while loading vitals for visit #${visitId}.`,
                        //     options: {
                        //         mode: 'in-app',
                        //     },
                        //     bg: ['bg-red-500', 'text-white'],
                        // });
                    });
            });
        });
    </script>
@endpush
