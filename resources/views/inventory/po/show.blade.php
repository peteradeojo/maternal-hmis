@extends('layouts.app')
@section('title', 'Purchase order | ' . $order->po_number)

@section('content')
    <div class="card" x-data>
        <x-back-link to="{{route('phm.inventory.purchases')}}"  />
        <p class="basic-header">{{ $order->po_number }}</p>

        <form @submit.prevent="saveOrder" method="post">
            @csrf

            <x-inventory.purchase-order x-on:inventory-product-selected="$store.order.purchases.push($event.detail.product)"
                :suppliers="$suppliers" :order="$order"></x-inventory.purchase-order>

            <div class="form-group sm:w-1/3">
                <label>Status</label>
                <select x-model="$store.order.status" name="status" class="form-control" required>
                    <option value="{{ Status::pending->value }}">{{ Status::pending->name }}</option>
                    <option value="{{ Status::completed->value }}">{{ Status::completed->name }}</option>
                    <option value="{{ Status::cancelled->value }}">{{ Status::cancelled->name }}</option>
                </select>
            </div>
            <div class="form-group">
                <button class="btn bg-blue-400 text-white">Save <i class="fa fa-save"></i></button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('order', {
                purchases: @js($order->lines),
                supplier_id: null,
                status: {{ $order->status }}
            });
        });

        async function saveOrder() {
            const data = Alpine.store('order');

            try {
                const res = await axios.post("{{ route('phm.inventory.order', $order) }}", {
                    orders: data.purchases,
                    supplier_id: data.supplier_id,
                    status: data.status,
                });

                location.reload();
            } catch (error) {
                notifyError(error.response?.data.message || error.message);
            }
        }
    </script>
@endpush
