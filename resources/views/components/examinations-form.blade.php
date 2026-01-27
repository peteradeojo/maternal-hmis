<div class="grid">
    <h3>General Physical Examinations</h3>
    <div class="form-group">
        <textarea name="physical_exams" class="form-control">{{ $exam?->general }}</textarea>
    </div>

    <h3>Specific Examinations</h3>
    <div class="form-group">
        <label for="spec-head">Head & Neck</label>
        <textarea type="text" name="head_and_neck" class="form-control" id="spec-head">{{ $exam?->specifics['head_and_neck'] }}</textarea>
    </div>
    <div class="form-group">
        <label for="chest">Chest</label>
        <textarea name="chest" id="chest" class="form-control">{{ $exam?->specifics['chest'] }}</textarea>
    </div>
    <div class="form-group">
        <label for="abdo">Abdomen</label>
        <textarea name="abdomen" id="abdo" class="form-control">{{ $exam?->specifics['abdomen'] }}</textarea>
    </div>
    <div class="form-group">
        <label for="muscles">Muscloskeletal</label>
        <textarea name="muscle_skeletal" id="muscles" class="form-control">{{ $exam?->specifics['muscle_skeletal'] }}</textarea>
    </div>
    <div class="form-group">
        <label for="vag">Vaginal/Rectal</label>
        <textarea id="vag" name="vaginal_digital_rectal" class="form-control">{{ $exam?->specifics['vaginal_digital_rectal'] }}</textarea>
    </div>
</div>
