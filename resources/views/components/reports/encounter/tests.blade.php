<div class="py-2">
    <p class="text-lg font-semibold">Test Results</p>
    {{-- @dump($visit::class) --}}

    @include('doctors.components.test-results', [
        'tests' => match ($visit::class) {
            'App\Models\AdmissionPlan' => $visit->tests,
            'App\Models\Admission' => $visit->tests->merge($visit->plan?->tests),
            default => $visit->tests->merge($visit->visit?->tests),
        },
        'cancellable' => false,
    ])
</div>
