@extends('layouts.app')
@section('title', $visit->patient->name . " | Doctor's Visit")

@section('content')
    <x-back-link />
    <livewire:doctor.visit-form :visit="$visit" @anc-profile-refresh="refreshProfile" />

    <x-overlay-modal id="diagnosis-modal">
        <p class="text-xl bold">Add a Diagnosis</p>
        <div class="py-1"></div>
        <form id="diagnosis-form">
            @csrf
            <div class="form-group">
                <label>Diagnosis</label>
                <input type="text" placeholder="Enter your diagnosis" name="diagnosis" class="form-control" required
                    list="diagnosis-list" />
                <datalist id="diagnosis-list">
                    @foreach ($diagnoses as $d)
                        <option>{{ $d['name'] }}</option>
                    @endforeach
                </datalist>
            </div>
            <div class="form-group">
                <button class="btn bg-blue-500 text-white">Submit</button>
            </div>
        </form>
    </x-overlay-modal>

    <x-overlay-modal id="notes-modal" title="Add/Edit Notes">
        <div id="notes-tabs" data-tablist="#bose">
            @include('components.tabs', ['options' => ['Add', 'Edit']])
            <div id="bose" class="p-1">
                <div class="tab">
                    <form id="note-form">
                        @csrf
                        <div class="form-group">
                            <label>Note</label>
                            <textarea name="note" class="w-full resize-y border border-gray-400 rounded-none form-textarea" rows="5"
                                required></textarea>
                        </div>
                        <div class="form-group">
                            <button class="btn bg-blue-500 text-white">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-overlay-modal>

    <x-overlay-modal id='scan-result-modal' title="Scan Result">
        <div id="display"></div>
    </x-overlay-modal>
@endsection

@pushOnce('scripts')
    <script>
        $(document).ready(function() {


            // initTab(document.querySelector('#nav-tab'));
            initTab(document.querySelector('#actions-tab'));
            initTab(document.querySelector('#notes-tabs'));
            // initTab(document.querySelector('#tests-tabs'));
            initTab(document.querySelector('#anc-visit-tests-2'));

            $(() => {
                $("#note-form").on('submit', (e) => {
                    e.preventDefault();
                    fetch("{{ route('api.doctor.note', ['visit' => $visit->id]) }}", {
                        method: 'POST',
                        body: new FormData(e.currentTarget),
                    }).then((res) => {
                        e.currentTarget.reset();
                        notifySuccess("Note saved!");
                    }).catch((err) => {
                        notifyError(err.message);
                    });
                });

                $("#diagnosis-form").on('submit', (e) => {
                    e.preventDefault();
                    fetch("{{ route('api.doctor.diagnosis', ['visit' => $visit->id]) }}", {
                        method: 'POST',
                        body: new FormData(e.currentTarget),
                    }).then((res) => {
                        e.currentTarget.reset();
                        notifySuccess("Diagnosis saved!");
                    }).catch((err) => {
                        console.error(err);
                    });
                });
            });

            function loadVisitReport(id) {
                const display = document.querySelector("#previous-visit-report");

                fetch("{{ route('doctor.visit', ['visit' => ':id']) }}?brief".replace(":id", id)).then((res) => {
                    res.text().then((text) => {
                        display.innerHTML = text;
                    }).catch(err => console.error(err));
                });
            }
        });
    </script>
@endpushOnce
