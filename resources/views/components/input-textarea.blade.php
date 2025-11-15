@props(['name', 'value'])

<textarea name="{{ $name }}" {{ $attributes }} class="form-control">{{ $value ?? '' }}</textarea>

<div>
    @error($name)
        <span class="error">{{ $message }}</span>
    @enderror
</div>
