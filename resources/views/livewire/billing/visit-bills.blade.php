<div class="grid gap-y-2">
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div class="py-2 flex items-center justify-between">
        <button class="btn bg-green-500 text-white" wire:click="$refresh">Refresh <i class="fa fa-refresh"></i></button>
        <div class="form-group">
            <label>Status</label>
            <select wire:model="status" wire:change="$refresh" class="text-sm">
                <option value="{{ null }}">All</option>
                <option value="{{ Status::pending->value }}">Pending</option>
                <option value="{{ Status::cancelled->value }}">Cancelled</option>
                <option value="{{ Status::PAID->value }}">PAID</option>
            </select>
        </div>
    </div>
    @forelse (($status ? $visit->bills->where('status', $status) : $visit->bills) as $i => $bill)
        <div x-data="{ show: false }" class="p-3 bg-white">
            <p>Bill No. <span class="font-bold">#{{ $bill->bill_number }}</span></p>
            <p>Created: {{ $bill->created_at->format('Y-m-d h:i A') }}</p>

            <p>Total: <span class="font-semibold">
                    {{ config('app.currency') }}{{ number_format($bill->amount) }}</span>
            </p>
            <p>Paid: <span class="font-semibold">
                    {{ config('app.currency') }}{{ number_format($bill->paid) }}</span>
            </p>
            <p>Balance: <span class="font-semibold">
                    {{ config('app.currency') }}{{ number_format($bill->balance) }}</span>
            </p>


            {{-- @unless ($bill->status == Status::cancelled->value)
                @if ($bill->balance > 0)
                    <button data-bill_id="{{ $bill->id }}"
                        class="pay-btn btn btn-sm bg-green-600 text-white">Pay</button>

                    <button data-bill_id="{{ $bill->id }}"
                        class="cancel-btn btn btn-sm bg-green-600 text-white">Cancel</button>
                @else
                    <button class="btn bg-gray-200" @click="show = !show">More details</button>
                    <div class="p-2" x-show="show" x-cloak x-transition>
                        <ul class="p-2 list-disc list-inside bg-gray-100">
                            @foreach ($bill->entries as $e)
                                <li>{{ $e->description }} - {{ $e->total_price }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @else
                <span class="inline-grid py-1 px-2 rounded-md text-white bg-red-500">Cancelled</span>
            @endunless --}}

            <div class="flex gap-x-2">
                @if ($bill->status == Status::cancelled->value)
                    <span class="inline-grid py-1 px-2 rounded-md text-white bg-red-500">Cancelled</span>
                @endif

                @if ($bill->status == Status::pending->value || $bill->status == Status::quoted->value)
                    @if ($bill->paid > 0)
                        <button data-bill_id="{{ $bill->id }}"
                            class="pay-btn btn btn-sm bg-green-600 text-white">Finish payment</button>
                    @else
                        <button data-bill_id="{{ $bill->id }}"
                            class="pay-btn btn btn-sm bg-green-600 text-white">Pay</button>
                    @endif
                    <button data-bill_id="{{ $bill->id }}"
                        class="cancel-btn btn btn-sm bg-red-500 text-white">Cancel</button>
                @endif

                <button class="btn btn-sm bg-gray-200" @click="show = !show">More details</button>
            </div>

            <div class="p-1" x-show="show" x-cloak x-transition>
                <ul class="p-4 list-disc list-inside bg-gray-100">
                    @foreach ($bill->entries as $e)
                        <li>{{ $e->description }} - {{ $e->total_price }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @empty
        <div class="p-1 text-center">No bills</div>
    @endforelse

</div>
