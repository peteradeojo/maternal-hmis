<div class="py px">
    <div class="row">
        <h2 class="my">Vitals</h2>
        <button wire:click='refreshData'>Reload</button>
    </div>
    <table id="patients" class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th></th>
            </tr>
        </thead>
        <tbody wire:model='patients'>
            @foreach ($patients as $i => $patient)
                <tr>
                    <td>{{ $patient->get('name') }}</td>
                    <td>{{ $patient->get('category') }}</td>
                    <td>
                        <a href="{{ route('nurses.patient-vitals', $i + 1) }}">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    {{-- <script>
        var dataTable = new DataTable('#patients');

        Livewire.on('dataUpdated', function() {
            console.log("Message received.");
            if (dataTable) {
                console.log(dataTable);
                dataTable.destroy();
                dataTable = null;
            }

            // Reinitialize DataTable
            dataTable = new DataTable('#patients');
            console.log(dataTable);
        });
    </script> --}}
@endpush
