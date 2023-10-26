@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header header">{{ $dep->name }}</div>
        <div class="body py">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dep->members as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->phone }}</td>
                            <td><a href="{{ route('it.staff.view', $user) }}">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("table").DataTable();
        });
    </script>
@endpush
