@extends('layouts.app')

@section('title', 'Access Failures')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Authorization Failures</h1>
            <a href="{{ route('iam.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left mr-2"></i> Back to IAM
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <table id="datalogs-table" class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Action</th>
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
            $('#datalogs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('iam.get-datalogs') }}",
                columns: [
                    { data: ({ created_at }) => parseDateFromSource(created_at, true), name: 'created_at' },
                    { data: 'user.name', name: 'user.name', defaultContent: 'Guest' },
                    { data: 'action', name: 'action' },
                    {
                        data: 'data',
                        render: function (data) {
                            try {
                                const parsed = JSON.parse(data);
                                return parsed.message || 'No message';
                            } catch (e) {
                                return 'Invalid data';
                            }
                        }
                    }
                ],
                order: [[0, 'desc']]
            });
        });
    </script>
@endpush