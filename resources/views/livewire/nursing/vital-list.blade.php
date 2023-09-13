<div class="card py px my">
    <div class="header">
        <div class="row between">
            <h2 class="card-header">Vitals</h2>
            <button class="btn btn-blue" wire:click='refreshData'>Reload</button>
        </div>
    </div>
    <div class="body py">
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
</div>
