@extends('layouts.app')

@section('content')
    <div class="bg-white p-4">
        <table class="table" id="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Date Admitted</th>
                    <th>Discharged</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        function showAdmission(id) {
            useGlobalModal((a) => {
                a.find(MODAL_TITLE).text('Admission');

                axios.get("{{ route('records.admissions.show', ':id') }}?sparse".replace(':id', id))
                    .then(({
                        data
                    }) => {
                        a.find(MODAL_BODY).html(data);
                    })
                    .catch((err) => {
                        console.error(err);
                        a.find(MODAL_BODY).html(`<p class='text-red-500 font-semibold text-2xl tracking-wide
                        '>Oops</p>`);
                    });
            });
        }

        $(document).ready(function() {
            $("#table").DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('api.records.admissions') }}"
                },
                columns: [{
                        data: ({
                                patient,
                                id
                            }) =>
                            `<a href='#' x-on:click='showAdmission(${id})' class='link'>${patient.name}</a>`,
                        name: 'patient.name'
                    },
                    {
                        data: 'ward.name'
                    },
                    {
                        data: ({
                            created_at
                        }) => formatUTCDate(created_at),
                    },
                    {
                        data: ({
                            discharged_on
                        }) => discharged_on ? formatUTCDate(discharged_on) : 'Not discharged'
                    }
                ],
                // ordering: false,
                // order: [
                //     [2, 'asc']
                // ]
            });
        });
    </script>
@endpush
