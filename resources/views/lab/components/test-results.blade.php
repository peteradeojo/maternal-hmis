@foreach ($tests as $test)
    <table class="table">
        <thead>
            <tr>
                <td colspan="4">
                    <p>By: <b>{{ $test->staff?->name }}</b></p>
                    <p class="text-xs">{{ $test->updated_at->format('Y-m-d h:i A') }}</p>
                </td>
            </tr>
            <tr class="border-t">
                <th>Name</th>
                <th>Result</th>
                <th>Unit</th>
                <th>Ref. range</th>
            </tr>
        </thead>
        <tbody>
            <tr wire:key="test:{{ $test->name }}">
                <td colspan="3"><b>{{ $test->name }}</b></td>
                <td class="text-xs">
                    @if (@$cancellable === true && ($test->results == null && $test->name != 'ROUTINE ANTENATAL TESTS'))
                        <button class="btn text-red-500 underline" wire:click="removeTest({{ $test->id }})">Cancel
                            Request</button>
                    @endif
                </td>
            </tr>
            @forelse ($test->results ?? [] as $result)
                <tr wire:key="test:{{ $test->name }}">
                    <td>{{ $result->description }}</td>
                    <td>{{ $result->result }}</td>
                    <td>{{ $result->unit }}</td>
                    <td>{{ $result->reference_range }}</td>
                </tr>
            @empty
                <tr wire:key="test:{{ $test->name }}">
                    <td colspan="4">No result provided.</td>
                </tr>
            @endforelse
        </tbody>

        <tfoot class="border-t">
            <tr>
                <td colspan="4">
                    <button class="btn btn-sm btn-blue px-4" onclick="() => printElement(this.closest('table'))">Print</button>
                </td>
            </tr>
        </tfoot>
    </table>
@endforeach
