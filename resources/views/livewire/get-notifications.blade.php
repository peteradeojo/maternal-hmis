<div wire:poll.5s>
    <p class="mb-1"><a class="text-blue-700 underline" href="#" wire:click='clearAll'>Clear</a></p>

    @forelse ($notifications as $n)
        <div class="p-2 mb-2 rounded bg-red-500">
            <div class="body text-sm">
                {!! $n->data['message'] !!}<br>
                <small>{{ $n->created_at->diffForHumans() }}</small>
                <small wire:click="clear('{{ $n->id }}')"><u class="text-white"
                        style="cursor: pointer">&times;</u></small>
            </div>
        </div>
    @empty
        <div class="card px">
            <div class="body">
                No notifications
            </div>
        </div>
    @endforelse
</div>
