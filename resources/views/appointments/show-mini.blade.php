<dl>
    <dd>
        <x-patient-profile :patient="$appointment->patient" />
    </dd>
</dl>
<dl>
    <dd class="mt-4">
        <p><strong>Appointment date: </strong> {{ $appointment->appointment_date?->format('Y-m-d h:i A') }} </p>
        <p><strong>Booked by: </strong> {{ $appointment->source }}</p>

        <div class="py-2 flex-center gap-x-2">
            <button class="btn bg-green-500 text-white subcheckin">Check In</button>
            <a href="{{ route('records.appointments.show', $appointment) }}" class="btn bg-blue-500 text-white">View</a>
        </div>
    </dd>
    {{-- @dump($appointment) --}}
</dl>
