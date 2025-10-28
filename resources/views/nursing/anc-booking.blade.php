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

                {{-- Obstetric history --}}
                <div class="sm:grid sm:grid-cols-6 gap-x-3 pb-2">
                    <div class="col-span-6">
                        <p class="text-xl bold">Obstetric History</p>
                    </div>

                    <div class="sm:col-span-6">
                        @livewire('lmp-form', ['profile' => $profile])
                    </div>

                    <div class="form-group">
                        <label>Gravidity</label>
                        <input type="number" class="input form-control" name="gravida" value="{{ $profile->gravida }}" />
                    </div>
                    <div class="form-group">
                        <label>Parity</label>
                        <input type="number" class="input form-control" name="parity" value="{{ $profile->parity }}" />
                    </div>
                    <div class="form-group"></div>
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

                {{-- Primary Assessment --}}
                {{-- <div class="grid grid-cols-6 gap-x-3">
                    <div class="col-span-6">
                        <p class="text-xl bold">History of Present Pregnancy</p>
                        <small>Check all that apply</small>
                    </div>

                    <div class="col-span-2"></div>
                    <div class="col-span-2"></div>
                    <span></span>
                </div> --}}

                <div class="flex justify-end py-4 px-4">
                    <button class="btn bg-blue-500 text-white">Save <i class="fa fa-save"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection
