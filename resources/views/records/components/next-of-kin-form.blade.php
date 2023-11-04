<div class="form-group">
    <label for="nok_name">Next of Kin Name</label>
    <input type="text" name="nok_name" id="nok_name" class="form-control" value="{{ $patient?->nok_name }}" />
</div>
<div class="form-group">
    <label for="nok_phone">Next of Kin Phone</label>
    <input type="text" name="nok_phone" id="nok_phone" class="form-control" value="{{ $patient?->nok_phone }}" />
</div>
<div class="form-group">
    <label for="nok_address">Next of Kin Address</label>
    <textarea name="nok_address" id="nok_address" class="form-control">{{ $patient?->nok_address }}</textarea>
</div>
