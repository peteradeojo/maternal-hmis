<div class="py px my">
    <div class="row mb-2">
        <h2 class="my">Vitals</h2>
        <button class="btn btn-blue" wire:click='refreshData'>Reload</button>
    </div>
    <table id="vitals-table" class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th></th>
            </tr>
        </thead>
        <tbody wire:model='visits'>
            @foreach ($visits as $v)
                <tr>
                    <td>{{ $v->patient->name }}</td>
                    <td>{{ $v->patient->category->name }}</td>
                    <td>
                        <a href="{{ route('nurses.patient-vitals', $v) }}">Take Vitals</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
