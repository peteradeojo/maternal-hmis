<div class="py-2">
    <p class="text-lg font-semibold">{{ $title ?? 'Notes' }}</p>
    @forelse($notes as $note)
        <div class="mb-3 p-3 border rounded">
            <p class="mb-1">{{ $note->note }}</p>
            <small class="text-gray-600">
                By: {{ $note->consultant?->name ?? 'Unknown' }}
                at {{ $note->created_at->format('d M Y H:i') }}
            </small>
        </div>
    @empty
        <p>No records found.</p>
    @endforelse
</div>
