@extends('layouts.app')
@section('title', 'Inventory')

@section('content')
    <div class="card bg-white">
        <h2 class="header">Inventory</h2>
    </div>
    <div class="p-1"></div>

    <div class="card bg-white">
        <div class="grid grid-cols-6 gap-4">
            <a href="{{ route('phm.inventory.purchases') }}" class="p-2 border-2 border-blue-400 grid place-items-center">
                <p>Purchases <i class="fa fa-dollar"></i></p>
            </a>
            <div class="p-2 border-2 border-blue-400 grid place-items-center">
                <p>Stock History <i class="fa fa-clock text-blue-400"></i></p>
            </div>
            <div class="p-2 border-2 border-blue-400 grid place-items-center">
                <p>Transfer <i class="fa fa-arrow-right text-blue-500"></i></p>
            </div>
            <a href="{{ route('phm.inventory.suppliers') }}" class="p-2 border-2 border-blue-400 grid place-items-center">
                <p>Suppliers <i class="fa fa-arrow-right text-blue-500"></i></p>
            </a>
            <a href="{{ route('phm.inventory.bulk-import') }}" class="p-2 border-2 border-blue-400 grid place-items-center">
                <p>Import <i class="fa fa-gears text-blue-500"></i></p>
            </a>
        </div>
    </div>

    <div class="p-1"></div>

    <div class="card bg-white">
        <div class="flex-center justify-between p-4">
            <p class="card-header">Stock Balance</p>
            <button @click="$dispatch('open-new-item')" class="btn bg-blue-500 text-white">Create <i
                    class="fa fa-plus"></i></button>
        </div>

        <table class="table" id="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <x-overlay-modal title="Add Inventory" id="new-item">
        <form action="#" id="new-stock-form" method="post" x-data="{ in_lot: false, quantity: 0, cost_price: 0, }">
            @csrf
            <p class="basic-header">Item information</p>
            <div class="form-group">
                <label>Name</label>
                <x-input-text name="name" class="form-control" />
            </div>
            <div class="form-group">
                <label>Code</label>
                <x-input-text name="sku" class="form-control" />
            </div>
            <div class="form-group">
                <label>Description</label>
                <x-input-text name="description" class="form-control" />
            </div>
            <div class="form-group">
                <label>Category</label>
                <x-input-select name="category" class="form-control">
                    @foreach ($categories as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </x-input-select>
            </div>
            <div class="form-group sm:flex-center">
                <label class="sm:w-1/3">In Pharmacy?
                    <input type="checkbox" name="is_pharmaceutical" checked />
                </label>
                <label>Requires lot?
                    <input :checked="in_lot" @change="in_lot = $event.target.checked" type="checkbox"
                        name="requires_lot" />
                </label>
            </div>

            <div class="grid grid-cols-3 gap-x-4">
                <div class="form-group">
                    <label>Base unit</label>
                    <x-input-select class="form-control" name="base_unit">
                        <option value="tab">Tablet</option>
                        <option value="bottle">Bottle</option>
                        <option value="satchet">Satchet</option>
                        <option value="ampoule">Ampoule</option>
                        <option value="vial">Vial</option>
                        <option value="bag">Bag</option>
                        <option value="capsule">Capsule</option>
                        <option value="unit">Unit</option>
                    </x-input-select>
                </div>
                <div class="form-group">
                    <label>Weight</label>
                    <x-input-number step="0.1" name="weight" class="form-control" placeholder="e.g. 500" />
                </div>
                <div class="form-group">
                    <label>Unit Measurement</label>
                    <x-input-text placeholder="e.g. mg,ml" name="si_unit" class="form-control" />
                </div>
            </div>

            <template x-if="in_lot">
                <div>
                    <p class="basic-header">Lot Information</p>
                    <div class="form-group">
                        <label>Lot number</label>
                        <x-input-text name="lot_number" class="form-control" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label>Manufacture date</label>
                            <x-input-date name="manufacture_date" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label>Expiry date</label>
                            <x-input-date name="expiry_date" class="form-control" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <x-input-number x-model="quantity" name="quantity_received" class="form-control" required />
                    </div>
                </div>
            </template>

            <template x-if="!in_lot">
                {{-- <p class="basic-header">Quantity</p> --}}
                <div class="form-group">
                    <label>Quantity</label>
                    <x-input-number name="quantity_received" class="form-control" x-model="quantity" />
                </div>
            </template>

            <div>
                <p class="basic-header">Price information</p>
                <div class="form-group">
                    <label>Unit cost price</label>
                    <x-input-number name="unit_cost" step="0.0001" x-model="cost_price" class="form-control" required />
                </div>

                <div x-data="{ types: @js($price_types), selling_prices: [0] }">
                    <button type="button" @click="selling_prices.push(0)" class="btn bg-blue-500 text-white">Add Selling
                        Price</button>

                    <template x-for="(price, index) in selling_prices">
                        <div class="grid grid-cols-2 gap-x-4 py-2">
                            <p class="col-span-full" x-text="'Price ' + (index + 1)"></p>
                            <div class="form-group">
                                <label>Unit Selling price</label>
                                <x-input-select name="" x-bind:name="'prices[' + index + '][price_type]'"
                                    class="form-control">
                                    <template x-for="type in types" :key="type">
                                        <option :value="type" x-text="type"></option>
                                    </template>
                                </x-input-select>
                            </div>
                            <div class="form-group">
                                <label>Unit Selling price</label>
                                <input type='number' step="0.0001" x-bind:name="'prices[' + index + '][price]'" x-model="price"
                                    class="form-control" required />
                            </div>
                            <div class="col-span-full">
                                <button type="button" class="btn btn-sm bg-red-500 text-white"
                                    @click="selling_prices.splice(index, 1)"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="form-group">
                <button class="btn bg-blue-400 text-white" type="submit">Submit <i class="fa fa-save"></i></button>
            </div>
        </form>
    </x-overlay-modal>

    {{-- <x-overlay-modal title="Create purchase order" id="purchase-order">
        <form action="" method="post">
            @csrf
        </form>
    </x-overlay-modal> --}}
@endsection

@push('scripts')
    <script>
        $(document).ready(() => {
            $("#table").DataTable({
                serverSide: true,
                ajax: "{{ route('phm-api.get-inventory') }}",
                columns: [{
                        data: (row) =>
                            `<a href='#' class='link stock-item' data-id="${row.item_id}">${row.item.name}</a>`,
                    },
                    {
                        data: 'item.sku'
                    },
                    {
                        data: (row) => row.prices[0]?.price
                    },
                    {
                        data: 'item.balance'
                    },
                    {
                        data: 'location.name'
                    },
                ],
                responsive: true,
            });

            asyncForm("#new-stock-form", "{{ route('phm.inventory.index') }}", (e, data) => {
                console.log(data);
                dispatchEvent(new CustomEvent('close-new-item'));
                e.target.reset();
            });

            $(document).on('click', ".stock-item", function(e) {
                e.preventDefault();

                const id = $(this).data('id');

                useGlobalModal((a) => {
                    a.find(MODAL_TITLE).text('Stock details');

                    axios.get("{{ route('phm.inventory.stock-details', ':id') }}".replace(':id',
                        id)).then((res) => {
                        a.find(MODAL_CONTENT).html(res.data);
                    }).catch((err) => {
                        a.find(MODAL_CONTENT).html(err.message);
                    })
                });
            });
        });
    </script>
@endpush
