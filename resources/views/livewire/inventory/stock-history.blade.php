<div>
    <div class="flex p-2">
        <input wire:model="search" type="search" class="form-control" placeholder="Search" id="" wire:change="resetCursor" />
        <button class="btn bg-primary" wire:click="loadTransactions"><i class="fa fa-check"></i></button>
    </div>

    <div class="p-2">
        <x-datatables id="">
            <x-slot:thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Quantity</th>
                    <th>Date</th>
                </tr>
            </x-slot:thead>

            <x-slot:tbody>
                <tbody>
                    @foreach ($transactions as $txn)
                        <tr>
                            <td>{{ $txn->id }}</td>
                            <td>{{ $txn->item?->name }}</td>
                            <td>{{ $txn->tx_type }}</td>
                            <td>{{ $txn->from->name }}</td>
                            <td>{{ $txn->to->name }}</td>
                            <td>{{ $txn->quantity }}</td>
                            <td>{{ $txn->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-slot:tbody>
        </x-datatables>

        <div class="flex justify-end gap-x-2">
            <button @disabled($cursor == 0) wire:click="previous" class="btn bg-gray-200">&larr;</button>
            <button @disabled($endOfResults) wire:click="next" class="btn bg-gray-200">&rarr;</button>
        </div>
    </div>
</div>
