<div class="py-2">
    <p class="text-lg font-semibold">Consultation Notes</p>
    @forelse($visit->notes as $note)
        <div class="mb-3 p-3 border rounded">
            <p class="mb-1">{{ $note->note }}</p>
            <small class="text-gray-600">
                By: {{ $note->consultant?->name ?? 'Unknown' }}
                at {{ $note->created_at->format('d M Y H:i') }}
            </small>
        </div>
    @empty
        <p>No notes recorded.</p>
    @endforelse
</div>
