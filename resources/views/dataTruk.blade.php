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
                <h2>Data Truk</h2>
                <a href="{{ route('trucks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Truk
                </a>
            </div>
            
            @if (session('message'))
                <div class="alert alert-{{ session('message_type') }} alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <form id="per-page-form" method="GET" action="{{ route('trucks.index') }}" class="d-inline-block">
                                    <!-- Jika ada pencarian, simpan nilainya -->
                                    @if(isset($search) && !empty($search))
                                    <input type="hidden" name="search" value="{{ $search }}">
                                    @endif
                                    Tampilkan 
                                    <select id="per-page-select" name="per_page" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                                        <option value="10" {{ isset($perPage) && $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ isset($perPage) && $perPage == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ isset($perPage) && $perPage == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ isset($perPage) && $perPage == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    entri
                                </form>
                            </div>
                            <div>
                                <form id="search-form" method="GET" action="{{ route('trucks.index') }}">
                                    <!-- Jika ada perPage, simpan nilainya -->
                                    @if(isset($perPage))
                                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                                    @endif
                                    <div class="input-group">
                                        <span class="input-group-text">Cari:</span>
                                        <input type="text" name="search" id="search-input" class="form-control" value="{{ $search ?? '' }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No. Plat</th>
                                    <th>Kategori</th>
                                    <th>Sopir</th>
                                    <th>Tanggal Beli/Masuk</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trucks as $truck)
                                <tr>
                                    <td>{{ $truck->plate_number }}</td>
                                    <td>
                                        {{ $truck->category == 'own' ? 'Milik Sendiri' : 'TEP' }}
                                    </td>
                                    <td>{{ $truck->drivers }}</td>
                                    <td>{{ date('d/m/Y', strtotime($truck->purchase_date)) }}</td>
                                    <td>{{ $truck->description }}</td>
                                    <td>
                                        <a href="{{ route('trucks.show', $truck->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('trucks.edit', $truck->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('trucks.destroy', $truck->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $trucks->firstItem() ?? 0 }} sampai {{ $trucks->lastItem() ?? 0 }} dari {{ $trucks->total() }} entri
                            </div>
                            <div>
                                {{ $trucks->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    
    @section('scripts')
    <script>
        $(document).ready(function() {
            // Submit form saat mencari
            $('#search-input').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    $('#search-form').submit();
                }
            });
        });
    </script>
    @endsection
</body>
</html>