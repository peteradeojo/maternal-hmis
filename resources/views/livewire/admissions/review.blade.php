<div>
    <div class="py-2" x-data="{ open: true }">
        {{-- <button class="bg-gray-200 rounded-md p-1">Add Note</button> --}}

        <div x-show="open" x-transition>
            <form wire:submit.prevent="save" method="post">
                <div class="form-group">
                    <label>Add Note</label>
                    <textarea wire:model="note" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn bg-red-500 text-white">Submit</button>
            </form>
        </div>
    </div>

    <div class="grid gap-y-2 py-4">
        @forelse ($admission->notes as $note)
            <div class="bg-gray-200 p-2">
                <p>{{ $note->note }}</p>
                <p><small>{{ $note->consultant->name }}</small></p>
                <p><small>{{ $note->created_at->format('Y-m-d h:i A') }}</small></p>
            </div>
        @empty
            <div class="bg-gray-200 p-1 text-center">No review notes</div>
        @endforelse
    </div>
</div>
