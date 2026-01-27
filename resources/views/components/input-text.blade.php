@props(['name'])

<input type="text" name="{{ $name }}" {{ $attributes }}>

@error($name)
    <div>
        <span class="error">{{ $message }}</span>
    </div>
@enderror
