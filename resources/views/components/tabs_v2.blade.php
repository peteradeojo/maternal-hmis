<!--
  Heads up! 👋

  Plugins:
    - @tailwindcss/forms
-->

@props(['id', 'target', 'options'])

<div x-data="{
    options: @js($options),
    showTab: function(option) {
    this.tabs[`${option}`] = 'active';
    console.log(this.tabs)
    },
    closeTabs: function() { Object.keys(this.tabs).forEach((o, i) => {this.tabs[`${o}`] = 'inactive'});  },
    tabs: {},
}" x-init="options.forEach((o, i) => { tabs[`${o}`] = 'inactive' })" class="tablist" id="{{ $id }}"
    data-tablist="#{{ $target }}" x-id="['tab']">
    {{-- Tabs --}}

    <p x-text="JSON.stringify(tabs)"></p>

    <div x-cloak>
        <div class="sm:hidden">
            <label class="sr-only">Tab</label>

            <select class="w-full rounded-md border-gray-200">
                <template x-for="o in options">
                    <option value="" x-text="o"></option>
                </template>
            </select>
        </div>

        <div class="hidden sm:block">
            <div class="border-b border-gray-300">
                <nav class="-mb-px flex gap-6" aria-label="Tabs">
                    @foreach ($options ?? [] as $i => $option)
                        @if ($i == 0)
                            <a href="#" @click.prevent="closeTabs;showTab('{{ $option }}')"
                                class="shrink-0 border-b-2 px-1 py-2 text-sm font-medium" :class="tabs['{{$option}}'] == 'active' ? 'active-tab' : 'default-tab'"
                                aria-current="page">
                                {{ $option }}
                            </a>
                        @else
                            <a href="#" class="shrink-0 border-b-2 px-1 py-2 text-sm font-medium"
                                @click.prevent="closeTabs;showTab('{{ $option }}')" :class="tabs['{{$option}}'] == 'active' ? 'active-tab' : 'default-tab'">
                                {{ $option }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>
    </div>

    <div id="{{ $target }}">
        {{ $slot }}
    </div>
</div>
