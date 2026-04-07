<!--
  Heads up! 👋

  Plugins:
    - @tailwindcss/forms
-->

@props(['id', 'target', 'options'])

<div x-data id="{{ $id }}" data-tablist="#{{ $target }}">
    <x-tabs :options="array_filter($options)" />

    <div id="{{ $target }}" x-cloak>
        {{ $slot }}
    </div>
</div>
