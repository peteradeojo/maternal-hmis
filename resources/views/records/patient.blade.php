@extends('layouts.app')
@section('title', $patient->name)

@php
    $btnColor = 'btn btn-red';
@endphp

@section('content')
    {{-- <div class="card py px">
        <a href="{{ route('records.patient.edit', $patient) }}" class="btn btn-red"><u>Edit this patient information</u>.</a>
    </div> --}}
    <div class="p-8 bg-white">
        <div class="header">
            {{-- <a href="#" id="checkIn" data-id="{{ $patient->id }}" class="{{ $btnColor }}">Check In</a> --}}
            <p class="text-lg font-semibold underline">{{ $patient->name }}</p>
        </div>
        <div class="body py-2">
            <div class="grid grid-cols-2 justify-center">
                <div class="grid gap-y-2">
                    <p><b>Category:</b> {{ $patient->category->name }}</p>
                    <p><b>Card Number:</b> {{ $patient->card_number }}</p>
                    <p><b>Registration Date:</b> {{ $patient->created_at?->format('Y-m-d') }}</p>
                    <p><b>Age: </b> {{ $patient->dob?->diffInYears() }} ({{ $patient->dob?->format('Y-m-d') }})</p>
                    <p><b>Gender:</b> {{ $patient->gender_value }}</p>
                    <p><b>Phone number: </b> {{ $patient->phone }}</p>
                    <p><b>E-mail address: </b> {{ $patient->email }}</p>
                    <p><b>Marital status: </b> {{ $patient->marital_status }}</p>
                    <p><b>Address: </b> {{ $patient->address }}</p>
                    <p><b>Occupation: </b> {{ $patient->occupation }}</p>
                    <p><b>Religion: </b> {{ $patient->religion }}</p>
                </div>
                <div class="grid grid-cols-2 p-2 gap-2 no-print">
                    <button
                        class="rounded border-4 flex flex-col justify-center items-center hover:scale-[1.04] duration-200">
                        <span>Visits this year</span>
                        <span class="text-xl font-semibold">
                            {{ $patient->visits->where('created_at', '>=', date('Y-01-01'))->count() }}
                        </span>
                        <span>
                            Last visit: {{ $patient->visits->last()?->created_at }}
                        </span>
                    </button>
                    <button class="rounded bg-teal-400 hover:text-white hover:scale-[1.04] duration-200" id="edit-patient">
                        Update patient details <i class="fa fa-edit"></i>
                    </button>
                    <button class="border rounded bg-green-400 hover:text-white hover:scale-[1.04] duration-200"
                        id="start-visit">
                        Start a visit <i class="fa fa-user-circle"></i>
                    </button>
                    <button class="border rounded bg-blue-200 hover:text-white hover:scale-[1.04] duration-200">
                        Print patient profile <i class="fa fa-address-card"></i>
                    </button>
                    {{-- <button class="border rounded">
                    </button> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Antenatal Profile --}}
    @if ($patient->category->name == 'Antenatal')
        <div class="sm:p-8 bg-white my-3">
            <div class="header flex items-center gap-x-2">
                <p class="text-lg font-semibold">Antenatal Profile</p>

                @if (!isset($patient->antenatalProfiles[0]) || $patient->antenatalProfiles[0]->status != Status::active->value)
                    <button id="add-anc-profile">
                        <i class="fa fa-plus-circle inline-block py-5 px-2 text-lg text-yellow-600"></i>
                    </button>
                @endif
            </div>

            <div class="body">
                <div class="py">
                    @if (!isset($patient->antenatalProfiles[0]))
                        <p class="text-danger">No antenatal profile found for this patient.
                        </p>
                    @else
                        <p><b>Category:</b>
                            {{ preg_replace('/_/', ' ', $patient->antenatalProfiles[0]?->card_type ?? '') }}</p>
                        <p><b>Registration Date:</b> {{ $patient->antenatalProfiles[0]?->created_at->format('Y-m-d') }}</p>
                        <p><b>Status: </b> {{ Status::tryFrom($patient->anc_profile->status)?->name }} | <a href="#"
                                class="link" id="close-anc-profile"
                                data-id="{{ $patient->antenatalProfiles[0]->id }}">Delete this profile</a></p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="p-8 bg-white my-3">
        <div class="header">
            <div class="flex gap-x-2 items-center">
                <p class="text-lg font-semibold">Health Insurance</p>
                <button><i class="text-lg fa fa-plus-circle text-green-500"></i></button>
            </div>
        </div>
        <div class="body foldable-body">
            @forelse ($patient->insurance as $insurance)
                <div class="py">
                    <p><b>HMO:</b> {{ $patient->insurance?->hmo_name }}</p>
                    <p><b>Company:</b> {{ $patient->insurance?->hmo_name }}</p>
                    <p><b>ID No:</b> {{ $patient->insurance?->hmo_id_no }}</p>
                </div>
            @empty
                <div class="p-2">
                    No insurance profiles have been added for this patient.
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#add-anc-profile").on("click", () => {
                useGlobalModal(async (a) => {
                    a.find("#global-modal-title").text("Add Antenatal profile");

                    axios.get("{{ route('records.new-anc', $patient) }}").then(({
                        data
                    }) => {
                        console.log(data);

                        a.find("#global-modal-content").html(data);
                    }).catch((err) => {
                        a.find("#global-modal-content").html(err.response.data);
                    }); //.finally(() => window.livewire.rescan());
                });
            });

            $("#edit-patient").on("click", () => {
                useGlobalModal(async (a) => {

                });
            });

            $("#start-visit").on("click", () => {
                useGlobalModal((a) => {
                    a.find("#global-modal-title").text("Check-In for Consultation");
                    axios.get("{{ route('records.start-visit', $patient) }}").then(({
                        data
                    }) => {
                        a.find("#global-modal-content").html(data);
                    }).catch((err) => {
                        a.find("#global-modal-content").html(err.response.data);
                    })
                });
            });

            $("#close-anc-profile").on("click", function() {
                useGlobalModal((a) => {
                    console.log(this);

                    a.find("#global-modal-title").text("Close Antenatal Profile")
                    axios.get("{{ route('records.close-anc', ':profile') }}".replace(":profile", $(
                        this).attr('data-id'))).then(({
                        data
                    }) => {
                        a.find("#global-modal-content").html(data);
                    }).catch((err) => a.find("#global-modal-content").html(err.response.data))
                });
            })
        });
    </script>
@endpush
