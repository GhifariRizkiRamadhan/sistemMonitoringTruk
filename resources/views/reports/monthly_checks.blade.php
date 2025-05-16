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
                Laporan Pengecekan Bulanan
                @if (!empty($truck_filter) && $truck_info)
                    - {{ $truck_info->plate_number }} ({{ $truck_info->category == 'own' ? 'Milik Sendiri' : 'TEP' }})
                @elseif (!empty($category_filter))
                    - Truk {{ $category_filter == 'own' ? 'Milik Sendiri' : 'TEP' }}
                @endif
            </h2>
            <div>
                @if ($checks->isNotEmpty())
                    <div class="btn-group">
                        <a href="{{ route('monthlyChecksReport') }}?export=excel&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('monthlyChecksReport') }}?export=pdf&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('monthlyChecksReport') }}" class="row align-items-end">
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
        
        <!-- Chart Cards -->
        <div class="row mb-4">
            <!-- Truk dengan KM Tinggi -->
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Truk dengan KM > 50,000</h6>
                    </div>
                    <div class="card-body">
                        @if ($high_km_trucks->isEmpty())
                            <p class="text-center">Tidak ada truk dengan KM di atas 50,000</p>
                        @else
                            <div class="chart-bar">
                                <canvas id="highKmChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Truk Perlu Servis Segera -->
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Truk Perlu Servis Segera (Sisa KM < 1,000)</h6>
                    </div>
                    <div class="card-body">
                        @if ($low_service_km_trucks->isEmpty())
                            <p class="text-center">Tidak ada truk yang perlu servis segera</p>
                        @else
                            <div class="chart-bar">
                                <canvas id="lowServiceKmChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Kondisi Truk -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Distribusi Kondisi Truk - {{ date('F Y', mktime(0, 0, 0, $month_filter, 1, $year_filter)) }}</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="conditionChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                @if ($checks->isEmpty())
                    <div class="alert alert-info">
                        Tidak ada data untuk periode yang dipilih.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th style="font-size: 75%;">No. Plat</th>
                                    <th style="font-size: 75%;">Tanggal Pengecekan</th>
                                    <th style="font-size: 75%;">Kondisi Ban</th>
                                    <th style="font-size: 75%;">KM Saat Ini</th>
                                    <th style="font-size: 75%;">Sisa KM Servis</th>
                                    <th style="font-size: 75%;">Kondisi Rem</th>
                                    <th style="font-size: 75%;">Kondisi Kabin</th>
                                    <th style="font-size: 75%;">Kondisi Bak</th>
                                    <th style="font-size: 75%;">Kondisi Lampu</th>
                                    <th style="font-size: 75%;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checks as $check)
                                <tr>
                                    <td style="font-size: 72%;">{{ $check->plate_number }}</td>
                                    <td style="font-size: 80%;">{{ date('d/m/Y', strtotime($check->check_date)) }}</td>
                                    <td style="font-size: 80%;">{!! app('App\Http\Controllers\MonthlyChecksReportController')->getConditionLabel($check->tire_condition) !!}</td>
                                    <td style="font-size: 80%;">{{ number_format($check->current_km) }} KM</td>
                                    <td style="font-size: 80%;">{{ number_format($check->service_km_remaining) }} KM</td>
                                    <td style="font-size: 80%;">{!! app('App\Http\Controllers\MonthlyChecksReportController')->getConditionLabel($check->brake_condition) !!}</td>
                                    <td style="font-size: 80%;">{!! app('App\Http\Controllers\MonthlyChecksReportController')->getConditionLabel($check->cabin_condition) !!}</td>
                                    <td style="font-size: 80%;">{!! app('App\Http\Controllers\MonthlyChecksReportController')->getConditionLabel($check->cargo_area_condition) !!}</td>
                                    <td style="font-size: 80%;">{!! app('App\Http\Controllers\MonthlyChecksReportController')->getConditionLabel($check->lights_condition) !!}</td>
                                    <td style="font-size: 80%;">{{ $check->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
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
    // Chart Truk dengan KM Tinggi
    @if (!$high_km_trucks->isEmpty())
    new Chart(document.getElementById('highKmChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($high_km_trucks->pluck('plate_number')) !!},
            datasets: [{
                label: 'Kilometer',
                data: {!! json_encode($high_km_trucks->pluck('current_km')) !!},
                backgroundColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('id-ID') + ' KM';
                        }
                    }
                }
            }
        }
    });
    @endif

    // Chart Truk Perlu Servis Segera
    @if (!$low_service_km_trucks->isEmpty())
    new Chart(document.getElementById('lowServiceKmChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($low_service_km_trucks->pluck('plate_number')) !!},
            datasets: [{
                label: 'Sisa KM Sebelum Servis',
                data: {!! json_encode($low_service_km_trucks->pluck('service_km_remaining')) !!},
                backgroundColor: '#e74a3b',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('id-ID') + ' KM';
                        }
                    }
                }
            }
        }
    });
    @endif

    // Chart Distribusi Kondisi Truk
    const conditionData = {
        labels: ['Ban', 'Rem', 'Kabin', 'Bak', 'Lampu'],
        datasets: [
            {
                label: 'Baik',
                data: [
                    {{ $condition_stats->good_tire ?? 0 }},
                    {{ $condition_stats->good_brake ?? 0 }},
                    {{ $condition_stats->good_cabin ?? 0 }},
                    {{ $condition_stats->good_cargo ?? 0 }},
                    {{ $condition_stats->good_lights ?? 0 }}
                ],
                backgroundColor: '#1cc88a'
            },
            {
                label: 'Kurang',
                data: [
                    {{ $condition_stats->fair_tire ?? 0 }},
                    {{ $condition_stats->fair_brake ?? 0 }},
                    {{ $condition_stats->fair_cabin ?? 0 }},
                    {{ $condition_stats->fair_cargo ?? 0 }},
                    {{ $condition_stats->fair_lights ?? 0 }}
                ],
                backgroundColor: '#f6c23e'
            },
            {
                label: 'Perlu Perbaikan',
                data: [
                    {{ $condition_stats->bad_tire ?? 0 }},
                    {{ $condition_stats->bad_brake ?? 0 }},
                    {{ $condition_stats->bad_cabin ?? 0 }},
                    {{ $condition_stats->bad_cargo ?? 0 }},
                    {{ $condition_stats->bad_lights ?? 0 }}
                ],
                backgroundColor: '#e74a3b'
            }
        ]
    };

    new Chart(document.getElementById('conditionChart'), {
        type: 'bar',
        data: conditionData,
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
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