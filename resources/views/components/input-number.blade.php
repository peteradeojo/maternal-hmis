@props(['name'])

<input type="number" name="{{ $name }}" {{ $attributes }} />
<div>
    @error($name)
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</div>
