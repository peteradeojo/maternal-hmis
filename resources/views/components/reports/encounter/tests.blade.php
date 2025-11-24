<div class="py-2">
    <p class="text-lg font-semibold">Test Results</p>

    @include('doctors.components.test-results', [
        'tests' => $visit->tests->merge($visit->visit->tests),
        'cancellable' => false,
    ])
</div>
