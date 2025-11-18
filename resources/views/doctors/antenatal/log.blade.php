<div class="grid md:grid-cols-2">
    <div class="grid gap-y-2">
        <p><b>Date:</b> {{ $visit->created_at->format('Y-m-d h:i A') }}</p>
        <p><b>EGA:</b> {{ $profile->maturity($visit->created_at) }}</p>
        <p><b>Height of fundus:</b> {{ $visit->fundal_height }} </p>
        <p><b>Presentation:</b> {{ $visit->presentation }} </p>
        <p><b>Relations of presenting part to birth:</b> {{ $visit->presentation_relationship }} </p>
        <p><b>Foetal Heart Rate:</b> {{ $visit->fetal_heart_rate }} </p>
        <p><b>Oedema:</b> {{ $visit->edema }} </p>

        <p class="flex items-center">
            <span class="flex-grow">IPT: <i class="fa {{ $visit->ipt ? 'fa-square-check' : 'fa-cancel' }}"></i></span>
            <span class="flex-grow">TT: <i class="fa {{ $visit->tt ? 'fa-square-check' : 'fa-cancel' }}"></i></span>
        </p>

        <p><b>Comment:</b> {{ $visit->note }}</p>
        <p>Next appointment date: {{ $visit->return_visit }}</p>
    </div>

    <div>
        <div class="grid gap-y-2">
            <p class="text-lg font-semibold">Vitals</p>
            <p><b>Weight:</b> {{ $visit->visit->vitals?->weight }} kg</p>
            <p><b>Temperature:</b> {{ $visit->visit->vitals?->temperature }} &deg;C</p>
            <p><b>Blood Pressure:</b> {{ $visit->visit->vitals?->blood_pressure }} mmHg</p>
        </div>

        <div class="grid gap-y-2">
            <p class="font-semibold pt-2 text-lg">Tests</p>
            <p><b>PCV:
                    {{ $visit->visit->getTestResults('PCV', 'PCV') ?? ($visit->first_visit ? $visit->visit->profile->getTestResult('PCV', 'PCV') : 'No result') }}
                </b></p>
            <p><b>Urinalysis: </b></p>
        </div>
    </div>
</div>
