@extends('layouts.app')

@section('title', 'Products')

@section('content')
    @if (session()->has('success'))
        <p class="text-blue">Upload successful</p>
    @endif

    @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
    @endforeach

    <div class="card">
        <p class="header">Products</p>
        <div class="body py-4">
            <form action="" method="post" enctype="multipart/form-data" class="border-2 p-1 mb-3">
                @csrf
                <div class="form-group">
                    <label for="">Category</label>
                    <input type="text" name="category" id="" required list="categories" />
                    <datalist id="categories">
                        @foreach ($categories as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    <label for="">Department</label>
                    <select name="department_id" id="">
                        @foreach ($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <input type="file" name="products" class="border border-gray-400 p-1">
                </div>
                <div class="flex">
                    <div class="form-group">
                        <label for="">name</label>
                        <input type="text" name="name" id="">
                    </div>
                    <div class="form-group">
                        <label for="">amount</label>
                        <input type="text" name="amount" id="">
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn bg-blue-600 text-white">Submit</button>
                </div>
            </form>

            <table id="table" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Category</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script defer>
        $(() => {
            $("#table").DataTable({
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('it.get-products') }}",
                    dataSrc: 'data'
                },
                responsive: true,
                orderable: false,
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'category.name'
                    },
                    {
                        data: (row) => ``
                    },
                ],
            });
        });
    </script>
@endpushOnce
