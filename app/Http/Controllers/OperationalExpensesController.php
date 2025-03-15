<?php

namespace App\Http\Controllers;

use App\Models\OperationalExpenses;
use App\Models\OperationalTypes;
use App\Models\Trucks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationalExpensesController extends Controller
{
    public function index(Request $request)
    {
        // Filter berdasarkan truk jika ada
        $truck_filter = $request->input('truck_id', '');
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        
        // Mengambil daftar truk untuk filter
        $trucks = Trucks::orderBy('plate_number')->get();
        
        // Query untuk mengambil catatan operasional
        $query = DB::table('operational_expenses as oe')
            ->select(
                'oe.*', 
                't.plate_number', 
                'ot.name as operational_type'
            )
            ->join('trucks as t', 'oe.truck_id', '=', 't.id')
            ->join('operational_types as ot', 'oe.operational_type_id', '=', 'ot.id');
        
        // Terapkan filter jika ada
        if (!empty($truck_filter)) {
            $query->where('oe.truck_id', $truck_filter);
        }
        
        // Terapkan pencarian jika ada
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('t.plate_number', 'like', "%{$search}%")
                  ->orWhere('ot.name', 'like', "%{$search}%")
                  ->orWhere('oe.description', 'like', "%{$search}%")
                  ->orWhere('oe.date', 'like', "%{$search}%");
            });
        }
        
        // Urutkan berdasarkan tanggal terbaru
        $query->orderBy('oe.date', 'desc');
        
        $expenses = $query->paginate($perPage);
        
        // Hitung total pengeluaran
        $total_expenses = 0;
        foreach ($expenses as $expense) {
            $total_expenses += $expense->quantity * $expense->price;
        }
        
        // Mempertahankan parameter saat pindah halaman
        $expenses->appends([
            'truck_id' => $truck_filter,
            'per_page' => $perPage,
            'search' => $search
        ]);
        
        return view('operationalExpenses.index', compact(
            'expenses', 
            'trucks', 
            'truck_filter', 
            'perPage', 
            'search', 
            'total_expenses'
        ));
    }
    
    public function create()
    {
        $trucks = Trucks::orderBy('plate_number')->get();
        $operationalTypes = OperationalTypes::orderBy('name')->get();
        
        return view('operationalExpenses.add', compact('trucks', 'operationalTypes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'operational_type_id' => 'required|exists:operational_types,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ]);
        
        OperationalExpenses::create([
            'truck_id' => $request->truck_id,
            'operational_type_id' => $request->operational_type_id,
            'description' => $request->description,
            'date' => $request->date,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'created_at' => now()
        ]);
        
        return redirect()->route('operational-expenses.index')
            ->with('message', 'Catatan operasional berhasil ditambahkan!')
            ->with('message_type', 'success');
    }
    
    public function edit($id)
    {
        $expense = OperationalExpenses::findOrFail($id);
        $trucks = Trucks::orderBy('plate_number')->get();
        $operationalTypes = OperationalTypes::orderBy('name')->get();
        
        return view('operationalExpenses.edit', compact('expense', 'trucks', 'operationalTypes'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'operational_type_id' => 'required|exists:operational_types,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ]);
        
        $expense = OperationalExpenses::findOrFail($id);
        $expense->update([
            'truck_id' => $request->truck_id,
            'operational_type_id' => $request->operational_type_id,
            'description' => $request->description,
            'date' => $request->date,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);
        
        return redirect()->route('operational-expenses.index')
            ->with('message', 'Catatan operasional berhasil diperbarui!')
            ->with('message_type', 'success');
    }
    
    public function destroy($id)
    {
        $expense = OperationalExpenses::findOrFail($id);
        $expense->delete();
        
        return redirect()->route('operational-expenses.index')
            ->with('message', 'Catatan operasional berhasil dihapus!')
            ->with('message_type', 'success');
    }
}