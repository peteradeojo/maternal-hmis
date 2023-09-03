<div>
    <div wire:poll.2s class="row py">
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

    @if (!in_array($user->department_id, [DepartmentsEnum::DOC->value, DepartmentsEnum::NUR->value]))
        <div class="card py px">
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
                            <th>Visit Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visits as $v)
                            <tr>
                                <td>{{ $v->patient->name }}</td>
                                <td>{{ $v->patient->card_number }}</td>
                                <td>{{ $v->patient->category->name }}</td>
                                <td>{{ $v->patient->gender_value[0] }}</td>
                                <td>{{ $v->getVisitType() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
