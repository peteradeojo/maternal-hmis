<div wire:poll.1s>
    <p class="mb-1"><a href="#" wire:click='clearAll'>Clear</a></p>

    @forelse ($notifications as $n)
        <div class="card py px bg-red">
            <div class="body">
                {{ $n->data['message'] }}
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
