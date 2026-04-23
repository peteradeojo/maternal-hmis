@props(['name'])

<div class="grid gap-2">
    <canvas height="200" class="signature border border-black"></canvas>
    <input type="text" class="signature-value hidden" name="{{ $name }}" />
    <div class="flex-center gap-x-4">
        <button type="button" class="btn bg-gray-300 clear-signature">Clear</button>
        <button type="button" class="btn bg-green-400 save-signature">Save</button>
    </div>
</div>
