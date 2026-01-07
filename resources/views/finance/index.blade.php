@extends('layouts.app')
@section('title', 'Finance Dashboard')

@section('content')
    <div class="container">
        <div class="grid grid-cols-2 gap-4">
            <div class="card p-2">
                <div class="card-header">
                    Payment History
                </div>
                <div class="footer">
                    <a href="{{ route('dashboard') }}" class="link">View Payments &rarr;</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="card p-4">
                <div class="card-header">Today</div>
                <canvas id="payment-types"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            new Chart(document.getElementById('payment-types'), {
                type: 'doughnut',
                data: {
                    labels: ['Cash', 'Bank', 'Card', 'Insurance'],
                    datasets: [
                        {
                            label: 'First dataset',
                            data: [100, 150, 200, 40],
                            backgroundColor: [
                                'black', 'blue', 'orange', 'pink'
                            ],
                        },
                    ],
                }
            });
        });
    </script>
@endpush
