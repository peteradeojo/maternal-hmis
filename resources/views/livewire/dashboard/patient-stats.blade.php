<div>
    <div class="row py">
        <div class="col">
            <div class="card">
                <div class="header card-header">
                    {{ $patients }}
                </div>
                <div class="footer">
                    Patients
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="header card-header">
                    {{ $patientsToday }}
                </div>
                <div class="footer">
                    Patients Today
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="header card-header">
                    {{ $currentAdmissions }}
                </div>
                <div class="footer">
                    Admissions
                </div>
            </div>
        </div>
    </div>

    @if (in_array($user->department_id, [DepartmentsEnum::REC->value]))
        <div class="card py px mb-1">
            <div class="card-header">
                Waiting Patients
            </div>
            <div class="body py">
                <table class="table" id="waitlist-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Card Number</th>
                            <th>Category</th>
                            <th>Gender</th>
                            <th>Wait time</th>
                            <th>Visit Type</th>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visits as $v)
                            <tr>
                                <td>{{ $v->patient->name }}</td>
                                <td>{{ $v->patient->card_number }}</td>
                                <td>{{ $v->patient->category->name }}</td>
                                <td>{{ $v->patient->gender_value[0] }}</td>
                                <td>{{ $v->created_at->diffForHumans(syntax: 1) }}</td>
                                <td>{{ $v->getVisitType() }}</td>
                                <td>
                                    @if ($user->department_id == DepartmentsEnum::REC->value)
                                        <a href="{{ route('records.force-check-out', $v) }}?force">Check out</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        let table = new DataTable(document.querySelector("#waitlist-table"));
        document.addEventListener('livewire:load', function() {
            console.log("Livewire loaded");
            console.log(Livewire);
            Livewire.on('reinitialize-datatable', function() {
                console.log("Livewire loaded");
                // Reinitialize DataTables here
                // $('#waitlist-table').DataTable().destroy();
                // $('#waitlist-table').DataTable();
                table.destroy();
                console.log(table);
                table = new DataTable(document.querySelector("#waitlist-table"));
            });
        });
    </script>
@endpush
