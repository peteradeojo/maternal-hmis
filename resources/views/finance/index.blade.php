@extends('layouts.app')
@section('title', 'Finance Dashboard')

@section('content')
    <div class="container grid gap-4">
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

        <div class="grid grid-cols-2">
            <div class="card p-4">
                <div class="text-center card-header">Service Breakdown</div>
                <div class="flex-center gap-x-4">
                    <button class="btn bg-primary text-white">&prec;</button>
                    <div class="w-full">
                        <canvas id="services"></canvas>
                    </div>
                    <button class="btn bg-primary text-white">&gt;</button>
                </div>
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

            let page = 0;

            const updateBillChart = (inc = 0) => {
                if ((page + inc) < 0) {
                    if (page == 0) return;
                    page = 0;
                } else page += inc;

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
                        labels.push(parseDateFromSource(point.created, false, false));
                        const vals = JSON.parse(point.status_counts);
                        datasets[0].data.push(vals[7] ?? 0);
                        datasets[1].data.push(vals[9] ?? 0);
                    });

                    setChartData(billChart, labels, datasets);
                } catch (error) {
                    console.error(error);
                    notifyError(error.message);
                }
            };

            updateBillChart();

            document.querySelector("button#previous").addEventListener('click', () => updateBillChart(1));
            document.querySelector("button#next").addEventListener('click', () => updateBillChart(-1));

            try {
                const services = new Chart(document.querySelector('#services'), {
                    type: 'bar',
                    options: {
                        indexAxis: 'y',
                        plugins: {
                            title: {
                                display: true,
                                text: 'Services breakdown',
                            },
                            legend: {
                                display: false,
                            },
                        },
                        elements: {
                            bar: {
                                borderWidth: 2,
                            },
                        },
                        responsive: true,

                    },
                    data: {
                        datasets: [
                            {
                                label: 'Services breakdown',
                                axis: 'y',
                                barPercentage: 0.2,
                                barThickness: 20,
                                maxBarThickness: 25,
                                borderRadius: 4,
                                data: [10, 20, 14, 8],
                                backgroundColor: [
                                    'yellow',
                                    'red',
                                    'blue',
                                    'green'
                                ],
                            }
                        ],
                        labels: ['Lab', 'Consultation', 'Pharmacy', 'Radiology'],
                    },
                });
            } catch (error) {
                console.error(error);
                // notifyError(error);
            }
        });
    </script>
@endpush
