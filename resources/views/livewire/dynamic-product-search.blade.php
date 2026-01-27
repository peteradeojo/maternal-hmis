<div>
    {{-- The whole world belongs to you. --}}
    <div class="relative">
        <input type="search" wire:model.debounce.2s="queryString" wire:keyup.debounce.250ms="searchProducts"
            wire:keydown.enter.prevent="select({{ null }})" class="w-full form-control relative"
            wire:blur.debounce.2s="resetResults" wire:focus.debounce.250ms="searchProducts" placeholder="Enter Item" />
        @if ($display)
            <ul class="relative bottom-0 border border-black sp-list max-h-80 overflow-y-auto w-2/3">
                @if (count($results ?? []) > 0)
                    <li class="hover:bg-gray-200 cursor-pointer font-bold text-gray-600">Click an item to Select
                    </li>
                    @forelse ($results  ?? [] as $result)
                        <li class="hover:bg-gray-200 cursor-pointer" wire:click="select({{ $result }})"
                            @click.stop>
                            {{ $result->name }}</li>
                    @empty
                        <li>No Result found</li>
                    @endforelse
                @else
                    <li class="hover:bg-gray-200 cursor-pointer font-bold text-gray-600">Press Enter to use custom
                        selection</li>
                @endif

            </ul>
        @endif

    </div>
</div>
