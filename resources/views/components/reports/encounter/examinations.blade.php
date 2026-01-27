<div class="py-2">
    <p class="text-lg font-semibold">Physical Examinations</p>
    @if ($visit->examination)
        <div class="mb-2">
            <strong>General:</strong>
            <p>{{ $visit->examination->general }}</p>
        </div>

        @if ($visit->examination->specifics)
            <div class="mb-2">
                <strong>Specifics:</strong>
                <ul class="list-disc pl-5">
                    @foreach ($visit->examination->specifics as $key => $value)
                        <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @else
        <p>No examinations recorded.</p>
    @endif
</div>
