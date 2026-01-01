<div class="grid gap-y-4">
    <div class="grid md:grid-cols-3 gap-y-2 gap-x-3">
        <x-card>
            <x-slot:title>
                <p class="font-semibold text-4xl">{{ $patients }}</p>
            </x-slot:title>

            <x-slot:footer>
                @role(['record', 'admin'])
                    <a href="{{ route('records.patients') }}" class="link">Patients &rarr;</a>
                @else
                    <p>Patients</p>
                @endrole
            </x-slot:footer>
        </x-card>

        <x-card border="green-500" color="green-500">
            <x-slot:title>
                <p class="font-semibold text-4xl">{{ $patientsToday }}</p>
            </x-slot:title>

            <x-slot:footer>
                <p>Patients Today</p>
            </x-slot:footer>
        </x-card>

        <x-card color="purple-700">
            <x-slot:title>
                <p class="font-semibold text-4xl">{{ $currentAdmissions }}</p>
            </x-slot:title>

            <x-slot:footer>
                @can('view admissions')
                    @role(['nurse'])
                        <a href="{{ route('nurses.admissions.get') }}" class="hover:underline">Current Admissions &rarr;</a>
                        @elserole(['record'])
                        <a href="{{ route('records.admissions') }}" class="hover:underline">Current Admissions &rarr;</a>
                        @elserole(['doctor'])
                        <a href="{{ route('doctor.admissions') }}" class="hover:underline">Current Admissions &rarr;</a>
                        @elserole(['billing', 'pharmacy'])
                        <a href="{{ route('phm.admissions') }}" class="hover:underline">Current Admissions &rarr;</a>
                    @else
                        <p>Current admissions</p>
                    @endrole
                @endcan
            </x-slot:footer>
        </x-card>

        @can('view bills')
            <x-card border="green-500" color="green-500">
                <x-slot:title>
                    <span class="font-medium text-4xl">{{ $stats['pendingBills'] }}</span>
                </x-slot:title>
                <x-slot:footer>
                    <a class="hover:underline" href="{{ route('billing.index') }}">Pending Bills &rarr;</a>
                </x-slot:footer>
            </x-card>
        @endcan
    </div>

    @role('record')
        <div class="card py px mb-1">
            <div class="card-header">
                Waiting Patients
            </div>
            <div class="body py">
                <x-datatables id="waitlist-table">
                    <x-slot:thead>
                        <tr>
                            <th>Name</th>
                            <th>Card Number</th>
                            <th>Category</th>
                            <th>Gender</th>
                            <th>Checked In</th>
                            <th>Visit Type</th>
                            <td></td>
                        </tr>
                    </x-slot:thead>
                    <x-slot:tbody>
                        @foreach ($visits as $v)
                            <tr>
                                <td><a href="{{ route('records.patient', $v->patient) }}"
                                        class="link">{{ $v->patient->name }}</a></td>
                                <td>{{ $v->patient->card_number }}</td>
                                <td>{{ $v->patient->category->name }}</td>
                                <td>{{ $v->patient->gender_value[0] }}</td>
                                <td>{{ $v->created_at->format('Y-m-d h:iA') }}</td>
                                <td>{{ $v->readable_visit_type }}</td>
                                <td>
                                    <a class="btn btn-sm bg-green-400"
                                        href="{{ route('billing.patient-bills', ['patient' => $v->patient_id]) }}">Check
                                        out</a>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot:tbody>
                </x-datatables>

            </div>
        </div>
    @endrole
</div>

@push('scripts')
    <script>
        let table = $("#waitlist-table").DataTable({
            responsive: true,
            order: [
                [4, 'desc']
            ]
        });
    </script>
@endpush
