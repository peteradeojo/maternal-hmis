<div class="py-2">
    <table class="table bordered">
        <tbody>
            <tr>
                <td class="font-semibold">Date</td>
                <td>{{ date('Y-m-d h:i A', strtotime($scan->results->date)) }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Clinician</td>
                <td>{{ $scan->results->clinician }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Investigation</td>
                <td>{{ $scan->name }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Age</td>
                <td>{{ $scan->results->age }}</td>
            </tr>
            <tr>
                <td class="font-semibold">LMP</td>
                <td>{{ $scan->results->lmp }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Gravidity</td>
                <td>{{ $scan->results->gravidity }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Parity</td>
                <td>{{ $scan->results->parity }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Report</td>
                <td>{{ $scan->results->report }}</td>
            </tr>
        </tbody>
    </table>
</div>
