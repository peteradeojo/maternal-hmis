<div x-data="{ editing: false, note: @js($note) }" @keyup.escape="note.note=`{{ $note->note }}`;editing=false">
    <div x-show="!editing" class="bg-gray-100 p-2">
        <p x-text="note.note"></p>
        <p><b>Attending:</b> <span x-text="note.consultant?.name"></span></p>
        <p><small><span x-text="new Date(note.created_at).toLocaleString('en-CA')"></span></small></p>
        <div class="flex gap-x-2">
            <button class="btn btn-sm bg-green-400 text-white" @click="editing = true">Edit <i
                    class="fa fa-pencil"></i></button>
        </div>
    </div>

    <div x-show="editing" class="bg-gray-100 p-2">
        <x-input-textarea x-model="note.note" name="note" class="mb-2 form-control"
            @keydown.enter.ctrl="updateNote(note.id, note.note).catch((err) => {
                note.note = `{{ $note->note }}`;
            }).finally(() => editing = false)" />

        <button class="btn btn-sm text-white bg-green-500"
            @click="updateNote(note.id, note.note).catch((err) => {
                note.note = `{{ $note->note }}`;
            }).finally(() => editing = false)">Update
            <i class="fa fa-save"></i></button>

        <button class="btn btn-sm text-white bg-red-500"
            @click="note.note = `{{ $note->note }}`;editing = false;">Cancel
            <i class="fa fa-stop"></i></button>
    </div>
</div>
