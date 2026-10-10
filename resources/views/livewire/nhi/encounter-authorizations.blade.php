<div>
    <p class="text-2xl">Authorizations</p>

    <table class="ui-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Requested Amt.</th>
                <th>Approved Amt.</th>
                <th></th>
            </tr>
            <thead>
            <tbody>
                @forelse($visit->authorizations as $i => $auth)
                    <tr>
                        <td>{{ $auth->authorization_code }}</td>
                        <td>{{ $auth->requested_amount }}</td>
                        <td>{{ $auth->approved_amount }}</td>
                        <td>
                            <button title="Edit" class="btn bg-primary btn-sm text-white"
                                wire:click="setEditing({{ $auth->id }})"><i class="fa fa-pencil"></i>
                                <button title="Delete" class="btn bg-red btn-sm text-white"
                                    wire:click="deleteAuthorization({{ $auth->id }})"><i class="fa fa-trash"></i>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="4">No codes submitted</td>
                    </tr>
                @endforelse
            </tbody>
    </table>

    <form wire:submit="save" class="grid grid-cols-2 gap-x-4 items-end">
        <div class="form-group">
            <label>Authorization code</label>
            <input type="text" class="form-control" wire:model="authorization_code" required />
        </div>
        <div class="form-group">
            <label>Requested Amount</label>
            <input type="number" class="form-control" step="0.01" wire:model="requested_amount" />
        </div>
        <div class="form-group">
            <label>Approved Ammount</label>
            <input type="number" class="form-control" step="0.01" wire:model="approved_amount" />
        </div>
        <div class="form-group">
            <button class="btn bg-primary">Submit</button>
        </div>
    </form>
</div>
