@extends('layouts.app')
@section('title', "Billing [#{$visit->id}]")

@section('content')
    <x-back-link />
    <div class="p-3 bg-white">
        <x-patient-profile :patient="$visit->patient" />
    </div>
    <div class="py-3"></div>

    {{-- <div x-data="{ bills: @js($visit->bills).map((o) => ({ id: o.id, show: false })) }" class="grid gap-y-2">
        @foreach ($visit->bills as $i => $bill)
            <div x-data class="p-3 bg-white">
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


                @unless ($bill->status == Status::cancelled->value)
                    @if ($bill->balance > 0)
                        <button data-bill_id="{{ $bill->id }}"
                            class="pay-btn btn btn-sm bg-green-600 text-white">Pay</button>

                        <button data-bill_id="{{ $bill->id }}"
                            class="cancel-btn btn btn-sm bg-green-600 text-white">Cancel</button>
                    @else
                        <button class="btn bg-gray-200"
                            @click="bills[{{ $i }}].show = !bills[{{ $i }}].show">More details</button>
                        <div class="p-2" x-show="bills[{{ $i }}].show" x-cloak x-transition>
                            <ul class="p-2 list-disc list-inside bg-gray-100">
                                @foreach ($bill->entries as $e)
                                    <li>{{ $e->description }} - {{ $e->total_price }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <span class="inline-grid py-1 px-2 rounded-md text-white bg-red-500">Cancelled</span>
                @endunless
            </div>
        @endforeach
    </div> --}}

    <livewire:billing.visit-bills :visit="$visit" />
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("click", ".pay-btn", (e) => {
                const el = e.currentTarget;
                const billId = $(el).data('bill_id');
                axios.get("{{ route('billing.init-payment', ':bill') }}".replace(":bill", billId))
                    .then((res) => {
                        useGlobalModal((a) => {
                            a.find(MODAL_TITLE).text(`Payment for #${billId}`);
                            a.find(MODAL_CONTENT).html(res.data);
                        });
                    })
                    .catch((err) => {
                        notifyError(err.message);
                    })
            });

            $(document).on("click", ".cancel-btn", (e) => {
                const el = e.currentTarget;
                $(el).attr('disabled', true);
                const billId = $(el).data('bill_id');

                if (confirm("This will delete all previous payments from this bill. Are you sure?") ==
                    false) {
                    $(el).attr('disabled', false);
                    return;
                }

                try {
                    axios.delete("{{ route('billing.cancel-bill', ':bill') }}".replace(":bill", billId))
                        .then((res) => {
                            notifySuccess("Bill cancellation successful.");
                        })
                        .catch((err) => {
                            notifyError(err.message);
                        }).finally(() => $(el).attr('disabled', false));
                } catch (err) {
                    notifyError(err.message || "An error occurred");
                }
            });
        });
    </script>
@endpush
