<form action="" method="post">
    @csrf
    @include('components.complaints-form', ['complaints' => $complaints])
    @include('components.examinations-form')
    @include('components.diagnosis-form')

    <fieldset class="p-1">
        <legend>Tests & Investigations</legend>
        @include('components.tests-form')
        @include('components.imagings-form')
    </fieldset>
    @include('components.prescriptions-form')
    <div class="form-group">
        <label for="remarks">Remarks</label>
        <textarea name="comment" id="remarks" cols="30" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="next_visit">Next Visit</label>
        <input type="date" name="next_visit" id="next_visit" class="form-control" min="{{ date('Y-m-d') }}">
    </div>
    <div class="form-group">
        <p class="pb-1">
            <label>
                <input type="checkbox" name="admit" /> Admit?
            </label>
        </p>
        <button class="btn btn-red" type="submit">Submit</button>
    </div>
</form>
