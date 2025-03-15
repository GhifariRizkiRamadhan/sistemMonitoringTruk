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
            <h2>Catatan Operasional/Perawatan</h2>
            <a href="{{ route('operational-expenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Catatan
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
                <form id="filter-form" method="GET" action="{{ route('operational-expenses.index') }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <div class="mb-3">
                        <label for="truck_id" class="form-label">Filter berdasarkan Truk</label>
                        <div class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <select class="form-select" id="truck_id" name="truck_id">
                                    <option value="">Semua Truk</option>
                                    @foreach($trucks as $truck)
                                        <option value="{{ $truck->id }}" {{ $truck_filter == $truck->id ? 'selected' : '' }}>
                                            {{ $truck->plate_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 150px;">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div>
                <form id="per-page-form" method="GET" action="{{ route('operational-expenses.index') }}" class="d-inline">
                    <input type="hidden" name="truck_id" value="{{ $truck_filter }}">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <label class="me-2">Tampilkan</label>
                    <select id="per-page-select" name="per_page" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="ms-1">entri</span>
                </form>
            </div>
            <div>
                <form id="search-form" method="GET" action="{{ route('operational-expenses.index') }}">
                    <input type="hidden" name="truck_id" value="{{ $truck_filter }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="input-group">
                        <span class="input-group-text">Cari:</span>
                        <input type="text" name="search" id="search-input" class="form-control form-control-sm" value="{{ $search }}">
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No. Plat</th>
                                <th>Item</th>
                                <th>Keterangan</th>
                                <th>Tanggal</th>
                                <th>Quantity</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($expenses->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                            @else
                                @foreach($expenses as $expense)
                                @php
                                    $total = $expense->quantity * $expense->price;
                                @endphp
                                <tr>
                                    <td>{{ $expense->plate_number }}</td>
                                    <td>{{ $expense->operational_type }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ date('d/m/Y', strtotime($expense->date)) }}</td>
                                    <td>{{ $expense->quantity }}</td>
                                    <td>Rp {{ number_format($expense->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('operational-expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('operational-expenses.destroy', $expense->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">Total Pengeluaran:</th>
                                <th colspan="2">
                                    Rp {{ number_format($total_expenses, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $expenses->firstItem() ?? 0 }} sampai {{ $expenses->lastItem() ?? 0 }} dari {{ $expenses->total() }} entri
            </div>
            <div>
                {{ $expenses->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Submit form pencarian saat tekan Enter
        $('#search-input').on('keyup', function(e) {
            if (e.key === 'Enter') {
                $('#search-form').submit();
            }
        });
        
        // Pastikan parameter di URL terbawa saat pindah halaman
        $('.pagination a').each(function() {
            var url = new URL($(this).attr('href'), window.location.origin);
            url.searchParams.set('truck_id', '{{ $truck_filter }}');
            url.searchParams.set('per_page', '{{ $perPage }}');
            url.searchParams.set('search', '{{ $search }}');
            $(this).attr('href', url.href);
        });
    });
</script>
@endsection
</body>
</html>