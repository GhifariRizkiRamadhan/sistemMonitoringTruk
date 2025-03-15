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
            <h2>Catatan Angkutan</h2>
            <a href="{{ route('shipments.create') }}" class="btn btn-primary">
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
                <form id="filter-form" method="GET" action="{{ route('shipments.index') }}">
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
                <form id="per-page-form" method="GET" action="{{ route('shipments.index') }}" class="d-inline">
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
                <form id="search-form" method="GET" action="{{ route('shipments.index') }}">
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
                                <th class="text-sm">No. Plat</th>
                                <th class="text-sm">Muatan</th>
                                <th class="text-sm">Tanggal Uang Jalan</th>
                                <th class="text-sm">Tanggal Muat</th>
                                <th class="text-sm">Tanggal Bongkar</th>
                                <th class="text-sm">Tonase</th>
                                <th class="text-sm">Upah/Ton</th>
                                <th class="text-sm">Uang Jalan</th>
                                <th class="text-sm">Ongkos Angkut</th>
                                <th class="text-sm">Hasil Truk</th>
                                <th class="text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($shipments->isEmpty())
                                <tr>
                                    <td colspan="11" class="text-center">Tidak ada data</td>
                                </tr>
                            @else
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
                                    <td>Rp {{ number_format($shipment->travel_money, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($ongkos_angkut, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($hasil_truk, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('shipments.edit', $shipment->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" style="display:inline">
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
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $shipments->firstItem() ?? 0 }} sampai {{ $shipments->lastItem() ?? 0 }} dari {{ $shipments->total() }} entri
            </div>
            <div>
                {{ $shipments->links('pagination::bootstrap-4') }}
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

        // Auto submit form saat perPage diubah (sudah ditangani oleh atribut onchange)
        
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