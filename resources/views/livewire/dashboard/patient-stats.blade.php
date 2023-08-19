<div wire:poll.2s>
    <div class="row py">
        <div class="col">
            <div class="card">
                <div class="header">
                    {{ $patients }}
                </div>
                <div class="footer">
                    Patients
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="header">
                    {{ $patientsToday }}
                </div>
                <div class="footer">
                    Patients Today
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="header">
                    {{ $currentAdmissions }}
                </div>
                <div class="footer">
                    Admissions
                </div>
            </div>
        </div>
    </div>
</div>
