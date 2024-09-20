<div wire:poll.5s>
    <p class="mb-1"><a href="#" wire:click='clearAll'>Clear</a></p>

    @forelse ($notifications as $n)
        <div class="card py mb-1 px bg-red">
            <div class="body">
                {!! $n->data['message'] !!}<br>
                <small>{{ $n->created_at->diffForHumans() }}</small>
                <small wire:click="clear('{{ $n->id }}')"><u class="text-white" style="cursor: pointer">Clear</u></small>
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
