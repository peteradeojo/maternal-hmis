@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Audit Trail</h1>
            <a href="{{ route('iam.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left mr-2"></i> Back to IAM
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <table id="audit-logs-table" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Resource</th>
                        <th>ID</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#audit-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('iam.get-audit-logs') }}",
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'user.name', name: 'user.name', defaultContent: 'System' },
                    { data: 'event', name: 'event' },
                    { data: 'auditable_type', name: 'auditable_type' },
                    { data: 'auditable_id', name: 'auditable_id' },
                    {
                        data: 'id',
                        render: function (data, type, row) {
                            return `<button class="btn btn-xs btn-primary view-details" data-id="${data}">View</button>`;
                        }
                    }
                ],
                order: [[0, 'desc']]
            });

            $(document).on('click', '.view-details', function () {
                const id = $(this).data('id');
                // Implement details modal if needed
                alert('Details for log ID: ' + id + '\nCheck console for raw data.');
                console.log($('#audit-logs-table').DataTable().row($(this).parents('tr')).data());
            });
        });
    </script>
@endpush