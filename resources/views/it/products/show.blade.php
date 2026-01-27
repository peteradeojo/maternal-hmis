<div>
    <form action="{{route('it.show-product', $product)}}" method="post">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" value="{{ $product->name }}" name="name" class="form-control">
        </div>
        <div class="form-group">
            <label>Description</label>
            <input type="text" value="{{ $product->description }}" name="description" class="form-control" />
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="product_category_id" class="form-control">
                @foreach ($categories as $option)
                    <option value="{{ $option->id }}" @match($option->id, $product->product_category_id) selected="true"
                        @endmatch>{{ $option->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" value="{{$product->amount}}" name="amount" step="0.01" class="form-control">
        </div>
        <div class="form-group">
            <label>Visible?
                <input type="checkbox" name="is_visible" checked="{{boolVal($product->is_visible) === true ? 'true' : 'false'}}" />
            </label>
        </div>
        <div class="form-group">
            <button class="btn bg-red-400 text-white">Submit <i class="fa fa-save"></i></button>
        </div>
    </form>
</div>
