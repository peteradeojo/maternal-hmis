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
        $(document).ready(function() {
            $("#table").DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('api.records.admissions') }}"
                },
                columns: [{
                        data: 'patient.name',
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
