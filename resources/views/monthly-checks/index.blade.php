<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @extends('layouts.app')

@section('content')
<div class="content" style="width: 100%; margin-top: 5.5%;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Pengecekan Bulanan</h2>
            <a href="{{ route('monthly-checks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pengecekan
            </a>
        </div>
        
        @if (session('message'))
            <div class="alert alert-{{ session('message_type') }} alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('monthly-checks.index') }}" class="row align-items-end">
                    <div class="col-md-3">
                        <label for="truck_id" class="form-label">Truk</label>
                        <select class="form-select" id="truck_id" name="truck_id">
                            <option value="">Semua Truk</option>
                            @foreach($trucks as $truck)
                                <option value="{{ $truck->id }}" 
                                        {{ $truckFilter == $truck->id ? 'selected' : '' }}>
                                    {{ $truck->plate_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="month" class="form-label">Bulan</label>
                        <select class="form-select" id="month" name="month">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                        {{ $monthFilter == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="year" class="form-label">Tahun</label>
                        <select class="form-select" id="year" name="year">
                            @php
                            $currentYear = date('Y');
                            @endphp
                            
                            @for($i = $currentYear - 5; $i <= $currentYear; $i++)
                                <option value="{{ $i }}" 
                                        {{ $yearFilter == $i ? 'selected' : '' }}>
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
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>No. Plat</th>
                                <th>Tanggal Pengecekan</th>
                                <th>Kondisi Ban</th>
                                <th>KM Saat Ini</th>
                                <th>Sisa KM Servis</th>
                                <th>Kondisi Rem</th>
                                <th>Kondisi Kabin</th>
                                <th>Kondisi Bak</th>
                                <th>Kondisi Lampu</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($checks as $check)
                            <tr>
                                <td>{{ $check->plate_number }}</td>
                                <td>{{ date('d/m/Y', strtotime($check->check_date)) }}</td>
                                <td>{!! getConditionLabel($check->tire_condition) !!}</td>
                                <td>{{ number_format($check->current_km) }} KM</td>
                                <td>{{ number_format($check->service_km_remaining) }} KM</td>
                                <td>{!! getConditionLabel($check->brake_condition) !!}</td>
                                <td>{!! getConditionLabel($check->cabin_condition) !!}</td>
                                <td>{!! getConditionLabel($check->cargo_area_condition) !!}</td>
                                <td>{!! getConditionLabel($check->lights_condition) !!}</td>
                                <td>{{ $check->description }}</td>
                                <td>
                                    <a href="{{ route('monthly-checks.edit', $check->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('monthly-checks.destroy', $check->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getConditionLabel($condition) {
    switch($condition) {
        case 'good':
            return '<span class="badge bg-success">Baik</span>';
        case 'fair':
            return '<span class="badge bg-warning">Kurang</span>';
        case 'needs_repair':
            return '<span class="badge bg-danger">Perlu Perbaikan</span>';
        default:
            return '';
    }
}
@endphp

@push('scripts')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>
@endpush
</body>
</html>