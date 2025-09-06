@extends('layouts.admin')

@section('content')
    <h1>Dashboard Admin</h1>
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Masyarakat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Resident::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Kategori Laporan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\ReportCategory::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Laporan
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ \App\Models\Report::count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Laporan</h6>
                    <div>
                        <select id="filter" class="custom-select custom-select-sm form-control form-control-sm">
                            <option value="7days" {{ $filter == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>5 Tahun Terakhir</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height:350px;">
                        <canvas id="reportChart" style="height:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jumlah Laporan per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height:350px; display:flex; align-items:center; justify-content:center;">
                        <canvas id="pieCategoryChart" style="height:100% !important; max-height:350px !important;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kategori Laporan Terbanyak</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Jumlah Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCategories as $i => $cat)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $cat->name }}</td>
                                    <td>{{ $cat->reports_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pengguna dengan Laporan Terbanyak</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
                                <th>Jumlah Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topUsers as $i => $user)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->resident->reports_count ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Line chart laporan
            const canvas = document.getElementById('reportChart');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: [{
                            label: 'Total Laporan',
                            data: @json($data),
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            pointBackgroundColor: '#4e73df',
                            pointBorderColor: '#4e73df',
                            pointHoverBackgroundColor: '#4e73df',
                            pointHoverBorderColor: '#4e73df',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 10,
                                right: 25,
                                top: 25,
                                bottom: 0
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false }
                            },
                            y: {
                                ticks: { beginAtZero: true, stepSize: 1 },
                                grid: {
                                    color: "#e3e6f0",
                                    zeroLineColor: "#e3e6f0",
                                    drawBorder: false,
                                    borderDash: [2],
                                    zeroLineBorderDash: [2]
                                }
                            }
                        }
                    }
                });
            }

            // Pie chart kategori laporan
            const pieCanvas = document.getElementById('pieCategoryChart');
            if (pieCanvas) {
                const pieCtx = pieCanvas.getContext('2d');
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: @json($pieLabels),
                        datasets: [{
                            data: @json($pieData),
                            backgroundColor: [
                                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#fd7e14', '#20c997', '#6f42c1'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true, position: 'bottom' }
                        }
                    }
                });
            }

            document.getElementById('filter').addEventListener('change', function () {
                window.location = '?filter=' + this.value;
            });
        });
    </script>
@endsection