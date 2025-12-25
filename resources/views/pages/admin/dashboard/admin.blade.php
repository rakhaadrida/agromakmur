@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-3">
            <h1 class="h3 mb-0 text-gray-800 menu-title">Dashboard</h1>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-md font-weight-bold text-primary text-uppercase mb-2">Transaksi (Total)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ formatQuantity($totalMonthlyTransaction) }} ({{ formatQuantity($totalAnnualTransaction) }})</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-md font-weight-bold text-success text-uppercase mb-2">Faktur Belum Cetak</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ formatQuantity($totalUnprintedInvoice) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-print fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-md font-weight-bold text-info text-uppercase mb-2">Faktur Pending</div>
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ formatQuantity($totalPendingInvoice) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-spinner fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-md font-weight-bold text-warning text-uppercase mb-2">Re-stok Barang</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ formatQuantity($totalLowStockProduct) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-recycle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-dark">Transaksi Terakhir</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <div class="table-stats order-table">
                                <table class="table table-striped">
                                    <thead class="bg-info text-center">
                                    <tr>
                                        <th style="width: 20px">#</th>
                                        <th style="width: 140px">Nomor SO</th>
                                        <th style="width: 120px">Tanggal SO</th>
                                        <th>Customer</th>
                                        <th style="width: 120px">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($latestTransactions as $key => $transaction)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td class="text-center">{{ $transaction->number }}</td>
                                            <td class="text-center">{{ formatDateIso($transaction->date, 'DD-MMM-YY') }}</td>
                                            <td>{{ $transaction->customer_name }}</td>
                                            <td class="text-right">{{ formatPrice($transaction->grand_total) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-dark">Status Faktur</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pb-1">
                            <canvas id="myPieChart"></canvas>
                        </div>
                        <div class="mt-2 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> {{ \App\Utilities\Constant::SALES_ORDER_STATUS_ACTIVE }}
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-danger"></i> {{ \App\Utilities\Constant::SALES_ORDER_STATUS_CANCELLED }}
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> {{ \App\Utilities\Constant::SALES_RETURN_STATUS_UPDATED }}
                            </span>
                            <br>
                            <span class="mr-2">
                                <i class="fas fa-circle text-secondary"></i> {{ \App\Utilities\Constant::SALES_ORDER_STATUS_WAITING_APPROVAL }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ url('assets/vendor/chart.js/Chart.min.js') }}"></script>
    <script type="text/javascript">
        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';
        Chart.defaults.global.defaultFontSize = '14';

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(',', '').replace(' ', '');
            let n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    let k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        let ctxPie = document.getElementById("myPieChart");
        let myPieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: @json($transactionStatuses),
                datasets: [{
                    data: @json($transactionPerStatus),
                    backgroundColor: ['#4e73df', '#e74a3b', '#1cc88a', '#858796'],
                    hoverBackgroundColor: ['#2d59d9', '#e22d1c', '#05a86d', '#636369'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false,
                },
                cutoutPercentage: 70,
            },
        });
    </script>
@endpush