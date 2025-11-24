@extends('layouts.app')

@section('content')
    <div class="container bg-white p-4">
        <x-patient-profile :patient="$doc->patient" />
        <hr class="py-2">

        {{-- @dump($doc->results) --}}
        <div class="body">
            <p><b>Requested by: </b> {{ $doc->requester->name }} </p>
            <p><b>Time: </b> {{ $doc->created_at->format('Y-m-d h:i A') }}</p>
            <p><b>Investigation: </b> <span class="underline">{{ $doc->name }}</span></p>

            <div class="py-3"></div>

            <form id="scan-form" x-data="{ form: 'obstetric' }">
                <select class="form-control float-end sm:w-1/3" name="report_type" @change="form = $event.target.value">
                    <option selected="selected" value="obstetric">Obstetric Report</option>
                    {{-- <option value="pelvic">Pelvic Report</option> --}}
                    <option value="general">General</option>
                    <option value="echo">Echocardiographic</option>
                </select>

                @csrf
                <div class="form-group sm:w-1/3">
                    <label>Date</label>
                    <x-input-datetime class="form-control" value="{{ $doc->results?->date }}" name="date" />
                </div>
                <div class="form-group sm:w-1/3">
                    <label>Clinician / Referred by</label>
                    <x-input-text class="form-control" name="clinician"
                        value="{{ $doc->results?->clinician ?? $doc->requester->name }}" />
                </div>

                <p class="font-semibold text-lg">Personal Information</p>
                <div class="form-group sm:w-1/4">
                    <label>Age</label>
                    <x-input-number name="age" class="form-control"
                        value="{{ $doc->results?->age ?? (int) $doc->patient->dob?->diffInYears() }}" />
                </div>

                <template x-if="form != 'echo'">
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label>LMP</label>
                            <x-input-date name="lmp" class="form-control"
                                value="{{ $doc->results->lmp ?? $doc->patient->ancProfile?->lmp?->format('Y-m-d') }}" />
                        </div>
                        <div class="form-group">
                            <label>Gravidity</label>
                            <x-input-text name="gravidity" value="{{ $doc->results?->gravidity }}" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Parity</label>
                            <x-input-text name="parity" class="form-control" value="{{ $doc->results?->parity }}" />
                        </div>
                    </div>
                </template>

                <template x-if="form == 'echo'">
                    <div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label>Sex</label>
                                <x-input-select name="gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </x-input-select>
                            </div>
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <x-input-number name="weight" class="form-control" step="0.1" />
                            </div>
                            <div class="form-group">
                                <label>Height (m)</label>
                                <x-input-number name="height" class="form-control" step="0.1" />
                            </div>
                        </div>

                        <h2 class="header">Echocardiogram Report</h2>
                    </div>
                </template>


                <div class="py-4" x-cloak>
                    <template x-if="form == 'obstetric'">
                        <div>
                            <h2 class="header">Obstetric Scan Report</h2>
                            <div class="grid sm:grid-cols-4 gap-4">
                                <div class="form-group">
                                    <label>Number of Fetuses</label>
                                    <x-input-text name="number_of_fetuses" class="form-control"
                                        value="{{ $doc->results?->number_of_fetuses }}" />
                                </div>
                                <div class="form-group">
                                    <label>Viability</label>
                                    <x-input-text name="viability" class="form-control"
                                        value="{{ $doc->results?->viability }}" />
                                </div>
                                <div class="form-group">
                                    <label>Gestational Sac Diameter</label>
                                    <x-input-text name="gestational_sac_diameter"
                                        value="{{ $doc->results?->gestational_sac_diameter }}" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Crown Rump Length (CRL)</label>
                                    <x-input-text name="crown_rump_length" value="{{ $doc->results?->crown_rump_length }}"
                                        class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Biparietal Diameter</label>
                                    <x-input-text name="biparietal_diameter" class="form-control"
                                        value="{{ $doc->results?->biparietal_diameter }}" />
                                </div>
                                <div class="form-group">
                                    <label>Head Circumference</label>
                                    <x-input-text name="head_circumference"
                                        value="{{ $doc->results?->head_circumference }}" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Abdominal Circumference</label>
                                    <x-input-text class="form-control"
                                        value="{{ $doc->results?->abdominal_circumference }}"
                                        name="abdominal_circumference" />
                                </div>
                                <div class="form-group">
                                    <label>Femur Length</label>
                                    <x-input-text class="form-control" value="{{ $doc->results?->femur_length }}"
                                        name="femur_length" />
                                </div>
                                <div class="form-group">
                                    <label>Estimated Gestational Age (EGA)</label>
                                    <x-input-text class="form-control"
                                        value="{{ $doc->results?->estimated_gestational_age }}"
                                        name="estimated_gestational_age" />
                                </div>
                                <div class="form-group">
                                    <label>Estimated Date of Delivery (EDD)</label>
                                    {{-- <x-input-text class="form-control" name="estimated_date_of_delivery" /> --}}
                                    <x-input-date class="form-control" name="estimated_date_of_delivery"
                                        value="{{ $doc->results?->estimated_date_of_delivery }}" />
                                </div>
                                <div class="form-group">
                                    <label>Estimated Fetal Weight</label>
                                    <x-input-text class="form-control" name="estimated_fetal_weight"
                                        value="{{ $doc->results?->estimated_fetal_weight }}" />
                                </div>
                                <div class="form-group">
                                    <label>Placental Location</label>
                                    <x-input-text class="form-control" name="placental_location"
                                        value="{{ $doc->results?->placental_location }}" />
                                </div>
                                <div class="form-group">
                                    <label>Placental Maturity</label>
                                    <x-input-text class="form-control" name="placental_maturity"
                                        value="{{ $doc->results?->placental_maturity }}" />
                                </div>
                                <div class="form-group">
                                    <label>Amniotic Fluid Volume</label>
                                    <x-input-text class="form-control" name="amniotic_fluid_volume"
                                        value="{{ $doc->results?->amniotic_fluid_volume }}" />
                                </div>
                                <div class="form-group">
                                    <label>Fetal Presentation</label>
                                    <x-input-text name="fetal_presentation" class="form-control"
                                        value="{{ $doc->results?->fetal_presentation }}" />
                                </div>
                                <div class="form-group">
                                    <label>Fetal Lie</label>
                                    <x-input-text name="fetal_lie" class="form-control"
                                        value="{{ $doc->results?->fetal_lie }}" />
                                </div>
                                <div class="form-group col-span-2">
                                    <label>Other Findings</label>
                                    <x-input-textarea name="other_findings" class="form-control"
                                        value="{{ $doc->results?->other_findings }}" />
                                </div>
                                <div class="form-group col-span-2">
                                    <label>Conclusion</label>
                                    <x-input-textarea name="conclusion" class="form-control"
                                        value="{{ $doc->results?->conclusion }}" />
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="form == 'pelvic'">
                        <div>
                            <h2 class="header">Pelvic Scan Report</h2>

                        </div>
                    </template>

                    <template x-if="form == 'general'">
                        <div>
                            <h2 class="header">General Radiology Report</h2>
                            <div class="form-group">
                                <label>Report</label>
                                <x-input-textarea name="report" class="form-control" required />
                            </div>
                        </div>
                    </template>
                </div>

                <div class="form-group">
                    <button class="btn bg-blue-400 text-white">Save</button>
                </div>
            </form>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(() => {
            // document.querySelectorAll("[data-action='drag_drop']").forEach((elem, i) => {
            //     elem.addEventListener("dragover", (e) => {
            //         e.preventDefault();
            //         elem.classList.add("bg-gray-100");
            //     });
            //     elem.addEventListener("dragenter", (e) => {
            //         e.preventDefault();
            //         elem.classList.add("bg-gray-100");
            //     });
            //     elem.addEventListener("dragleave", (e) => {
            //         e.preventDefault();

            //         elem.classList.remove("bg-gray-100");
            //     });

            //     elem.addEventListener('dragend', (e) => {
            //         e.preventDefault();

            //         elem.classList.remove("bg-gray-100");
            //     });

            //     elem.addEventListener("drop", (ev) => {
            //         ev.preventDefault();

            //         // Use DataTransfer interface to access the file(s)
            //         [...ev.dataTransfer.files].forEach((file, i) => {
            //             document.getElementById("result_file").files[0] = file;
            //             document.getElementById("result_label").innerHTML =
            //                 `<p>${file.name}</p>`;
            //         });
            //     });
            // })

            // document.querySelector("#result_file").addEventListener("change", (e) => {
            //     const file = e.target.files[0];
            //     document.getElementById("result_label").innerHTML = `<p>${file.name}</p>`;
            // });

            $(document).on('submit', '#scan-form', function(e) {
                e.preventDefault();
                const p = confirm("Are you sure? This will overwrite any previous results for this scan.");

                if (!p) return;

                const formData = new FormData(e.currentTarget);

                // console.log("Form submitted.");
                submitForm(document.querySelector("#scan-form"), "{{ route('rad.scan', $doc) }}", (e,
                    res) => {
                    const {
                        data
                    } = res;
                    console.log(res);
                    notifySuccess("Scan result saved!");
                });
            });
        });
    </script>
@endpush
