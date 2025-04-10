@extends('layouts.app')

@section('content')
<div class="content" style="width: 100%; margin-top: 5.5%;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Pengecekan Bulanan</h2>
            <a href="{{ route('monthly-checks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form action="{{ route('monthly-checks.update', $check->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="truck_id" class="form-label">Truk <span class="text-danger">*</span></label>
                            <select class="form-select @error('truck_id') is-invalid @enderror" id="truck_id" name="truck_id" required>
                                <option value="">-- Pilih Truk --</option>
                                @foreach($trucks as $truck)
                                    <option value="{{ $truck->id }}" {{ old('truck_id', $check->truck_id) == $truck->id ? 'selected' : '' }}>
                                        {{ $truck->plate_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('truck_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="check_date" class="form-label">Tanggal Pengecekan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('check_date') is-invalid @enderror" 
                                   id="check_date" name="check_date" value="{{ old('check_date', date('Y-m-d', strtotime($check->check_date))) }}" required>
                            @error('check_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="current_km" class="form-label">KM Saat Ini <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('current_km') is-invalid @enderror" 
                                   id="current_km" name="current_km" value="{{ old('current_km', $check->current_km) }}" required>
                            @error('current_km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="service_km_remaining" class="form-label">Sisa KM Servis <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('service_km_remaining') is-invalid @enderror" 
                                   id="service_km_remaining" name="service_km_remaining" 
                                   value="{{ old('service_km_remaining', $check->service_km_remaining) }}" required>
                            @error('service_km_remaining')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="tire_condition" class="form-label">Kondisi Ban <span class="text-danger">*</span></label>
                            <select class="form-select @error('tire_condition') is-invalid @enderror" id="tire_condition" name="tire_condition" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="good" {{ old('tire_condition', $check->tire_condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                <option value="fair" {{ old('tire_condition', $check->tire_condition) == 'fair' ? 'selected' : '' }}>Kurang</option>
                                <option value="needs_repair" {{ old('tire_condition', $check->tire_condition) == 'needs_repair' ? 'selected' : '' }}>
                                    Perlu Perbaikan
                                </option>
                            </select>
                            @error('tire_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="brake_condition" class="form-label">Kondisi Rem <span class="text-danger">*</span></label>
                            <select class="form-select @error('brake_condition') is-invalid @enderror" id="brake_condition" name="brake_condition" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="good" {{ old('brake_condition', $check->brake_condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                <option value="fair" {{ old('brake_condition', $check->brake_condition) == 'fair' ? 'selected' : '' }}>Kurang</option>
                                <option value="needs_repair" {{ old('brake_condition', $check->brake_condition) == 'needs_repair' ? 'selected' : '' }}>
                                    Perlu Perbaikan
                                </option>
                            </select>
                            @error('brake_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="cabin_condition" class="form-label">Kondisi Kabin <span class="text-danger">*</span></label>
                            <select class="form-select @error('cabin_condition') is-invalid @enderror" id="cabin_condition" name="cabin_condition" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="good" {{ old('cabin_condition', $check->cabin_condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                <option value="fair" {{ old('cabin_condition', $check->cabin_condition) == 'fair' ? 'selected' : '' }}>Kurang</option>
                                <option value="needs_repair" {{ old('cabin_condition', $check->cabin_condition) == 'needs_repair' ? 'selected' : '' }}>
                                    Perlu Perbaikan
                                </option>
                            </select>
                            @error('cabin_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="cargo_area_condition" class="form-label">Kondisi Bak <span class="text-danger">*</span></label>
                            <select class="form-select @error('cargo_area_condition') is-invalid @enderror" 
                                    id="cargo_area_condition" name="cargo_area_condition" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="good" {{ old('cargo_area_condition', $check->cargo_area_condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                <option value="fair" {{ old('cargo_area_condition', $check->cargo_area_condition) == 'fair' ? 'selected' : '' }}>Kurang</option>
                                <option value="needs_repair" {{ old('cargo_area_condition', $check->cargo_area_condition) == 'needs_repair' ? 'selected' : '' }}>
                                    Perlu Perbaikan
                                </option>
                            </select>
                            @error('cargo_area_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="lights_condition" class="form-label">Kondisi Lampu <span class="text-danger">*</span></label>
                            <select class="form-select @error('lights_condition') is-invalid @enderror" id="lights_condition" name="lights_condition" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="good" {{ old('lights_condition', $check->lights_condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                <option value="fair" {{ old('lights_condition', $check->lights_condition) == 'fair' ? 'selected' : '' }}>Kurang</option>
                                <option value="needs_repair" {{ old('lights_condition', $check->lights_condition) == 'needs_repair' ? 'selected' : '' }}>
                                    Perlu Perbaikan
                                </option>
                            </select>
                            @error('lights_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="engine_condition" class="form-label">Kondisi Mesin <span class="text-danger">*</span></label>
                            <select class="form-select @error('engine_condition') is-invalid @enderror" id="engine_condition" name="engine_condition" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="good" {{ old('engine_condition', $check->engine_condition) == 'good' ? 'selected' : '' }}>Baik</option>
                                <option value="fair" {{ old('engine_condition', $check->engine_condition) == 'fair' ? 'selected' : '' }}>Kurang</option>
                                <option value="needs_repair" {{ old('engine_condition', $check->engine_condition) == 'needs_repair' ? 'selected' : '' }}>
                                    Perlu Perbaikan
                                </option>
                            </select>
                            @error('engine_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4">{{ old('notes', $check->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input @error('is_ok') is-invalid @enderror" type="radio" 
                                   name="is_ok" id="is_ok_yes" value="1" {{ old('is_ok', $check->is_ok) == 1 ? 'checked' : '' }} required>
                            <label class="form-check-label" for="is_ok_yes">
                                <span class="text-success">Layak Operasi</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input @error('is_ok') is-invalid @enderror" type="radio" 
                                   name="is_ok" id="is_ok_no" value="0" {{ old('is_ok', $check->is_ok) == 0 ? 'checked' : '' }} required>
                            <label class="form-check-label" for="is_ok_no">
                                <span class="text-danger">Tidak Layak Operasi</span>
                            </label>
                            @error('is_ok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-calculate service_km_remaining based on truck selection
        $('#truck_id').change(function() {
            const truckId = $(this).val();
            if (truckId) {
                // Optional: You could fetch the last service data via AJAX
                // and pre-calculate the remaining KM
            }
        });
        
        // Form validation
        $('#tire_condition, #brake_condition, #cabin_condition, #cargo_area_condition, #lights_condition, #engine_condition')
            .change(function() {
                const value = $(this).val();
                if (value === 'needs_repair') {
                    $('#is_ok_no').prop('checked', true);
                }
            });
    });
</script>
@endsection