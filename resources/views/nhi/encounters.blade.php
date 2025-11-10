@extends('layouts.app')
@section('title', 'Patient Encounters')

@section('content')
    <div class="p-4 bg-white">
        <table id="table" class="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Card Number</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#table").DataTable({
                serverSide: true,
                ajax: "{{ route('api.nhi.visits') }}",
                columns: [{
                        data: ({
                                id,
                                patient
                            }) =>
                            `<a href='#' class='link visit-link' data-id='${id}'>${patient.name}</a>`
                    },
                    {
                        data: 'patient.card_number'
                    },
                    {
                        data: ({created_at}) => new Date(created_at).toLocaleString('en-CA')
                    },
                    {
                        data: ({
                                patient
                            }) =>
                            patient.insurance && patient.insurance?.length < 1 ? "No insurance" :
                            patient.insurance[0]
                            .hmo_name,
                        name: 'insurance',
                        ordering: true
                    },
                ],
                ordering: false,
            });

            $(document).on('click', '.visit-link', (e) => {
                const {id} = $(e.currentTarget).data();
                useGlobalModal((a) => {
                    a.find(MODAL_TITLE).text('Patient Encounter');

                    axios.get("{{route('nhi.show-encounter', ':id')}}".replace(':id', id))
                    .then(({data}) => {
                        a.find(MODAL_BODY).html(data);
                    })
                    .catch((err) => {
                        console.error(err);
                        a.find(MODAL_BODY).html(`<p class='text-red-500 font-semibold text-2xl tracking-wide
                        '>Oops</p>`);
                    });
                });
            });
        });
    </script>
@endpush
