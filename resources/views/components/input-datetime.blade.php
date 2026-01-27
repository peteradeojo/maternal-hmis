@props(['value' => null, 'name'])

<input type="datetime-local" name="{{ $name }}" value="{{ !empty($value) ? $value : date('Y-m-d\TH:i') }}" {{ $attributes }} />
<div>
    @error($name)
        <span class="text-red-500">{{ $message }}</span>
    @enderror
</div>
