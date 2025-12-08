@extends('layouts.app')

@section('content')
    <div class="card" x-data>
        <x-back-link />

        <p class="basic-header">Create Purchase Order</p>

        <form action="" method="post"
            @submit.prevent="submitPurchaseOrder()">
            @csrf
            <x-inventory.purchase-order :order="null" :suppliers="$suppliers"
                x-on:inventory-product-selected="addToPurchases($event.detail.purchases, $event.detail.product)"></x-inventory.purchase-order>

            <div class="form-group">
                <button class="btn bg-blue-400 text-white">Submit <i class="fa fa-save"></i></button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        async function submitPurchaseOrder() {
            const order = Alpine.store('order');
            try {
                const res = await axios.post("{{ route('phm.inventory.new-order') }}", {
                    orders: order.purchases,
                    supplier_id: order.supplier_id,
                });
                location.href = "{{ route('phm.inventory.purchases') }}";
            } catch (error) {
                notifyError(error.response.data.message ?? err.message);
            }
        }

        function addToPurchases(purchases, newLine) {
            if (purchases.filter((e, i) => e.item.id == newLine.item.id).length > 0) {
                notifyError("Purchase already added for this item.");
                return;
            }

            purchases.push(newLine);
        }

        document.addEventListener('alpine:init',() => {
            Alpine.store('order', {
                purchases: [],
                supplier_id: null,
                lot: {},
                in_lot: false,
            });
        });
    </script>
@endpush
