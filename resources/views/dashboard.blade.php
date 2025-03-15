<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
@extends('layouts.app')

@section('content')
<div class="content" style="width: 100%; margin-top: 3.5%;">
    <div class="container-fluid">
        <!-- Baris Pertama: 3 card (Lifetime) -->
        <div class="row">
            <!-- Total Pengeluaran Sejak Awal -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total Pengeluaran
                                </div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ formatRupiah($lifetime_expense->lifetime_total_expense ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Profit Sejak Awal -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Profit
                                </div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ formatRupiah($lifetime_profit) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Tonase Sejak Awal -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Tonase
                                </div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ number_format($total_tonnage_data->total_tonnage ?? 0, 2, ',', '.') }} Ton
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-weight-hanging fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris Kedua: 4 card (Bulanan + Total Truk) -->
        <div class="row">
            <!-- Total Truk -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Truk</div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ $truck_stats->total }}
                                    <small class="text-muted">
                                        ({{ $truck_stats->own_trucks }} Sendiri, 
                                        {{ $truck_stats->tep_trucks }} TEP)
                                    </small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penghasilan Bulan Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Penghasilan Bulan Ini
                                </div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ formatRupiah($income_stats->total_income ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengeluaran Bulan Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Pengeluaran Bulan Ini
                                </div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ formatRupiah($expense_stats->total_expense ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tools fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit Bulan Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Profit Bulan Ini
                                </div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ formatRupiah($profit_this_month) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik & Muatan Terpopuler -->
        <div class="row">
            <!-- Grafik Pendapatan 6 Bulan Terakhir -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Pendapatan 6 Bulan Terakhir
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="incomeChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Muatan Terpopuler -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Muatan Terpopuler
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie">
                            <canvas id="cargoChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru & Peringatan Servis -->
        <div class="row">
            <!-- Aktivitas Terbaru -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Aktivitas Terbaru
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach($recent_activities as $activity)
                                    <tr>
                                        <td width="100">
                                            {{ \Carbon\Carbon::parse($activity->date)->format('d/m/Y') }}
                                        </td>
                                        <td width="120">
                                            {{ $activity->plate_number }}
                                        </td>
                                        <td>
                                            {{ $activity->description }}
                                        </td>
                                        <td class="text-right 
                                            {{ $activity->type == 'expense' ? 'text-danger' : 'text-success' }}">
                                            Bersih  
                                            {{ formatRupiah(abs($activity->amount)) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peringatan Servis -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Peringatan Servis
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($service_alerts->isEmpty())
                            <p class="text-success mb-0">
                                <i class="fas fa-check-circle"></i> 
                                Tidak ada truk yang memerlukan servis segera.
                            </p>
                        @else
                            @foreach($service_alerts as $alert)
                                <div class="alert alert-warning mb-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>{{ $alert->plate_number }}</strong>
                                    perlu servis ({{ number_format($alert->service_km_remaining) }} KM tersisa)
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kalender Operasional Truk -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Kalender Operasional Truk
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="truckFilter" class="form-label">
                                Pilih Truk:
                            </label>
                            <select id="truckFilter" class="form-select" style="max-width: 300px;">
                                <option value="all">All Trucks</option>
                                @foreach($truck_list as $tk)
                                    <option value="{{ $tk['id'] }}">
                                        {{ $tk['plate_number'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="calendar" style="max-width: 1000px; margin: 0 auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Grafik Pendapatan 6 Bulan
const monthlyData = @json($monthly_stats);
new Chart(document.getElementById('incomeChart'), {
    type: 'line',
    data: {
        labels: monthlyData.map(item => {
            const [year, month] = item.month.split('-');
            const date = new Date(year, month - 1);
            return date.toLocaleDateString('id-ID', { month: 'short', year: '2-digit' });
        }),
        datasets: [{
            label: 'Pendapatan',
            data: monthlyData.map(item => item.income),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: true
            }
        }
    }
});

// Chart Muatan Terpopuler
const cargoData = @json($popular_cargo);
new Chart(document.getElementById('cargoChart'), {
    type: 'doughnut',
    data: {
        labels: cargoData.map(item => item.name),
        datasets: [{
            data: cargoData.map(item => item.total_tonnage),
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b'
            ],
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value.toLocaleString('id-ID')} ton (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '60%'
    }
});
</script>

@endsection
 
</body>
</html>