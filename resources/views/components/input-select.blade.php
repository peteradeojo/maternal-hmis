@props(['name'])

<select name="{{ $name }}" class="form-control" {{$attributes}}>
    {{ $slot }}
</select>

<div>
    @error($name)
        <span class="error">{{ $message }}</span>
    @enderror
</div>
