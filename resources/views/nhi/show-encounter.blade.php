<div class="pb-8">
    <x-patient-profile :patient="$visit->patient" />

    <p class="text-lg font-semibold">Encounter Details</p>
    <p>Date: {{ $visit->created_at->format('Y-m-d h:i A') }}</p>

    <div class="py-4">
        <h3 class="text-lg font-semibold">Authorization Codes</h3>

        {{-- <form action="{{ route('api.nhi.update-authorization') }}" class="flex items-end gap-x-4"> --}}
        {{--     @csrf --}}
        {{--     <div class="form-group w-full"> --}}
        {{--         {{-- <label>Authorization Code</label> --}} --}}
        {{--         <input type="text" class="form-control" name="authorization_code" required /> --}}
        {{--     </div> --}}
        {{--     <div class="form-group"> --}}
        {{--         <button class="btn bg-blue-400">Submit</button> --}}
        {{--     </div> --}}
        {{-- </form> --}}

        <table class="table">
            @forelse ($visit->authorizations as $auth)
                <tr>
                    <td>{{ $auth->authorization_code }}</td>
                    <td>{{ $auth->created_at->format('Y-m-d h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td>No authorizations</td>
                </tr>
            @endforelse
        </table>
    </div>

    <x-reports.encounter.complaints :visit="$visit" />
    <x-reports.encounter.examinations :visit="$visit" />
    <x-reports.encounter.notes :visit="$visit" />
    <x-reports.encounter.diagnoses :visit="$visit" />
    <x-reports.encounter.tests :visit="$visit" />
    <x-reports.encounter.scans :visit="$visit" />
    <x-reports.encounter.prescriptions :visit="$visit" />
    <x-reports.encounter.admission :visit="$visit" />
</div>
