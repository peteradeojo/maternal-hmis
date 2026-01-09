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

        <div class="p-1"></div>

        <div class="grid grid-cols-3 gap-4">
            <div class="card p-4">
                <div class="text-center card-header">Payments Breakdown</div>
                <canvas id="payment-types"></canvas>
            </div>
            <div class="card p-4">
                <div class="text-center card-header">Payments Today</div>
                <canvas id="today-payments"></canvas>
            </div>
        </div>
        <div class="p-1"></div>
        <div class="card p-4 bill-trends">
            <div class="text-center card-header">Bill trends</div>
            <div class="flex-center gap-x-4">
                <button id="previous" class="btn bg-primary text-white">&prec;</button>
                <div class="w-full">
                    <canvas id="bill-trends"></canvas>
                </div>
                <button id="next" class="btn bg-primary text-white">&gt;</button>
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
                    labels: [
                        {!! join(',', array_map(fn($p) => "'" . ucfirst($p->method) . "'", $payments)) !!}
                    ],
                    datasets: [{
                        label: 'Payment history',
                        data: [
                            {{ join(',', array_map(fn($p) => "{$p->amount}", $payments)) }}
                        ],
                        backgroundColor: [
                            'green', PRIMARY_COLOR, 'orange', 'pink', 'red'
                        ],
                    }, ],
                }
            });

            new Chart(document.getElementById('today-payments'), {
                type: 'doughnut',
                data: {
                    labels: [
                        {!! join(',', array_map(fn($p) => "'" . ucfirst($p->method) . "'", $today)) !!}
                    ],
                    datasets: [{
                        label: 'Payment history',
                        data: [
                            {{ join(',', array_map(fn($p) => "{$p->amount}", $today)) }}
                        ],
                        backgroundColor: [
                            'green', PRIMARY_COLOR, 'orange', 'pink', 'red'
                        ],
                    }, ],
                }
            });

            const billChart = new Chart(
                document.querySelector("#bill-trends"), {
                    type: 'bar',
                    data: {
                        datasets: [],
                        labels: [],
                    },
                    options: {
                        elements: {
                            line: {
                                tension: 0.18,
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                stacked: true,
                            },
                            y: {
                                stacked: true,
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: true,
                    }
                }
            );

            // axios.get("{{ route('finance.charts.bill-trend') }}")
            //     .then(({
            //         data
            //     }) => {

            //     })
            //     .catch((err) => {
            //         console.error(err);
            //     });

            let page = 0;

            const updateChart = (inc = 0) => {
                if ((page + inc) < 0) page = 0;
                else page += inc;

                getBillTrend(page);
            }

            const getBillTrend = async (increment = 0) => {
                try {
                    const {
                        data
                    } = await axios.get(`{{ route('finance.charts.bill-trend') }}?ago=${increment}`);

                    const labels = [];
                    const datasets = [{
                            label: "Paid",
                            data: [],
                        },
                        {
                            label: "Cancelled",
                            data: [],
                        },
                    ];

                    data.forEach(point => {
                        labels.push(point.created);
                        const vals = JSON.parse(point.status_counts);
                        datasets[0].data.push(vals[7] ?? 0);
                        datasets[1].data.push(vals[9] ?? 0);
                    });

                    setChartData(billChart, labels, datasets);
                    console.log(res);
                } catch (error) {

                }
            };

            updateChart();

            document.querySelector("button#previous").addEventListener('click', () => updateChart(1));
            document.querySelector("button#next").addEventListener('click', () => updateChart(-1));
        });
    </script>
@endpush
