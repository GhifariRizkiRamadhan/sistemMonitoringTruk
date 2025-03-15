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
            <h2>Tambah Truk</h2>
            <a href="{{ route('trucks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
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
                <form method="POST" action="{{ route('trucks.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="plate_number" class="form-label required">Nomor Plat</label>
                        <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                               id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required>
                        @error('plate_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label required">Kategori</label>
                        <select class="form-select @error('category') is-invalid @enderror" 
                                id="category" name="category" required>
                            <option value="own" {{ old('category') == 'own' ? 'selected' : '' }}>Milik Sendiri</option>
                            <option value="tep" {{ old('category') == 'tep' ? 'selected' : '' }}>TEP</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="drivers" class="form-label required">Sopir (pisahkan dengan koma)</label>
                        <input type="text" class="form-control @error('drivers') is-invalid @enderror" 
                               id="drivers" name="drivers" value="{{ old('drivers') }}" required>
                        <small class="text-muted">Contoh: John Doe, Jane Doe</small>
                        @error('drivers')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="purchase_date" class="form-label required">Tanggal Beli/Masuk</label>
                        <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                               id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" required>
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
</body>
</html>