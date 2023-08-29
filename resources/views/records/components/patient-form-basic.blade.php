<div class="form-group">
    <label for="card_number">Card Number</label>
    <input type="text" name="card_number" id="card_number" value="{{ old('card_number') }}" class="form-control" />
</div>
<div class="form-group">
    <label for="name">Name *</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required />
</div>
<div class="form-group">
    <label for="gender">Gender</label>
    <select name="gender" id="gender" class="form-control" required="required">
        <option value="0">Female</option>
        <option value="1">Male</option>
    </select>
</div>
<div class="form-group">
    <label for="dob">Date of Birth</label>
    <input type="date" name="dob" id="dob" class="form-control" value="{{ old('dob') }}"
        max="{{ date('Y-m-d') }}" />
</div>
<div class="form-group">
    <label for="phone">Phone</label>
    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" />
</div>
<div class="form-group">
    <label for="email">E-mail</label>
    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" />
</div>
<div class="form-group">
    <label for="address">Address</label>
    <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
</div>
<div class="form-group">
    <label for="poo">Place of Origin</label>
    <input type="text" name="place_of_origin" id="poo" class="form-control"
        value="{{ old('place_of_origin') }}" />
</div>
<div class="form-group">
    <label for="tribe">Tribe</label>
    <input type="text" name="tribe" id="tribe" class="form-control" value="{{ old('tribe') }}" />
</div>
<div class="form-group">
    <label for="marital_status">Marital Status</label>
    <select name="marital_status" id="marital_status" class="form-control">
        <option value="1">Married</option>
        <option value="2">Single</option>
        <option value="3">Divorced</option>
        <option value="4">Widowed</option>
    </select>
</div>
<div class="form-group">
    <label for="occupation">Occupation</label>
    <input type="text" name="occupation" id="occupation" class="form-control" value="{{ old('occupation') }}" />
</div>
<div class="form-group">
    <label for="religion">Religion</label>
    <select name="religion" id="religion" class="form-control">
        <option value="1">Christianity</option>
        <option value="2">Islam</option>
        <option value="3">Other</option>
    </select>
</div>
