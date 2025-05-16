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
<div class="content" style="width: 100%; margin-top: 5.5%;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                Laporan Pendapatan
                @if (!empty($truck_filter) && $truck_info)
                    - {{ $truck_info->plate_number }} ({{ $truck_info->category == 'own' ? 'Milik Sendiri' : 'TEP' }})
                @elseif (!empty($category_filter))
                    - Truk {{ $category_filter == 'own' ? 'Milik Sendiri' : 'TEP' }}
                @endif
            </h2>
            <div>
                @if ($shipments->isNotEmpty())
                    <div class="btn-group">
                        <a href="{{ route('incomeReport') }}?export=excel&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" 
                           class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('incomeReport') }}?export=pdf&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" 
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('incomeReport') }}" class="row align-items-end">
                    <div class="col-md-3">
                        <label for="truck_id" class="form-label">Truk</label>
                        <select class="form-select" id="truck_id" name="truck_id">
                            <option value="">Semua Truk</option>
                            @foreach($trucks as $truck)
                                <option value="{{ $truck->id }}" {{ $truck_filter == $truck->id ? 'selected' : '' }}>
                                    {{ $truck->plate_number }} ({{ $truck->category == 'own' ? 'Milik Sendiri' : 'TEP' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Semua Kategori</option>
                            <option value="own" {{ $category_filter == 'own' ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="tep" {{ $category_filter == 'tep' ? 'selected' : '' }}>TEP</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="month" class="form-label">Bulan</label>
                        <select class="form-select" id="month" name="month">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                        {{ $month_filter == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="year" class="form-label">Tahun</label>
                        <select class="form-select" id="year" name="year">
                            @php
                            $current_year = date('Y');
                            @endphp
                            @for($i = $current_year - 5; $i <= $current_year; $i++)
                                <option value="{{ $i }}" {{ $year_filter == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Info Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Total Angkutan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $chart_data['total_shipments'] }} Trip</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Total Ongkos Angkut</h6>
                    </div>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($chart_data['total_transport_cost'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Total Hasil</h6>
                    </div>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($chart_data['total_results'], 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Tren Pendapatan -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tren Pendapatan Harian - {{ date('F Y', mktime(0, 0, 0, $month_filter, 1, $year_filter)) }}</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="incomeChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                @if ($shipments->isEmpty())
                    <div class="alert alert-info">
                        Tidak ada data untuk periode yang dipilih.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th style="font-size: 75%;">No. Plat</th>
                                    <th style="font-size: 75%;">Muatan</th>
                                    <th style="font-size: 75%;">Tanggal Uang Jalan</th>
                                    <th style="font-size: 75%;">Tanggal Muat</th>
                                    <th style="font-size: 75%;">Tanggal Bongkar</th>
                                    <th style="font-size: 75%;">Tonase</th>
                                    <th style="font-size: 75%;">Upah/Ton</th>
                                    <th style="font-size: 75%;">Ongkos Angkut</th>
                                    <th style="font-size: 75%;">Uang Jalan</th>
                                    <th style="font-size: 75%;">Hasil Truk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shipments as $shipment)
                                @php
                                    $ongkos_angkut = $shipment->tonnage * $shipment->wage_per_ton;
                                    $hasil_truk = $ongkos_angkut - $shipment->travel_money;
                                @endphp
                                <tr>
                                    <td>{{ $shipment->plate_number }}</td>
                                    <td>{{ $shipment->cargo_type }}</td>
                                    <td>{{ date('d/m/Y', strtotime($shipment->travel_money_date)) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($shipment->loading_date)) }}</td>
                                    <td>{{ date('d/m/Y', strtotime($shipment->unloading_date)) }}</td>
                                    <td>{{ number_format($shipment->tonnage, 2) }}</td>
                                    <td>Rp {{ number_format($shipment->wage_per_ton, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($ongkos_angkut, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($shipment->travel_money, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($hasil_truk, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end">Total:</th>
                                    <th>Rp {{ number_format($total_ongkos, 0, ',', '.') }}</th>
                                    <th>Rp {{ number_format($total_uang_jalan, 0, ',', '.') }}</th>
                                    <th>Rp {{ number_format($total_hasil, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Tren Pendapatan
const dailyData = @json($daily_income);
const ctx = document.getElementById('incomeChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: dailyData.map(item => item.date),
        datasets: [{
            label: 'Hasil Pendapatan',
            data: dailyData.map(item => item.hasil),
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
        }
    }
});

// Script untuk memastikan filter truk dan kategori tidak digunakan bersamaan
$(document).ready(function() {
    // Ketika dropdown truk berubah
    $('#truck_id').change(function() {
        if ($(this).val() !== '') {
            // Jika truk spesifik dipilih, reset filter kategori
            $('#category').val('');
        }
    });
    
    // Ketika dropdown kategori berubah
    $('#category').change(function() {
        if ($(this).val() !== '') {
            // Jika kategori dipilih, reset filter truk
            $('#truck_id').val('');
        }
    });
});
</script>
@endsection
</body>
</html>