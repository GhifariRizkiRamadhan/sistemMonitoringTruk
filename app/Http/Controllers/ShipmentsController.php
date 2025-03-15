<?php

namespace App\Http\Controllers;

use App\Models\Shipments;
use App\Models\Trucks;
use App\Models\CargoTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentsController extends Controller
{
    public function index(Request $request)
    {
        // Filter berdasarkan truk jika ada
        $truck_filter = $request->input('truck_id', '');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        
        // Mengambil daftar truk untuk filter
        $trucks = Trucks::orderBy('plate_number')->get();
        
        // Query untuk mengambil catatan angkutan
        $query = DB::table('shipments as s')
            ->select(
                's.*', 
                't.plate_number', 
                'ct.name as cargo_type'
            )
            ->join('trucks as t', 's.truck_id', '=', 't.id')
            ->join('cargo_types as ct', 's.cargo_type_id', '=', 'ct.id');
        
        // Terapkan filter jika ada
        if (!empty($truck_filter)) {
            $query->where('s.truck_id', $truck_filter);
        }
        
        // Terapkan pencarian jika ada
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('t.plate_number', 'like', "%{$search}%")
                  ->orWhere('ct.name', 'like', "%{$search}%")
                  ->orWhere('s.travel_money_date', 'like', "%{$search}%")
                  ->orWhere('s.loading_date', 'like', "%{$search}%")
                  ->orWhere('s.unloading_date', 'like', "%{$search}%")
                  ->orWhere('s.tonnage', 'like', "%{$search}%")
                  ->orWhere('s.travel_money', 'like', "%{$search}%")
                  ->orWhere('s.wage_per_ton', 'like', "%{$search}%");
            });
        }
        
        // Urutkan berdasarkan tanggal terbaru
        $query->orderBy('s.travel_money_date', 'desc');
        
        $shipments = $query->paginate($perPage);
        
        // Mempertahankan parameter saat pindah halaman
        $shipments->appends([
            'truck_id' => $truck_filter,
            'per_page' => $perPage,
            'search' => $search
        ]);
        
        return view('shipments.index', compact('shipments', 'trucks', 'truck_filter', 'perPage', 'search'));
    }
    public function create()
    {
        $trucks = Trucks::orderBy('plate_number')->get();
        $cargoTypes = CargoTypes::orderBy('name')->get();
        
        return view('shipments.add', compact('trucks', 'cargoTypes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'cargo_type_id' => 'required|exists:cargo_types,id',
            'travel_money_date' => 'required|date',
            'loading_date' => 'required|date',
            'unloading_date' => 'required|date',
            'travel_money' => 'required|numeric|min:0',
            'tonnage' => 'required|numeric|min:0',
            'wage_per_ton' => 'required|numeric|min:0',
        ]);
        
        Shipments::create([
            'truck_id' => $request->truck_id,
            'cargo_type_id' => $request->cargo_type_id,
            'travel_money_date' => $request->travel_money_date,
            'loading_date' => $request->loading_date,
            'unloading_date' => $request->unloading_date,
            'travel_money' => $request->travel_money,
            'tonnage' => $request->tonnage,
            'wage_per_ton' => $request->wage_per_ton,
            'created_at' => now()
        ]);
        
        return redirect()->route('shipments.index')
            ->with('message', 'Catatan angkutan berhasil ditambahkan!')
            ->with('message_type', 'success');
    }
    
    public function edit($id)
    {
        $shipment = Shipments::findOrFail($id);
        $trucks = Trucks::orderBy('plate_number')->get();
        $cargoTypes = CargoTypes::orderBy('name')->get();
        
        return view('shipments.edit', compact('shipment', 'trucks', 'cargoTypes'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'cargo_type_id' => 'required|exists:cargo_types,id',
            'travel_money_date' => 'required|date',
            'loading_date' => 'required|date',
            'unloading_date' => 'required|date',
            'travel_money' => 'required|numeric|min:0',
            'tonnage' => 'required|numeric|min:0',
            'wage_per_ton' => 'required|numeric|min:0',
        ]);
        
        $shipment = Shipments::findOrFail($id);
        $shipment->update([
            'truck_id' => $request->truck_id,
            'cargo_type_id' => $request->cargo_type_id,
            'travel_money_date' => $request->travel_money_date,
            'loading_date' => $request->loading_date,
            'unloading_date' => $request->unloading_date,
            'travel_money' => $request->travel_money,
            'tonnage' => $request->tonnage,
            'wage_per_ton' => $request->wage_per_ton,
        ]);
        
        return redirect()->route('shipments.index')
            ->with('message', 'Catatan angkutan berhasil diperbarui!')
            ->with('message_type', 'success');
    }
    
    public function destroy($id)
    {
        $shipment = Shipments::findOrFail($id);
        $shipment->delete();
        
        return redirect()->route('shipments.index')
            ->with('message', 'Catatan angkutan berhasil dihapus!')
            ->with('message_type', 'success');
    }
}