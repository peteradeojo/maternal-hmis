@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="flex-center justify-between py-2">
            <p class="basic-header">Suppliers</p>
            <button @click="$dispatch('open-suppliers')" class="btn bg-blue-400 text-white">Add <i
                    class="fa fa-plus"></i></button>
        </div>

        <table class="table" id="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $sup)
                    <tr>
                        <td>{{ $sup->name }}</td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-overlay-modal id="suppliers">
        <form action="" method="post">
            @csrf
            <div class="form-group">
                <label>Name</label>
                <x-input-text name="name" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Address</label>
                <x-input-text name="address" class="form-control" />
            </div>
            <div class="form-group">
                <label>Tel. No</label>
                <x-input-text name="tel" class="form-control" />
            </div>
            <div class="form-group">
                <label>E-mail Address</label>
                <x-input-text name="email" class="form-control" />
            </div>
            <div class="form-group">
                <button class="btn bg-blue-400 text-white">Submit</button>
            </div>
        </form>
    </x-overlay-modal>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#table").DataTable();
        })
    </script>
@endpush
