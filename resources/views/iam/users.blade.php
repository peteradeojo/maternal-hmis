@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
    <div class="container card rounded-md p-1">
        <x-datatables id="users">
            <x-slot:thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th></th>
                </tr>
            </x-slot:thead>
            <x-slot:tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->department->name }}</td>
                        <td>
                            <a href="{{ route('iam.manage-user', $user) }}" class="link">Manage</a>
                        </td>
                    </tr>
                @endforeach
            </x-slot:tbody>
        </x-datatables>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#users").DataTable();
        });
    </script>
@endpush
