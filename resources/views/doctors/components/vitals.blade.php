@if ($visit->svitals)
    <p><b>Taken By: </b> {{ $visit->svitals->recorder?->name }}</p>
    {{-- <p><b>Time: </b>
                        @if (isset($visit->vitals->time))
                            {{ Carbon::parse($visit->vitals->time)->format('Y-m-d h:i A') }}
                        @endif
                    </p> --}}
    <p><b>Weight: </b> {{ $visit->svitals->weight }} kg</p>
    <p><b>Height: </b> {{ $visit->svitals->height }} cm</p>
    <p><b>B/P: </b> {{ $visit->svitals->blood_pressure }} mmHg</p>
    <p><b>Respiration: </b> {{ $visit->svitals->respiratory_rate }} c/m</p>
    <p><b>Pulse: </b> {{ $visit->svitals->pulse }} b/m</p>
    <p><b>Temperature: </b> {{ $visit->svitals->temperature }} &deg;C</p>
@else
    @if ($visit->vitals?->data)
        <p><b>Taken By: </b> {{ $visit->vital_staff?->name }}</p>
        {{-- <p><b>Time: </b>
                                @if (isset($visit->vitals->time))
                                    {{ Carbon::parse($visit->vitals->time)->format('Y-m-d h:i A') }}
                                @endif
                            </p> --}}
        <p><b>Weight: </b> {{ $visit->vitals?->data->weight }} kg</p>
        <p><b>Height: </b> {{ $visit->vitals?->data->height }} cm</p>
        <p><b>B/P: </b> {{ $visit->vitals?->data->blood_pressure }} mmHg</p>
        <p><b>Respiration: </b> {{ $visit->vitals?->data->respiratory_rate }} c/m</p>
        <p><b>Pulse: </b> {{ $visit->vitals?->data->pulse }} b/m</p>
        <p><b>Temperature: </b> {{ $visit->vitals?->data->temperature }} &deg;C</p>
    @else
        Not taken
    @endif
@endif
