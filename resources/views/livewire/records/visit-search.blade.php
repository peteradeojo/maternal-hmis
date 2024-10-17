<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}


    <div class="flex lg:w-1/2 gap-x-2">
        <div class="w-1/2">
            <label class="block w-full">Name</label>
            <input type="search" name="" id="" class="form-input form-control" placeholder="Search name"
                wire:keyup.debounce="search" wire:model="name_query" />
        </div>

        <div class="w-1/2">
            <label class="block w-full">Card Number</label>
            <input type="search" name="" id="" class="form-input form-control"
                placeholder="Card Number" wire:keyup.debounce="search" wire:model="number_query" />
        </div>
    </div>

    @if (!empty($visits))
        <div class="overflow-x-auto max-w-full py-2">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Card Number</th>
                        <th>Date</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visits as $visit)
                        <tr>
                            <td>{{ $visit->patient->name }}</td>
                            <td>{{ $visit->patient->card_number }}</td>
                            <td>{{ $visit->created_at->format('Y-m-d h:i A') }}</td>
                            <td>{{ $visit->readable_visit_type }}</td>
                            <td><a href="{{route('records.show-history', $visit)}}" class="link">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
