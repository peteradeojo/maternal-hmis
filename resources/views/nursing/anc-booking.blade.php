@extends('layouts.app')

@section('content')
    <div class="card p-1">
        <div class="card-header header">{{ $profile->patient->name }}</div>
        <div class="body py-2">
            <form action="" method="post">
                {{-- Personal history --}}
                <div class="grid grid-cols-6 gap-3">
                    <div class="col-span-6">
                        <p class="text-xl bold">Personal History</p>
                        <small>Check all that apply</small>
                    </div>

                    <div class="form-group">
                        <label>Chest Disease?
                            <input type="checkbox" name="chest_disease" />
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Kidney Disease?
                            <input type="checkbox" name="kidney_disease" />
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Blood Transfusion?
                            <input type="checkbox" name="blood_disease" />
                        </label>
                    </div>
                    <div class="form-group col-span-3">
                        <label>Operations excluding C/S?
                            <input type="checkbox" name="non_cs_surgery" />
                        </label>
                    </div>
                    <div class="form-group col-span-2">
                        <label>Others (please specify)</label>
                        <input type="text" name="personal_history_other" class="input form-control" />
                    </div>
                </div>

                <div class="py-2"></div>

                {{-- Obstetric history --}}
                <div class="grid grid-cols-6 gap-x-3">
                    <div class="col-span-6">
                        <p class="text-xl bold">Obstetric History</p>
                    </div>

                    <div class="form-group">
                        <label for="">Gravidity</label>
                        <input type="text" class="input form-control" name="gravida" />
                    </div>
                    <div class="form-group">
                        <label for="">Parity</label>
                        <input type="text" class="input form-control" name="parity" />
                    </div>
                    <div class="form-group"></div>
                </div>

                <div class="py-2"></div>

                {{-- Family history --}}
                <div class="grid grid-cols-6 gap-x-3 gap-y-1">
                    <div class="col-span-6 pb-4">
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
                        <input type="text" class="input form-control" name="gravida" />
                    </div>

                    <span></span>

                    <div class="col-span-2">
                        <label for="">Tuberculosis</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="parity" />
                    </div>
                    <span></span>

                    <div class="col-span-2">
                        <label for="">Hypertension</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="parity" />
                    </div>
                    <span></span>
                    <div class="col-span-2">
                        <label for="">Heart Disease</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="parity" />
                    </div>
                    <span></span>
                    <div class="col-span-2">
                        <label for="">Others (please specify)</label>
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="input form-control" name="parity" />
                    </div>
                    <span></span>
                </div>

                {{-- Primary Assessment --}}
                <div class="grid grid-cols-6 gap-x-3">
                    <div class="col-span-6">
                        <p class="text-xl bold">History of Present Pregnancy</p>
                        <small>Check all that apply</small>
                    </div>

                    <div class="col-span-2"></div>
                    <div class="col-span-2"></div>
                    <span></span>
                </div>
            </form>
        </div>
    </div>
@endsection
