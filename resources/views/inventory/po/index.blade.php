@extends('layouts.app')

@section('content')
    <div class="card bg-white">
        <p class="basic-header">Purchase Orders</p>

        <a href="{{ route('phm.inventory.new-order') }}" class="btn bg-blue-500 text-white create-order">
            Create Order
            {{-- <i class="fa fa-note"></i> --}}
        </a>

        <table id="table" class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $po)
                    <tr>
                        <td><a href="{{route('phm.inventory.order', $po)}}" data-po="{{ $po->id }}" class="link">{{ $po->po_number }}</a></td>
                        <td>{{ $po->status->name }}</td>
                        <td>{{ $po->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#table").DataTable({
                ordering: false,
            });
        });
    </script>
@endpush
