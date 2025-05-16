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
                Laporan Keuangan
                @if (!empty($truck_filter) && $truck_info)
                    - {{ $truck_info->plate_number }} ({{ $truck_info->category == 'own' ? 'Milik Sendiri' : 'TEP' }})
                @elseif (!empty($category_filter))
                    - Truk {{ $category_filter == 'own' ? 'Milik Sendiri' : 'TEP' }}
                @endif
            </h2>
            <div>
                @if (!empty($financial_data))
                    <div class="btn-group">
                        <a href="{{ route('financialReport') }}?export=excel&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('financialReport') }}?export=pdf&truck_id={{ $truck_filter }}&category={{ $category_filter }}&month={{ $month_filter }}&year={{ $year_filter }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('financialReport') }}" class="row align-items-end">
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
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Penghasilan Kotor</h5>
                        <h3>Rp {{ number_format($total_income, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengeluaran</h5>
                        <h3>Rp {{ number_format($total_expense, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Penghasilan Bersih</h5>
                        <h3>Rp {{ number_format($total_net_income, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                @if (empty($financial_data))
                    <div class="alert alert-info">
                        Tidak ada data untuk periode yang dipilih.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>No. Plat</th>
                                    <th>Kategori</th>
                                    <th>Penghasilan Kotor</th>
                                    <th>Total Pengeluaran</th>
                                    <th>Cicilan</th>
                                    <th>Penghasilan Bersih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($financial_data as $row)
                                <tr>
                                    <td>{{ $row['plate_number'] }}</td>
                                    <td>{{ $row['category'] == 'own' ? 'Milik Sendiri' : 'TEP' }}</td>
                                    <td>Rp {{ number_format($row['gross_income'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($row['total_expense'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($row['installment'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($row['net_income'], 0, ',', '.') }}</td>
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
<script>
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