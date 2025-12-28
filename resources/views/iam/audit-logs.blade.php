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
                    { data: ({ created_at }) => parseDateFromSource(created_at, true), name: 'created_at' },
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
                // alert('Details for log ID: ' + id + '\nCheck console for raw data.');
                const data = $('#audit-logs-table').DataTable().row($(this).parents('tr')).data();

                useGlobalModal((a) => {
                    a.find(MODAL_TITLE).text('Audit Log Details');
                    a.find(MODAL_CONTENT).html(`
                        <p><strong>Date:</strong> ${data.created_at}</p>
                        <p><strong>User:</strong> ${data.user.name}</p>
                        <p><strong>Event:</strong> ${data.event}</p>
                        <p><strong>Resource:</strong> ${data.auditable_type}</p>
                        <p><strong>ID:</strong> ${data.auditable_id}</p>
                        <p><strong>Details:</strong> ${data.data}</p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${Object.entries(data.new_values).map(([key, value]) => `
                                    <tr>
                                        <td>${key}</td>
                                        <td>${data.old_values[key]}</td>
                                        <td>${value}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    `);
                });
            });
        });
    </script>
@endpush