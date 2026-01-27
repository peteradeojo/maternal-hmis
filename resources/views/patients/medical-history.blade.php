<div>
    @forelse ($patient->visits->slice(1,11) as $visit)
        <x-patient-history :visit="$visit">
            <p class="font-semibold">History</p>
            <table class="table" x-data="{ history: @js($visit->histories) }">
                <tr>
                    <th>Presentation</th>
                    <th>Duration</th>
                </tr>
                <template x-if="history.length < 1">
                    <tr>
                        <td colspan="2">No histories</td>
                    </tr>
                </template>
                <template x-for="h in history">
                    <tr>
                        <td x-text="h.presentation"></td>
                        <td></td>
                    </tr>
                </template>
            </table>
        </x-patient-history>
    @empty
        <p>No prior visit recorded.</p>
    @endforelse
</div>
