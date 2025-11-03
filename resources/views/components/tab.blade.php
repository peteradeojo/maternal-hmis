<p x-text="'{{$option}} ' + $data['tabs']['{{$option}}']"></p>
<div x-modal="$data['tabs']['{{$option}}']" class="tab" x-show="$data['tabs']['{{$option}}'] == 'active'">
    {{ $slot }}
</div>
