@extends('layouts.app')

@section('content')
    <div class="card py px">
        <table id="admissions">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Date Admitted</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("table#admissions").DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ route('api.nursing.admissions') }}",
                },
                columns: [{
                        data: (admission) => admission.in_ward ?
                            `<a href='{{ route('nurses.admissions.show', ':id') }}'>${admission.id}</a>`.replace(
                                ':id', admission.id) : admission.id,
                    },
                    {
                        data: 'patient.name'
                    },
                    {
                        data: ({
                                id,
                                ward
                            }) => ward ? ward.name :
                            `<a href='{{ route('nurses.admissions.assign-ward', ':id') }}'>Assign ward</a>`
                            .replace(':id', id)
                    },
                    {
                        data: ({
                            created_at
                        }) => new Date(created_at).toLocaleString()
                    },
                ]
            });
        });
    </script>
@endpush
