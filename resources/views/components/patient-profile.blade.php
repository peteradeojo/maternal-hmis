<p class="text-lg font-semibold pb-2">{{ $patient->name }} #{{ $patient->card_number }}</p>
<div class="grid sm:grid-cols-3 gap-x-4">
    <div>
        <p><b>Category:</b> {{ $patient->category->name }}</p>
        <p><b>Gender:</b> {{ $patient->gender }}</p>
        <p><b>Phone number:</b> {{ $patient->phone }}</p>
    </div>
    <div>
        <p><b>Date of birth:</b> {{ $patient->dob?->format('Y-m-d') }}</p>
        <p><b>Age: </b> {{ $patient->getAge() }}</p>
    </div>
    @if ($insurance = $patient->insure)
        <div
            class="p-4 {{ auth()->user()->hasRole(['doctor', 'nurse'])? 'motion-safe:animate-bounce': '' }} flex gap-x-8 items-center bg-green-200">
            <div>
                @isset($insurance->organization)
                    @if ($insurance->organization?->is_public)
                        <i class="text-3xl fa fa-shield-halved"></i>
                        <p class="">Public</p>
                    @else
                        <i class='text-3xl fa fa-user-shield'></i>
                        <p class="">Private</p>
                    @endif
                @else
                    <i class="text-3xl fa fa-shield"></i>
                @endisset
            </div>


            <div>
                <p>
                    <b>Insurance:</b>
                    <span>{{ $insurance->hmo_name ?? 'No insurance' }}</span>
                </p>
                <p>
                    <b>Insurance number:</b>
                    <span>{{ $insurance->hmo_id_no ?? 'No insurance' }}</span>
                </p>
            </div>
        </div>
    @endif
</div>
{{ $slot }}
