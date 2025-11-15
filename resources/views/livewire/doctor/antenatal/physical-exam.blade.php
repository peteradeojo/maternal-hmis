<div>
    {{-- The whole world belongs to you. --}}
    <p class="font-semibold text-lg">Physical Examination</p>
    <div>
        <form wire:submit.prevent="save">
            <p class="font-semibold py-2">General Condition</p>
            <div class="flex gap-x-2 items-center justify-center">
                <div class="form-group w-1/2">
                    <label>Oedema</label>
                    <x-input-select name='physical.oedema' wire:model="physical.oedema" class="form-control">
                        <option value="normal">Normal</option>
                        <option value="mild">Mild</option>
                        <option value="moderate">Moderate</option>
                        <option value="severe">Severe</option>
                    </x-input-select>
                </div>
                <div class="form-group w-1/2">
                    <label>Anemia</label>
                    <x-input-select wire:model="physical.anemia" class="form-control" name='physical.anemia'>
                        <option value="not pale">Not Pale</option>
                        <option value="pale">Pale</option>
                    </x-input-select>
                </div>
            </div>
            <div class="form-group">
                <label class="font-semibold py-2">Respiratory System</label>
                <x-input-textarea name='physical.respiratory' wire:model="physical.respiratory" class="form-control" />
            </div>
            <div class="form-group">
                <label class="font-semibold py-2">Cardiovascular System</label>
                <x-input-textarea name='physical.cardio' wire:model="physical.cardio" class="form-control" />
            </div>

            <p class="font-semibold py-2">Abdomen</p>
            <div class="form-group">
                <label>Spleen</label>
                <x-input-text wire:model="physical.spleen" type="text" class="form-control" name='physical.spleen' />
            </div>
            <div class="form-group">
                <label>Liver</label>
                <x-input-text wire:model="physical.liver" type="text" class="form-control" name='physical.liver' />
            </div>

            <div class="form-group">
                <label class="font-semibold py-2">Vaginal Examination</label>
                <x-input-textarea wire:model="physical.vaginal" class="form-control" name='physical.vaginal' />
            </div>
            <div class="form-group">
                <label class="font-semibold py-2">Other Abnormalities</label>
                <x-input-textarea wire:model="physical.other" class="form-control" name='physical.other' />
            </div>
            <div class="form-group">
                <label class="font-semibold py-2">Comments</label>
                <x-input-textarea wire:model="physical.comment" class="form-control" name='physical.comment' />
            </div>
            <div class="form-group">
                <button class="btn float-end bg-green-500 text-white">Save <i class="fa fa-save"></i></button>
            </div>
        </form>
    </div>
</div>
