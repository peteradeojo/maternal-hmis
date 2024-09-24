@extends('layouts.app')
@section('title', $visit->patient->name . " | Doctor's Visit")

@section('content')
    @php
        $profile = null;
    @endphp

    <livewire:doctor.visit-form :visit="$visit" />

    <div class="modal hide" id="diagnosis-modal">
        <div class="content bg-white p-2">
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
        </div>
    </div>

    <div class="modal hide" id="notes-modal">
        <div class="content p-2 bg-white">
            <p class="bold">Add Note</p>
            <div class="py-1"></div>

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
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script defer>
        // initTab(document.querySelector('#nav-tab'));
        initTab(document.querySelector('#actions-tab'));
        initTab(document.querySelector('#notes-tabs'));
        initTab(document.querySelector('#tests-tabs'));

        function asyncForm(form, route, callback = (e, data) => {}) {
            $(form).on("submit", (e) => {
                e.preventDefault();
                fetch(route, {
                    method: 'POST',
                    body: new FormData(e.currentTarget),
                    headers: {
                        'Accept': 'application/json',
                    },
                }).then((res) => {
                    callback(e.currentTarget, res);
                }).catch((err) => {
                    console.error(err);
                });
            });
        }

        $(() => {
            $("#note-form").on('submit', (e) => {
                e.preventDefault();
                fetch("{{ route('api.doctor.note', ['visit' => $visit->id]) }}", {
                    method: 'POST',
                    body: new FormData(e.currentTarget),
                }).then((res) => e.currentTarget.closest(".modal").classList.add("hide")).catch((
                    err) => {
                    console.error(err);
                });
            });

            $("#diagnosis-form").on('submit', (e) => {
                e.preventDefault();
                fetch("{{ route('api.doctor.diagnosis', ['visit' => $visit->id]) }}", {
                    method: 'POST',
                    body: new FormData(e.currentTarget),
                }).then((res) => {
                    e.currentTarget.closest(".modal").classList.add("hide");
                }).catch((err) => {
                    console.error(err);
                });
            });
        });

        function addPrescription(data) {
            console.log(data);
        }
    </script>
@endpushOnce