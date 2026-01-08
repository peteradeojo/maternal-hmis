@props(['name'])

<input type="number" name="{{ $name }}" {{ $attributes }} />
@error($name)
    <div>
        <span class="text-red-500">{{ $message }}</span>
    </div>
@enderror
