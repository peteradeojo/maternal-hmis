<div>
    <form action="{{ route('records.new-anc', $patient) }}" method="post">
        @csrf
        <div class="form-group">
            <label for="cardType">Card Type</label>
            <select name="card_type" id="cardType" class="form-control">
                <option value="1">Bronze</option>
                <option value="2">Silver</option>
                <option value="3">Gold</option>
                <option value="4">Diamond</option>
                <option value="5">Platinum</option>
                <option value="6">Limited</option>
                <option value="7">Gold Plus</option>
                <option value="8">Diamond Plus</option>
            </select>
        </div>
        <div class="mb-2">
            <h2 class="text-2xl bold">Spouse</h2>
            @include('records.components.spouse-form')
        </div>
        <div class="py"></div>
        <div class="form-group flex justify-end">
            <button class="btn btn-blue">Save <i class="fa fa-save"></i></button>
        </div>
    </form>
</div>
