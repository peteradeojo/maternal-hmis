<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="card py-1 px-1">
        <p class="text-xl bold">Waiting</p>
        <div class="py-3"></div>

        <table class="table" id="waitlist">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Card Number</th>
                    <th>Category</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waitlist as $visit)
                    <tr>
                        <td>{{ $visit->patient->name }}</td>
                        <td>{{ $visit->patient->card_number }}</td>
                        <td>{{ $visit->patient->category->name }}</td>
                        <td>{{ $visit->created_at?->format('Y-m-d h:i A') }}</td>
                        <td><a href="{{ route('rad.scans', ['patient_id' => $visit->patient_id]) }}"
                                class="text-blue-600 hover:underline">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@script
    <script>
        $("#waitlist").DataTable({
            responsive: true,
            ordering: false
        });
    </script>
@endscript
