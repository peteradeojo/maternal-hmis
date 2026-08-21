<div class="row">
    <div class="col-3">
        <div class="form-group">
            <label for="hmo_name">HMO Name</label>
            <input type="text" @isset($profile) value="{{ $profile->hmo_name }}" @endisset
                name="hmo_name" id="hmo_name" class="form-control">
        </div>
    </div>
    <div class="px-1"></div>
    <div class="col-3">
        <div class="form-group">
            <label>Company</label>
            <input type="text" @isset($profile) value="{{ $profile->hmo_company }}" @endisset
                name="hmo_company" class="form-control">
        </div>
    </div>
    <div class="px-1"></div>
    <div class="col-3">
        <div class="form-group">
            <label>ID Number</label>
            <input type="text" @isset($profile) value="{{ $profile->hmo_id_no }}" @endisset
                name="hmo_id_no" class="form-control">
        </div>
    </div>
    <div class="col-3 grid grid-cols-2 gap-x-4">
        <label class="col-span-2">Validity Period</label>
        <div class="form-group">
            <label>From</label>
            <input type="date" @isset($profile) value="{{ $profile->validity_from }}" @endisset
                name="validity_from" class="form-control">
        </div>
        <div class="form-group">
            <label>To</label>
            <input type="date" @isset($profile) value="{{ $profile->validity_to }}" @endisset
                name="validity_to" class="form-control">
        </div>
    </div>
    @isset($profile)
        <div class="col-3">
            <label>Status</label><br>
            <select name="status" class="form-control">
                <option @selected($profile?->status == Status::pending) value="{{ Status::pending->value }}">Pending</option>
                <option @selected($profile?->status == Status::active) value="{{ Status::active->value }}">Active</option>
                <option @selected($profile?->status == Status::blocked) value="{{ Status::blocked->value }}">Blocked</option>
                <option @selected($profile?->status == Status::cancelled) value="{{ Status::cancelled->value }}">Cancelled</option>
            </select>
        </div>
    @endisset
</div>
