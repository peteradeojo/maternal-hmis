@extends('layouts.app')

@section('content')
    <div class="card p-1">
        <div class="card-header header">{{ $profile->patient->name }}</div>
        <div class="body py-2">
            <form action="" method="post">
                @csrf
                {{-- Personal history --}}
                <div class="grid grid-cols-6 gap-3 items-start pb-2">
                    <div class="col-span-6">
                        <p class="text-xl bold">Personal History</p>
                        <small>Check all that apply</small>
                    </div>

                    <div class="grid col-span-3">
                        <div class="form-group">
                            <label class="block w-full">Chest Disease?
                                <input type="checkbox" name="risk_assessment[chest_disease]" />
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Kidney Disease?
                                <input type="checkbox" name="risk_assessment[kidney_disease]" />
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Blood Transfusion?
                                <input type="checkbox" name="risk_assessment[blood_disease]" />
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Operations excluding C/S?
                                <input type="checkbox" name="risk_assessment[non_cs_surgery]" />
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-span-3">
                        <label>Others (please specify)</label>
                        {{-- <input type="text" name="personal_history_other" class="input form-control" /> --}}
                        <textarea name="risk_assessment[personal_history_other]" class="form-control resize-y" rows="3"></textarea>
                    </div>
                </div>

                {{-- Family history --}}
                <div class="grid grid-cols-6 gap-x-3 gap-y-1">
                    <div class="col-span-6">
                        <p class="text-xl bold">Family History</p>
                        <small>leave blank if no</small>
                    </div>

                    <div class="col-span-2 bold">Condition</div>
                    <div class="col-span-2 bold">Relation</div>
                    <span></span>

                    <div class="col-span-2">
                        <label for="">Multiple Pregnancy</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="risk_assessment[multiple_pregnancy]" />
                    </div>

                    <span></span>

                    <div class="col-span-2">
                        <label for="">Tuberculosis</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="risk_assessment[tuberculosis]" />
                    </div>
                    <span></span>

                    <div class="col-span-2">
                        <label for="">Hypertension</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="risk_assessment[hypertension]" />
                    </div>
                    <span></span>
                    <div class="col-span-2">
                        <label for="">Heart Disease</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="risk_assessment[heart_disease]" />
                    </div>
                    <span></span>
                    <div class="col-span-2">
                        <label for="">Others (please specify)</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="risk_assessment[others]" />
                    </div>
                </div>

                {{-- Obstetric history --}}
                <div class="sm:grid sm:grid-cols-6 gap-x-3 pb-2">
                    <div class="col-span-6">
                        <p class="text-xl bold">Obstetric History</p>
                    </div>

                    <div class="sm:col-span-6">
                        @livewire('lmp-form', ['profile' => $profile])
                    </div>

                    <div class="form-group sm:col-span-2">
                        <label>Gravidity</label>
                        <input type="number" class="input form-control" name="gravida" value="{{ $profile->gravida }}" />
                    </div>
                    <div class="form-group sm:col-span-2">
                        <label>Parity</label>
                        <input type="number" class="input form-control" name="parity" value="{{ $profile->parity }}" />
                    </div>
                    <div class="form-group sm:col-span-2">
                        <label>Number of living children</label>
                        <input type="number" name="number_of_living_children" class="form-control"
                            value="{{ $profile->data?->number_of_living_children }}">
                    </div>

                    <div class="form-group flex flex-col py-8 gap-y-2 col-span-full" x-data="{
                        obj_history: [],
                        addRow() {
                            this.obj_history.push({});
                        },
                        removeRow(i) {
                            return this.obj_history.splice(i, 1);
                        }
                    }">
                        <div class="flex justify-end">
                            <button type="button" x-on:click="addRow" class="btn btn-sm bg-blue-500 text-white">Add
                                &plus;</button>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date of Birth</th>
                                    <th>Duration of pregnancy</th>
                                    <th>Pregnancy, Labour & puerperium</th>
                                    <th>Type of Delivery</th>
                                    <th>Place of Delivery</th>
                                    <th>Baby Condition</th>
                                    <th>Gender</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="h, i in obj_history">
                                    <tr wire:key="i">
                                        <td>
                                            <input type="date" x-model="obj_history[i].dateOfBirth"
                                                :name="`obj_history[${i}][date_of_birth]`" class="form-control" required />
                                        </td>
                                        <td>
                                            <input type="text" :name="`obj_history[${i}][duration_of_pregnancy]`"
                                                id="" class="form-control" />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                :name="`obj_history[${i}][pregnancy_labour_and_puerperium]`" />
                                        </td>
                                        <td>
                                            <input type="text" :name="`obj_history[${i}][type_of_delivery]`" class="form-control" />
                                        </td>
                                        <td><input type="text" class="form-control" :name="`obj_history[${i}][place_of_delivery]`" /></td>
                                        <td>
                                            <select :name="`obj_history[${i}][baby_condition]`" class="form-control">
                                                <option selected disabled>Select a condition</option>
                                                <option value="alive">Alive</option>
                                                <option value="stillborn">Stillborn</option>
                                                <option value="dead">Dead</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select :name="`obj_history[${i}][gender_of_baby]`" class="form-control">
                                                <option disabled selected>Select gender</option>
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button wire:key="'button_' + i" @click="() => removeRow(i)"
                                                class="btn btn-sm bg-red-500 text-white" type="button"><i
                                                    class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="obj_history.length < 1">
                                    <tr>
                                        <td class="text-center" colspan="8">No records</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="sm:grid sm:grid-cols-6 gap-4">

                    <div class="col-span-3 flex flex-col gap-y-2">
                        <h3 class="text-xl font-bold">History of Present Pregnancy</h3>
                        <div class="form-group">
                            <label>Vaginal Bleeding</label>
                            <input type="text" class="form-control" name="present_pregnancy[vaginal_bleeding]" />
                        </div>
                        <div class="form-group">
                            <label>Vaginal Discharge</label>
                            <input type="text" class="form-control" name="present_pregnancy[vaginal_discharge]" />
                        </div>
                        <div class="form-group">
                            <label>Urinary Symptoms</label>
                            <input type="text" class="form-control" name="present_pregnancy[urinary_symptoms]" />
                        </div>
                        <div class="form-group">
                            <label>Leg Swelling</label>
                            <input type="text" class="form-control" name="present_pregnancy[leg_swelling]" />
                        </div>
                        <div class="form-group">
                            <label>Other symptoms</label>
                            <input type="text" class="form-control" name="present_pregnancy[other_symptoms]" />
                        </div>
                    </div>

                    {{-- <div class="col-span-3 flex flex-col gap-y-2">
                        <p class="text-xl font-bold">Vitals</p>

                        <livewire:nurses.vitals :event="$profile" :showResults="false" />
                    </div> --}}
                </div>

                <div class="py-4">
                    <button class="btn bg-primary text-white">Submit <i class="fa fa-save"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection
