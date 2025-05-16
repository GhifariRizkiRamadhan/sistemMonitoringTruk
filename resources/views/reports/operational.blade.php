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
                Laporan Pengeluaran Operasional
                @if (!empty($truck_filter) && $truck_info)
                    - {{ $truck_info->plate_number }} ({{ $truck_info->category == 'own' ? 'Milik Sendiri' : 'TEP' }})
                @elseif (!empty($category_filter))
                    - Truk {{ $category_filter == 'own' ? 'Milik Sendiri' : 'TEP' }}
                @endif
            </h2>
            <div>
                @if ($expenses->isNotEmpty())
                    <div class="btn-group">
                        <a href="{{ route('operationalReport') }}?export=excel&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('operationalReport') }}?export=pdf&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('operationalReport') }}" class="row align-items-end">
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
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Total Operasional</h6>
                    </div>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_operasional }} Item</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tools fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Total Pengeluaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Distribusi Pengeluaran -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Jenis Pengeluaran - {{ date('F Y', mktime(0, 0, 0, $month_filter, 1, $year_filter)) }}</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="expenseChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($expenses->isEmpty())
                    <div class="alert alert-info">
                        Tidak ada data untuk periode yang dipilih.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>No. Plat</th>
                                    <th>Item</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                    <th>Quantity</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_amount = 0;
                                @endphp
                                @foreach($expenses as $expense)
                                    @php
                                        $jumlah = $expense->quantity * $expense->price;
                                        $total_amount += $jumlah;
                                    @endphp
                                    <tr>
                                        <td>{{ $expense->plate_number }}</td>
                                        <td>{{ $expense->operational_type }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>{{ date('d/m/Y', strtotime($expense->date)) }}</td>
                                        <td>{{ $expense->quantity }}</td>
                                        <td>Rp {{ number_format($expense->price, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Total Pengeluaran:</th>
                                    <th>Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</th>
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
    // Chart Distribusi Pengeluaran
    const expenseTypeData = @json($expense_by_type);
    
    new Chart(document.getElementById('expenseChart'), {
        type: 'pie',
        data: {
            labels: expenseTypeData.map(item => item.name),
            datasets: [{
                data: expenseTypeData.map(item => item.total_expense),
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
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
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