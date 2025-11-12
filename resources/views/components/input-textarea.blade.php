@props(['name'])

<textarea name="{{ $name }}" {{ $attributes }} class="form-control"></textarea>

<div>
    @error($name)
        <span class="error">{{ $message }}</span>
    @enderror
</div>
