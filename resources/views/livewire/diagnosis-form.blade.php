<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <div class="form-group">
        <label>Diagnosis</label>
        <input type="text" wire:model.live.debounce.300ms="query" placeholder="Enter your diagnosis" name="diagnosis"
            class="form-control" />
    </div>
    <span class="hidden" wire:loading.class.remove="hidden" wire:target="query">
        {{-- <span wire:loading wire:target="query"> --}}
        <x-spinner />
    </span>
    @if (count($results) > 0)
        <ul class="border  max-h-[300px] overflow-y-scroll">
            @foreach ($results as $row)
                <li class="p-1 hover:bg-gray-400 hover:cursor-pointer" wire:click="addDiagnosis({{ $row['id'] }})">
                    {{ $row['code'] }} - {{ $row['short_description'] }}
                </li>
            @endforeach
        </ul>
    @endif
    <div class="form-group">
        <button class="btn bg-blue-500 text-white">Submit</button>
    </div>
</div>
