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
            <h2>Edit Catatan Angkutan</h2>
            <a href="{{ route('shipments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('shipments.update', $shipment->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="truck_id" class="form-label required">Truk</label>
                        <select class="form-select @error('truck_id') is-invalid @enderror" 
                                id="truck_id" name="truck_id" required>
                            <option value="">Pilih Truk</option>
                            @foreach($trucks as $truck)
                                <option value="{{ $truck->id }}" {{ old('truck_id', $shipment->truck_id) == $truck->id ? 'selected' : '' }}>
                                    {{ $truck->plate_number }} ({{ $truck->category == 'own' ? 'Milik Sendiri' : 'TEP' }})
                                </option>
                            @endforeach
                        </select>
                        @error('truck_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="cargo_type_id" class="form-label required">Jenis Muatan</label>
                        <select class="form-select @error('cargo_type_id') is-invalid @enderror" 
                                id="cargo_type_id" name="cargo_type_id" required>
                            <option value="">Pilih Jenis Muatan</option>
                            @foreach($cargoTypes as $cargoType)
                                <option value="{{ $cargoType->id }}" {{ old('cargo_type_id', $shipment->cargo_type_id) == $cargoType->id ? 'selected' : '' }}>
                                    {{ $cargoType->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cargo_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="travel_money_date" class="form-label required">Tanggal Uang Jalan</label>
                        <input type="date" class="form-control @error('travel_money_date') is-invalid @enderror" 
                               id="travel_money_date" name="travel_money_date" 
                               value="{{ old('travel_money_date', $shipment->travel_money_date) }}" required>
                        @error('travel_money_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="loading_date" class="form-label required">Tanggal Muat</label>
                        <input type="date" class="form-control @error('loading_date') is-invalid @enderror" 
                               id="loading_date" name="loading_date" 
                               value="{{ old('loading_date', $shipment->loading_date) }}" required>
                        @error('loading_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="unloading_date" class="form-label required">Tanggal Bongkar</label>
                        <input type="date" class="form-control @error('unloading_date') is-invalid @enderror" 
                               id="unloading_date" name="unloading_date" 
                               value="{{ old('unloading_date', $shipment->unloading_date) }}" required>
                        @error('unloading_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="travel_money" class="form-label required">Uang Jalan</label>
                        <input type="number" class="form-control @error('travel_money') is-invalid @enderror" 
                               id="travel_money" name="travel_money" 
                               value="{{ old('travel_money', $shipment->travel_money) }}" required>
                        @error('travel_money')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tonnage" class="form-label required">Tonase</label>
                        <input type="number" step="0.01" class="form-control @error('tonnage') is-invalid @enderror" 
                               id="tonnage" name="tonnage" 
                               value="{{ old('tonnage', $shipment->tonnage) }}" required>
                        @error('tonnage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="wage_per_ton" class="form-label required">Upah per Ton</label>
                        <input type="number" class="form-control @error('wage_per_ton') is-invalid @enderror" 
                               id="wage_per_ton" name="wage_per_ton" 
                               value="{{ old('wage_per_ton', $shipment->wage_per_ton) }}" required>
                        @error('wage_per_ton')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
</body>
</html>