@extends('layouts.app')

@section('content')
    <div class="container grid gap-y-4">
        <div class="card p-4 bg-white">
            <p class="basic-header">Stock Takes</p>

            <a href="{{ route('phm.inventory.new-stock-take') }}" class="btn bg-blue-400 text-white">Start Stock Take</a>
        </div>


        <div class="card p-4 bg-white">
            <table class="table" id="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Initiated by</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($counts as $tk)
                        <tr>
                            <td><a href="{{ route('phm.inventory.stock-count', $tk) }}" class="link">{{ $tk->id }}</a>
                            </td>
                            <td>{{ $tk->created_at }}</td>
                            <td>{{ $tk->counter->name }}</td>
                            <td>{{ ucfirst($tk->status->name) }}</td>
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
            $("#table").DataTable();
        });
    </script>
@endpush
