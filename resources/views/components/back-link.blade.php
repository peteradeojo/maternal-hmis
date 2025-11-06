@props(['to'])

<a href="{{ $to ?? 'javascript:history.back()' }}" class="inline-block link btn mb-4 btn-sm bg-blue-400 text-white">
    < Back</a>
