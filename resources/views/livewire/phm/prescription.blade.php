<div wire:poll.5s>
    <button wire:click="$refresh">Reload</button>
    <table class="table table-list">
        <thead>
            <tr>
                <th>Prescription</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Amount</th>
                <th>Available</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($doc->treatments as $t)
                <tr>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->dosage }}</td>
                    <td>{{ $t->frequency }}</td>
                    <td>{{ $t->duration }}</td>
                    <td>{{ $t->amount !== null ? $t->amount : 'Not Responded' }}</td>
                    <td>{{ $t->available !== null ? ($t->available ? 'Yes' : 'No') : 'Not Responded' }}</td>
                </tr>
                @php
                    $total += $t->available ? $t->amount ?? 0 : 0;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td align="right">NGN</td>
                <td align="right"><b>{{ number_format($total, 2) }}</b></td>
            </tr>
            <tr>
                <td>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
