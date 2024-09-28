@if ($visit->svitals)
    <p><b>Taken By: </b> {{ $visit->svitals->recorder?->name }}</p>
    <p><b>Weight: </b> {{ $visit->svitals->weight }} kg</p>
    <p><b>Height: </b> {{ $visit->svitals->height }} cm</p>
    <p><b>B/P: </b> {{ $visit->svitals->blood_pressure }} mmHg</p>
    <p><b>Respiration: </b> {{ $visit->svitals->respiratory_rate }} c/m</p>
    <p><b>Pulse: </b> {{ $visit->svitals->pulse }} b/m</p>
    <p><b>Temperature: </b> {{ $visit->svitals->temperature }} &deg;C</p>
@else
    @if ($visit->vitals)
        <p class="mb-1"><b>Attendant: </b> {{ $visit->vitals->recorder?->name }}</p>
        <div class="grid grid-cols-3">
            <p><b>Weight: </b> {{ $visit->vitals->weight }} kg</p>
            <p><b>Height: </b> {{ $visit->vitals->height }} cm</p>
            <p><b>B/P: </b> {{ $visit->vitals->blood_pressure }} mmHg</p>
            <p><b>Respiration: </b> {{ $visit->vitals->respiratory_rate }} c/m</p>
            <p><b>Pulse: </b> {{ $visit->vitals->pulse }} b/m</p>
            <p><b>Temperature: </b> {{ $visit->vitals->temperature }} &deg;C</p>
        </div>
    @else
        Not taken
    @endif
@endif
