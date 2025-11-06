<div>
    <table class="table table-list mb-2" key="{{ $test->id }}">
        <thead>
            <tr>
                <td class="flex items-center gap-x-4">
                    <b>{{ Str::upper($test->name) }}</b>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <span x-cloak class="flex justify-end items-center gap-x-2 w-full">
                        <label>Status</label>
                        <select x-model="$wire.status" @change="$wire.status = ($event.target.value);$wire.updateHash()"
                            class="min-w-fit text-sm">
                            <option value="{{ Status::pending->value }}">Pending</option>
                            <option value="{{ Status::active->value }}">Sample Collected</option>
                            <option value="{{ Status::completed->value }}">Tests Done</option>
                            <option value="{{ Status::closed->value }}">Delivered</option>
                            <option value="{{ Status::cancelled->value }}">Rejected</option>
                        </select>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <th>Result</th>
                <th>Unit</th>
                <th>Reference Range</th>
                <td>
                    @unless ($status == Status::closed->value || $status == Status::cancelled->value)
                        <button wire:click="addResult" class="add-result btn bg-blue-500 text-white" type="button">Add
                            Result</button>
                    @endunless
                </td>
            </tr>
        </thead>
        <tbody>
            @if ($status != Status::cancelled->value)
                @foreach ($results ?? [] as $j => $result)
                    <tr>
                        <td><input type="text" wire:model="results.{{ $j }}.description"
                                class="form-control" @if ($status == Status::closed->value) disabled @endif></td>
                        <td><input type="text" wire:model="results.{{ $j }}.result" class="form-control"
                                @if ($status == Status::closed->value) disabled @endif></td>
                        <td><input type="text" class="form-control" wire:model="results.{{ $j }}.unit"
                                @if ($status == Status::closed->value) disabled @endif></td>
                        <td><input type="text" class="form-control"
                                wire:model="results.{{ $j }}.reference_range"
                                @if ($status == Status::closed->value) disabled @endif></td>
                        <td>
                            {{-- @unless ($test->status == Status::completed->value) --}}
                            <button wire:click="removeResult({{ $j }})" class="btn bg-red-400 text-white"
                                type="button">Remove
                                <i class="fa fa-trash"></i>
                            </button>
                            {{-- @endunless --}}
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">
                    <button wire:click="save" @if ($currentHash == $initHash) disabled @endif
                        class="btn bg-blue-400 text-white">Save <i class="fa fa-save"></i></button>
                    {{-- <span wire:loading wire:target="save" class="inline-block"><x-spinner w="w-4" /></span> --}}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
