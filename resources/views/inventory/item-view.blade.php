<div x-data="{ item: @js($item) }">
    <form @submit.prevent="" action="" method="post">
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
            <div class="form-group">
                <label></label>
            </div>
        </div>
    </form>
</div>
