@php
    if (!isset($patient)) {
        $patient = null;
    }
@endphp

<div class="grid md:grid-cols-3 gap-x-4">
    <div class="form-group">
        <label for="name">Name *</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') ?? $patient?->name }}"
            required />
    </div>
    <div class="form-group">
        <label for="card_number">Card Number</label>
        <input type="text" name="card_number" id="card_number"
            value="{{ old('card_number') ?? $patient?->card_number }}"
            @isset($patient)
            {{-- readonly --}}
        @endisset
            class="form-control" />
    </div>

    <div class="form-group">
        <label>Category</label>
        <select name="category_id" id="" class="form-control" @readonly(@$mode && @$mode == 'anc')>
            @if (@$mode == 'anc')
                <option value="{{ $ancCategory?->id }}">Antenatal</option>
            @else
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected($category->id == $patient?->category_id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>

<div class="form-group">
    <label for="gender">Gender</label>
    <select name="gender" id="gender" class="form-control" required="required">
        <option value="0" @selected($patient?->gender_value == 'Female')>Female</option>
        <option value="1" @selected($patient?->gender_value == 'Male')>Male</option>
    </select>
</div>
<div class="form-group">
    <label for="dob">Date of Birth</label>
    <input type="date" name="dob" id="dob" class="form-control"
        value="{{ old('dob') ?? $patient?->dob?->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" />
</div>
<div class="form-group">
    <label for="phone">Phone</label>
    <input type="text" name="phone" id="phone" class="form-control"
        value="{{ old('phone') ?? $patient?->phone }}" />
</div>
<div class="form-group">
    <label for="email">E-mail</label>
    <input type="email" name="email" id="email" class="form-control"
        value="{{ old('email') ?? $patient?->email }}" />
</div>
<div class="form-group">
    <label for="address">Address</label>
    <textarea name="address" id="address" class="form-control">{{ old('address') ?? $patient?->address }}</textarea>
</div>
<div class="form-group">
    <label for="poo">Place of Origin</label>
    <input type="text" name="place_of_origin" id="poo" class="form-control"
        value="{{ old('place_of_origin') ?? $patient?->place_of_origin }}" />
</div>
<div class="form-group">
    <label for="tribe">Tribe</label>
    <input type="text" name="tribe" id="tribe" class="form-control"
        value="{{ old('tribe') ?? $patient?->tribe }}" />
</div>
<div class="form-group">
    <label for="marital_status">Marital Status</label>
    <select name="marital_status" id="marital_status" class="form-control">
        <option value="{{ MarriageEnum::Single->value }}" @selected($patient?->marital_status == 'Single')>Single</option>
        <option value="{{ MarriageEnum::Married->value }}" @selected($patient?->marital_status == 'Married')>Married</option>
        <option value="{{ MarriageEnum::Divorced->value }}" @selected($patient?->marital_status == 'Divorced')>Divorced</option>
        <option value="{{ MarriageEnum::Widowed->value }}" @selected($patient?->marital_status == 'Widowed')>Widowed</option>
    </select>
</div>
<div class="form-group">
    <label for="occupation">Occupation</label>
    <input type="text" name="occupation" id="occupation" class="form-control"
        value="{{ old('occupation') ?? $patient?->occupation }}" />
</div>
<div class="form-group">
    <label for="religion">Religion</label>
    <select name="religion" id="religion" class="form-control">
        <option value="1" @if ($patient?->religion == 1) selected @endif>Christianity</option>
        <option value="2" @if ($patient?->religion == 2) selected @endif>Islam</option>
        <option value="3" @if ($patient?->religion == 3) selected @endif>Other</option>
    </select>
</div>
