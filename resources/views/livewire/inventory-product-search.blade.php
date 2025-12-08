<div x-data="{ open: false }"
 @click.outside="open = false"

 @searched="$event.detail[0] > 0 ? open = true : open = false"
 >
    {{-- <form wire:submit.prevent="search" class="relative"> --}}
        <div class="form-group">
            <label>Search</label>

            <small class="float-end"><i class="fa fa-info"></i> Press enter to search</small>
            <input type="search" placeholder="Item name, code, description" class="form-control" wire:model="queryString"
                {{-- minlength="2"  --}}
            {{-- @focus="open = true" --}}
            {{-- @blur="open = false" --}}
            x-on:keydown.enter.prevent="$wire.search()"
             />

            <ul x-show="open" class="relative bottom-0 border border-black sp-list max-h-80 overflow-y-auto w-2/3">
                @forelse ($results as $result)
                    <li @click="$wire.selected('{{ $result->id }}')" class="p-1 hover:bg-gray-300">{{ $result->name }}
                    </li>
                @empty
                    <li>No result found</li>
                @endforelse
            </ul>
        </div>

    {{-- </form> --}}

</div>
