@extends('layouts.app')
@section('title', 'Billing')

@section('content')
    <div class="bg-white p-2">
        <table id="table" class="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Card number</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(() => {
            $("#table").DataTable({
                serverSide: true,
                ajax: "{{ route('billing.get-billable-patients') }}",
                columns: [{
                        data: (row) =>
                            `<a href='{{ route('billing.patient-bills', ':id') }}' class='link'>${row.patient.name}</a>`
                            .replace(':id', row.patient_id)
                    },
                    {
                        data: 'patient.card_number'
                    },
                    {
                        data: (row) => new Date(row.created_at).toLocaleDateString('en-CA', {
                            minute: '2-digit',
                            hour: '2-digit',
                        })
                    },
                ],
                ordering: false,
            });
        });
    </script>
@endpush
