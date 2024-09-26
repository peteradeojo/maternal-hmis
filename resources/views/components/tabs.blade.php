<!--
  Heads up! 👋

  Plugins:
    - @tailwindcss/forms
-->
<div>
    <div class="sm:hidden">
        <label for="Tab" class="sr-only">Tab</label>

        <select id="Tab" class="w-full rounded-md border-gray-200">
            @foreach ($options ?? [] as $option)
                <option>{{ $option }}</option>
            @endforeach
        </select>
    </div>

    <div class="hidden sm:block">
        <div class="border-b border-gray-300">
            <nav class="-mb-px flex gap-6" aria-label="Tabs">
                @foreach ($options ?? [] as $i => $option)
                    @if ($i == 0)
                        <a href="#" class="shrink-0 border-b-2 px-1 py-2 text-sm font-medium active-tab"
                            aria-current="page">
                            {{ $option }}
                        </a>
                    @else
                        <a href="#" class="shrink-0 border-b-2 px-1 py-2 text-sm font-medium default-tab">
                            {{ $option }}
                        </a>
                    @endif
                @endforeach

            </nav>
        </div>
    </div>
</div>
