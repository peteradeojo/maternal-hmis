<div class="grid sm:grid-cols-2">
    <p class="text-lg font-semibold col-span-full pb-2">{{ $patient->name }} #{{ $patient->card_number }}</p>
    <p><b>Category:</b> {{ $patient->category->name }}</p>
    <p><b>Gender:</b> {{ $patient->gender }}</p>
    <p><b>Phone number:</b> {{ $patient->phone }}</p>
    <p><b>Date of birth:</b> {{ $patient->dob?->format('Y-m-d') }}</p>
    <p><b>Age: </b> {{ $patient->getAge() }}</p>
</div>
@if ($insurance = $patient->insure)
    <div class="pt-4 text-lg flex flex-reverse gap-x-8 items-center ">
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

        <div>
            @isset($insurance->organization)
                @if ($insurance->organization?->is_public)
                    <i class="text-4xl fa fa-shield-halved"></i>
                    <p class="">Public</p>
                @else
                    <i class='text-4xl fa fa-user-shield'></i>
                    <p class="">Private</p>
                @endif
            @endisset
        </div>
    </div>
@endif
{{ $slot }}
