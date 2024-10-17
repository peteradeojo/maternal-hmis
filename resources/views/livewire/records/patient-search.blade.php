<div>
    {{-- Care about people's approval and you will be their prisoner. --}}

    <div class="md:flex justify-between w-full items-end">
        <div class="md:w-1/2 gap-x-2 flex">
            <input type="search" wire:model="searchName" wire:keydown.debounce="search" class="form-input w-full"
                placeholder="Name" />
            <input type="search" wire:model="searchNumber" class="form-input w-full" wire:keydown.debounce="search"
                placeholder="Card Number" />
        </div>
        <div class="md:hidden py-2"></div>
        <div class="md:w-1/2 flex flex-col md:items-end justify-end">
            <label for="" class="">Select Category</label>
            <select id="" class="form-select w-1/2" wire:model="category" wire:change="search">
                <option value="">All</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->name }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="py-2">
        @if ($searchResults)
            <div class="overflow-x-auto max-w-full">
                <table class="table overflow-x-auto">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Card Number</th>
                            <th>Gender</th>
                            <th>Phone Number</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($searchResults as $result)
                            <tr>
                                <td>{{ $result->name }}</td>
                                <td>{{ $result->category->name }}</td>
                                <td>{{ $result->card_number }}</td>
                                <td>{{ $result->gender_value }}</td>
                                <td>{{ $result->phone }}</td>
                                <td><a class="link" href="{{ route('records.patient', $result) }}">View</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No Result</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <p>Search to fetch records.</p>
        @endif
    </div>
</div>
