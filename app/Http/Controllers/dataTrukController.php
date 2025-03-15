<?php

namespace App\Http\Controllers;

use App\Models\Trucks;
use App\Models\Drivers;
use App\Models\TruckDrivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataTrukController extends Controller
{
    public function index(Request $request)
    {
        // Perbaikan 1: Pastikan parameter $request digunakan dengan benar
        $perPage = $request->input('per_page', 10); // Default 10 item per halaman
        $search = $request->input('search', '');
        
        $query = DB::table('trucks as t')
            ->select(
                't.id', 
                't.plate_number', 
                't.category', 
                't.purchase_date', 
                't.description', 
                't.created_at',
                DB::raw('GROUP_CONCAT(d.name SEPARATOR ", ") as drivers')
            )
            ->leftJoin('truck_drivers as td', 't.id', '=', 'td.truck_id')
            ->leftJoin('drivers as d', 'td.driver_id', '=', 'd.id')
            ->groupBy('t.id', 't.plate_number', 't.category', 't.purchase_date', 't.description', 't.created_at')
            ->orderBy('t.id', 'desc');
        
        // Tambahkan pencarian jika ada
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('t.plate_number', 'like', "%{$search}%")
                  ->orWhere('t.description', 'like', "%{$search}%")
                  ->orWhere('d.name', 'like', "%{$search}%");
            });
        }
        
        $trucks = $query->paginate($perPage);
        
        // Mempertahankan parameter saat pindah halaman
        $trucks->appends([
            'per_page' => $perPage,
            'search' => $search
        ]);
        
        return view('dataTruk', compact('trucks', 'perPage', 'search'));
    }

    public function create()
    {
        // Kita tidak perlu mengirim daftar sopir karena akan input manual
        return view('tambahTruk');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'plate_number' => 'required|unique:trucks,plate_number',
            'category' => 'required|in:own,tep',
            'purchase_date' => 'required|date',
            'description' => 'nullable',
            'drivers' => 'required|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Buat truk baru
            $truck = Trucks::create([
                'plate_number' => $request->plate_number,
                'category' => $request->category,
                'purchase_date' => $request->purchase_date,
                'description' => $request->description,
                'created_at' => now()
            ]);

            // Proses data sopir
            $driversArray = explode(',', $request->drivers);
            
            foreach ($driversArray as $driverName) {
                $driverName = trim($driverName);
                if (empty($driverName)) continue;
                
                // Cek apakah sopir sudah ada
                $driver = Drivers::where('name', $driverName)->first();
                
                if (!$driver) {
                    // Buat sopir baru jika belum ada
                    $driver = Drivers::create([
                        'name' => $driverName,
                        'created_at' => now()
                    ]);
                }
                
                // Hubungkan sopir dengan truk
                TruckDrivers::create([
                    'truck_id' => $truck->id,
                    'driver_id' => $driver->id,
                    'created_at' => now()
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('trucks.index')
                ->with('message', 'Truk berhasil ditambahkan!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('message', 'Terjadi kesalahan: ' . $e->getMessage())
                ->with('message_type', 'danger')
                ->withInput();
        }
    }

    public function edit($id)
    {
        $truck = Trucks::findOrFail($id);
        $drivers = Drivers::all();
        $selectedDrivers = TruckDrivers::where('truck_id', $id)
            ->pluck('driver_id')
            ->toArray();
            
        return view('dataTruk.edit', compact('truck', 'drivers', 'selectedDrivers'));
    }

    public function update(Request $request, $id)
    {
        $truck = Trucks::findOrFail($id);

        $request->validate([
            'plate_number' => 'required|unique:trucks,plate_number,'.$id,
            'category' => 'required|in:own,tep',
            'purchase_date' => 'required|date',
            'description' => 'nullable',
            'driver_ids' => 'nullable|array',
        ]);

        $truck->update([
            'plate_number' => $request->plate_number,
            'category' => $request->category,
            'purchase_date' => $request->purchase_date,
            'description' => $request->description,
        ]);

        // Update relasi sopir - hapus yang lama dulu
        TruckDrivers::where('truck_id', $id)->delete();
        
        // Tambahkan relasi yang baru
        if ($request->has('driver_ids') && is_array($request->driver_ids)) {
            foreach ($request->driver_ids as $driverId) {
                TruckDrivers::create([
                    'truck_id' => $id,
                    'driver_id' => $driverId,
                    'created_at' => now()
                ]);
            }
        }

        return redirect()->route('trucks.index')
            ->with('message', 'Data truk berhasil diperbarui!')
            ->with('message_type', 'success');
    }

    public function destroy($id)
    {
        // Periksa apakah truk terkait dengan data lain
        $hasShipments = DB::table('shipments')->where('truck_id', $id)->exists();
        $hasMonthlyChecks = DB::table('monthly_checks')->where('truck_id', $id)->exists();
        $hasExpenses = DB::table('operational_expenses')->where('truck_id', $id)->exists();

        if ($hasShipments || $hasMonthlyChecks || $hasExpenses) {
            return redirect()->route('trucks.index')
                ->with('message', 'Tidak dapat menghapus truk karena masih terkait dengan data lain!')
                ->with('message_type', 'danger');
        }

        // Hapus relasi truk dengan sopir
        TruckDrivers::where('truck_id', $id)->delete();
        
        // Hapus truk
        $truck = Trucks::findOrFail($id);
        $truck->delete();

        return redirect()->route('trucks.index')
            ->with('message', 'Truk berhasil dihapus!')
            ->with('message_type', 'success');
    }

    // Method tambahan untuk menangani relasi dengan data lain
    public function show($id)
    {
        $truck = Trucks::findOrFail($id);
        
        // Mengambil data sopir untuk truk ini
        $drivers = DB::table('drivers as d')
            ->join('truck_drivers as td', 'd.id', '=', 'td.driver_id')
            ->where('td.truck_id', $id)
            ->select('d.*')
            ->get();
            
        // Mengambil data pengiriman untuk truk ini
        $shipments = DB::table('shipments as s')
            ->join('cargo_types as ct', 's.cargo_type_id', '=', 'ct.id')
            ->where('s.truck_id', $id)
            ->select('s.*', 'ct.name as cargo_name')
            ->orderBy('s.loading_date', 'desc')
            ->get();
            
        // Mengambil data pemeriksaan bulanan
        $monthlyChecks = DB::table('monthly_checks')
            ->where('truck_id', $id)
            ->orderBy('check_date', 'desc')
            ->get();
            
        // Mengambil data pengeluaran operasional
        $expenses = DB::table('operational_expenses as oe')
            ->join('operational_types as ot', 'oe.operational_type_id', '=', 'ot.id')
            ->where('oe.truck_id', $id)
            ->select('oe.*', 'ot.name as expense_type')
            ->orderBy('oe.date', 'desc')
            ->get();
            
        return view('dataTruk.show', compact('truck', 'drivers', 'shipments', 'monthlyChecks', 'expenses'));
    }
}