@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="tf-section-2 mb-30">
                <div class="flex gap20 flex-wrap-mobile">
                    <div class="w-half">
                        <div class="wg-chart-default mb-20">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-shopping-bag"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Total Orders</div>
                                        <h4>{{ $orders->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wg-chart-default mb-20">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Total Amount</div>
                                        <h4>{{ $orders->sum('total') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wg-chart-default mb-20">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-shopping-bag"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Pending Orders</div>
                                        <h4>{{ $orders->pluck('status')->countBy()->get('ordered', 0) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wg-chart-default">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Pending Orders Amount</div>
                                        <h4>{{ $ordered_sum }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-half">
                        <div class="wg-chart-default mb-20">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-shopping-bag"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Delivered Orders</div>
                                        <h4>{{ $orders->pluck('status')->countBy()->get('delivered', 0) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wg-chart-default mb-20">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Delivered Orders Amount</div>
                                        <h4>{{ $delivered_sum }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wg-chart-default mb-20">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-shopping-bag"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Canceled Orders</div>
                                        <h4>{{ $orders->pluck('status')->countBy()->get('canceled', 0) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="wg-chart-default">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap14">
                                    <div class="image ic-bg">
                                        <i class="icon-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <div class="body-text mb-2">Canceled Orders Amount</div>
                                        <h4>{{ $canceled_sum }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="wg-box">




                    <div class="flex items-center justify-between">
                        <h5>Earnings revenue</h5>
                        <div class="dropdown default">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                <span class="icon-more"><i class="icon-more-horizontal"></i></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0);">This Week</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Last Week</a>
                                </li>
                            </ul>
                        </div>
                    </div>



                    <div class="flex flex-wrap gap40">
                        <div>
                            <div class="mb-2">
                                <div class="block-legend">
                                    <div class="dot t1"></div>
                                    <div class="text-tiny">Revenue</div>
                                </div>
                            </div>
                            <div class="flex items-center gap10">
                                <h4>$37,802</h4>
                                <div class="box-icon-trending up">
                                    <i class="icon-trending-up"></i>
                                    <div class="body-title number">0.56%</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-2">
                                <div class="block-legend">
                                    <div class="dot t2"></div>
                                    <div class="text-tiny">Order</div>
                                </div>
                            </div>
                            <div class="flex items-center gap10">
                                <h4>$28,305</h4>
                                <div class="box-icon-trending up">
                                    <i class="icon-trending-up"></i>
                                    <div class="body-title number">0.56%</div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div id="line-chart-8"></div>
                </div>

            </div>
            <div class="tf-section mb-30">

                <div class="wg-box">
                    <div class="flex items-center justify-between">
                        <h5>Recent orders</h5>
                        <div class="dropdown default">
                            <a class="btn btn-secondary dropdown-toggle" href="{{ route('admin.orders') }}">
                                <span class="view-all">View all</span>
                            </a>
                        </div>
                    </div>
                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 80px">OrderNo</th>
                                    <th>Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Tax</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Total Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($orders->count() > 5)
                                    @for($i = 0; $i < 5; $i += 1)
                                        <tr>
                                            <td class="text-center">{{ $orders[$i]->id }}</td>
                                            <td class="text-center">{{ $orders[$i]->name }}</td>
                                            <td class="text-center">{{ $orders[$i]->phone }}</td>
                                            <td class="text-center">${{ $orders[$i]->subtotal }}</td>
                                            <td class="text-center">${{ $orders[$i]->tax }}</td>
                                            <td class="text-center">${{ $orders[$i]->total }}</td>
                                            <td class="text-center">
                                                @if($orders[$i]->status == 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif($orders[$i]->status == 'canceled')
                                                    <span class="badge bg-danger">Canceled</span>
                                                @else
                                                    <span class="badge bg-warning">Ordered</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $orders[$i]->created_at }}</td>
                                            <td class="text-center">{{ $orders[$i]->orderItems->count() }}</td>
                                            <td>{{ $orders[$i]->delivered_date }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.order.details', ['id' => $orders[$i]->id]) }}">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="icon-eye"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    @endfor
                                @else
                                    @for($i = 0; $i < $orders->count(); $i += 1)
                                        <tr>
                                            <td class="text-center">{{ $orders[$i]->id }}</td>
                                            <td class="text-center">{{ $orders[$i]->name }}</td>
                                            <td class="text-center">{{ $orders[$i]->phone }}</td>
                                            <td class="text-center">${{ $orders[$i]->subtotal }}</td>
                                            <td class="text-center">${{ $orders[$i]->tax }}</td>
                                            <td class="text-center">${{ $orders[$i]->total }}</td>
                                            <td class="text-center">
                                                @if($orders[$i]->status == 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif($orders[$i]->status == 'canceled')
                                                    <span class="badge bg-danger">Canceled</span>
                                                @else
                                                    <span class="badge bg-warning">Ordered</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $orders[$i]->created_at }}</td>
                                            <td class="text-center">{{ $orders[$i]->orderItems->count() }}</td>
                                            <td>{{ $orders[$i]->delivered_date }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.order.details', ['id' => $orders[$i]->id]) }}">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="icon-eye"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    @endfor
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--@push('scripts')--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>--}}
{{--    <script>--}}
{{--        // Function to update chart data--}}
{{--        function updateChartData(period) {--}}
{{--            // Fetch data for the selected period--}}
{{--            fetch('/admin/earnings-data?period=' + period)--}}
{{--                .then(response => response.json())--}}
{{--                .then(data => {--}}
{{--                    // Update chart data--}}
{{--                    myChart.data.datasets[0].data = data.revenue;--}}
{{--                    myChart.data.labels = data.dates;--}}
{{--                    myChart.update();--}}
{{--                });--}}
{{--        }--}}

{{--        // Initialize chart--}}
{{--        const ctx = document.getElementById('line-chart-8').getContext('2d');--}}
{{--        const myChart = new Chart(ctx, {--}}
{{--            type: 'line',--}}
{{--            data: {--}}
{{--                labels: [], // Will be populated with dates--}}
{{--                datasets: [{--}}
{{--                    label: 'Earnings Revenue',--}}
{{--                    data: [], // Will be populated with revenue data--}}
{{--                    backgroundColor: 'rgba(255, 99, 132, 0.2)',--}}
{{--                    borderColor: 'rgba(255, 99, 132, 1)',--}}
{{--                    borderWidth: 1--}}
{{--                }]--}}
{{--            },--}}
{{--            options: {--}}
{{--                scales: {--}}
{{--                    y: {--}}
{{--                        beginAtZero: true--}}
{{--                    }--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        // Update chart with initial data--}}
{{--        updateChartData('this_week');--}}

{{--        // Handle dropdown selection--}}
{{--        document.querySelector('.dropdown-menu').addEventListener('click', function(event) {--}}
{{--            if (event.target.tagName === 'A') {--}}
{{--                const period = event.target.textContent.toLowerCase().replace(/\s+/g, '_');--}}
{{--                updateChartData(period);--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}
