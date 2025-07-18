@extends('layouts.master2')

@section('title', 'Dashboard')

@section('content')
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('storage/logo_jendra.png') }}" alt="Logo Rajendra" height="200"
            width="200">
    </div>

    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <!-- Blade (dashboard-pemilik.blade.php) -->
            <section class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0 font-weight-bold">Dashboard Pemilik</h1>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h4>Total Tiket Terjual</h4>
                                    <h5>{{ $totalTiketBulanIni }}</h5>
                                    <p style="font-size: 14px;">di bulan ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-ticket fa-3x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h4>Total Pembatalan Tiket</h4>
                                    <h5>{{ $totalPembatalanBulanIni }}</h5>
                                    <p style="font-size: 14px;">di bulan ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-list fa-3x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h4>Pendapatan Perbulan</h4>
                                    <h5>Rp. {{ number_format($pendapatanBulanan, 0, ',', '.') }}</h5>
                                    <p style="font-size: 14px;">Pada Saat Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave fa-3x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h4>Pendapatan Pertahun</h4>
                                    <h5>Rp. {{ number_format($pendapatanTahunan, 0, ',', '.') }}</h5>
                                    <p style="font-size: 14px;">Pada Saat Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Donut Chart -->
                    <div class="row">
                        <!-- Bar chart -->
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="far fa-chart-bar"></i> Tiket Perbulan</h3>
                                </div>
                                <div class="card-body">
                                    <div id="bar-chart" style="height: 300px; width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex">
                            <div class="card card-primary card-outline flex-fill">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="far fa-chart-bar"></i> Tiket Rute Perbulan
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="donut-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Aktivitas Harian -->
                        <div class="col-md-6 d-flex">
                            <div class="card card-outline card-info flex-fill">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-calendar-day"></i> Aktivitas Harian</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartAktivitasHarian" style="height: 300px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </div>
        <!-- /.content-wrapper -->
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
@endsection

@push('scripts')
    <script>
        /* Bar Chart */
        const grafikData = @json($dataGrafik); // array isi 12 elemen

        $(function() {
            var bar_data = {
                data: grafikData.map((val, i) => [i + 1, val]),
                bars: {
                    show: true
                },
            };

            $.plot('#bar-chart', [bar_data], {
                grid: {
                    borderWidth: 1,
                    borderColor: '#f3f3f3',
                    tickColor: '#f3f3f3',
                    markings: function(axes) {
                        let markings = [];
                        for (let y = 0; y <= axes.yaxis.max; y += 1) {
                            markings.push({
                                yaxis: {
                                    from: y,
                                    to: y
                                },
                                color: '#e0e0e0',
                                lineWidth: 1
                            });
                        }
                        return markings;
                    }
                },
                series: {
                    bars: {
                        show: true,
                        barWidth: 0.4,
                        align: 'center'
                    }
                },
                colors: ['#3c8dbc'],
                xaxis: {
                    ticks: [
                        [1, 'Jan'],
                        [2, 'Feb'],
                        [3, 'Mar'],
                        [4, 'Apr'],
                        [5, 'Mei'],
                        [6, 'Jun'],
                        [7, 'Jul'],
                        [8, 'Agu'],
                        [9, 'Sep'],
                        [10, 'Okt'],
                        [11, 'Nov'],
                        [12, 'Des']
                    ]
                },
                yaxis: {
                    min: 0,
                    tickSize: 5,
                    tickDecimals: 0
                }
            });
        });


        var donutData = {!! $donutData !!};

        function labelFormatter(label, series) {
            return '<div style="font-size:13px; text-align:center; padding:2px; color:white;">' +
                label + '<br>' + series.data[0][1] + '</div>';
        }

        $.plot('#donut-chart', donutData, {
            series: {
                pie: {
                    show: true,
                    radius: 1,
                    innerRadius: 0.5,
                    label: {
                        show: true,
                        radius: 2 / 3,
                        formatter: labelFormatter,
                        threshold: 0.1
                    }
                }
            },
            legend: {
                show: false
            }
        });

        /* Bar Chart */
        const aktivitasData = @json($aktivitasHarian);

        const labels = aktivitasData.map(item => item.label);
        const values = aktivitasData.map(item => item.total);

        var aktivitasHarianLabels = {!! json_encode($aktivitasHarian->pluck('label')) !!};
        var aktivitasHarianData = {!! json_encode($aktivitasHarian->pluck('total')) !!};

        const ctx = document.getElementById('chartAktivitasHarian').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pemesanan',
                    data: values,
                    backgroundColor: '#17a2b8',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.y} Tiket`
                        }
                    }
                }
            }
        });
    </script>
@endpush
