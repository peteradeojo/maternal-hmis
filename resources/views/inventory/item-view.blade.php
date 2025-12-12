<div x-data="{
    item: @js($item),
    quantity: 0,
    cost: 0,
    prices: [],
    edit: function({ item, quantity, prices, cost }) {
        axios.post('{{ route('phm.inventory.stock-details', $item) }}', {
                item,
                quantity,
                prices,
                cost
            }).then((res) => {
                console.log(res.data);
            })
            .catch((err) => {
                console.error(err);
            });
    }
}" x-init="cost = item.costs[0].cost;
prices = item.prices;
quantity = item.balance" x-cloak>
    <p class="basic-header">Item Information</p>
    <form @submit.prevent="edit($data)" action="" method="post" x-cloak>
        @csrf
        <div class="form-group">
            <label>Name</label>
            <x-input-text x-model="item.name" class="form-control" name="name" required="required" />
        </div>
        <div class="form-group">
            <label>Code</label>
            <x-input-text x-model="item.sku" class="form-control" name="name" required="required" />
        </div>
        <div class="form-group">
            <label>Description</label>
            <x-input-text x-model="item.description" class="form-control" name="name" required="required" />
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category" x-model="item.category" class="form-control">
                @foreach ($categories as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2">
            <div class="form-group">
                <label>
                    Is Pharmaceutical? <input x-model="item.is_pharmaceutical" type="checkbox" />
                </label>
            </div>
            {{-- <div class="form-group">
                <label></label>
            </div> --}}
        </div>

        <div class="grid grid-cols-3 gap-x-4">
            <div class="form-group">
                <label>Base unit</label>
                <select x-model="item.base_unit" class="form-control">
                    @foreach ($units as $t => $un)
                        <option value="{{ $t }}">{{ $un }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Weight</label>
                <input class="form-control" x-model="item.weight" type="number" step="0.1" />
            </div>
            <div class="form-group">
                <label>Unit Measurement</label>
                <input type="text" class="form-control" x-model="item.si_unit" />
            </div>
        </div>

        <div class="form-group">
            <label>Quantity</label>
            <input type="number" x-model="quantity" class="form-control" />
        </div>

        <p class="basic-header">Price information</p>
        <div class="form-group">
            <label>Unit cost price</label>
            <input type="number" x-model="cost" step="0.0001" class="form-control" />
        </div>
        <button type="button" class="btn bg-blue-400 text-white">Add Selling Price</button>

        <div class="py-2"></div>
        <template x-for="(price, index) in prices">
            <div class="grid grid-cols-2 gap-x-4">
                <label class="col-span-full" x-text="`Price ${index+1}`"></label>
                <div class="form-group">
                    <label>Price profile</label>
                    <select class="form-control">
                        @foreach ($price_types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Selling profile</label>
                    <input type="number" x-model="prices[index].price" id="" step="0.0001"
                        class="form-control">
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-sm bg-red-500 text-white"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </template>

        <button class="btn float-right bg-red-500 text-white">Submit <i class="fa fa-save"></i></button>
    </form>
</div>
