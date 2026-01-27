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
            <form action="" method="post" enctype="multipart/form-data"
                class="border-2 p-1 mb-3 grid sm:grid-cols-4 gap-x-4 items-center">
                @csrf
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id" class="form-control">
                        @foreach ($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" required list="categories" />
                    <datalist id="categories">
                        @foreach ($categories as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    {{-- <input type="file" name="products" class="border border-gray-400 p-1"> --}}
                    <input type="file" name="products" class="form-control">
                </div>
                <div class="flex gap-x-2 col-span-2">
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="text" name="amount" class="form-control">
                    </div>
                </div>
                <div class="form-group col-span-full">
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
                        <th>Visible</th>
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
                        data: (row) =>
                            `<a href='#' class='edit-product link' data-id='${row.id}'>${row.name}</a>`
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
                        data: ({is_visible}) => is_visible ? 'Yes' : 'No',
                    }
                ],
            });

            $(document).on('click', '.edit-product', (e) => {
                e.preventDefault();

                axios.get("{{ route('it.show-product', ':id') }}".replace(':id', $(e.currentTarget).data()
                        .id))
                    .then((res) => {
                        useGlobalModal((a) => {
                            a.find(MODAL_TITLE).text('Product Details');
                            a.find(MODAL_BODY).html(res.data);
                        });
                    })
                    .catch((err) => {
                        console.error(err);
                        notifyError(err.message);
                    });
            });
        });
    </script>
@endpushOnce
