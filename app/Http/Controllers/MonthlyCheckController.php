<?php

namespace App\Http\Controllers;

use App\Models\MonthlyChecks;
use App\Models\Truck;
use App\Models\Trucks;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonthlyCheckController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai filter
        $truckFilter = $request->input('truck_id', '');
        $monthFilter = $request->input('month', date('m'));
        $yearFilter = $request->input('year', date('Y'));

        // Ambil semua truk untuk dropdown filter
        $trucks = Trucks::orderBy('plate_number')->get();

        // Query pengecekan bulanan dengan filter
        $checks = MonthlyChecks::join('trucks', 'monthly_checks.truck_id', '=', 'trucks.id')
            ->select('monthly_checks.*', 'trucks.plate_number')
            ->where('monthly_checks.month', $monthFilter)
            ->where('monthly_checks.year', $yearFilter);

        if (!empty($truckFilter)) {
            $checks->where('monthly_checks.truck_id', $truckFilter);
        }

        $checks = $checks->orderBy('monthly_checks.check_date', 'DESC')->get();

        return view('monthly-checks.index', compact('trucks', 'checks', 'truckFilter', 'monthFilter', 'yearFilter'));
    }

    public function create()
    {
        $trucks = Trucks::orderBy('plate_number')->get();
        return view('monthly-checks.create', compact('trucks'));
    }

    public function store(Request $request)
    {
        // Validasi data permintaan
        $validatedData = $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'check_date' => 'required|date',
            'tire_condition' => 'required|in:good,fair,needs_repair',
            'current_km' => 'required|numeric',
            'service_km_remaining' => 'required|numeric',
            'brake_condition' => 'required|in:good,fair,needs_repair',
            'cabin_condition' => 'required|in:good,fair,needs_repair',
            'cargo_area_condition' => 'required|in:good,fair,needs_repair',
            'lights_condition' => 'required|in:good,fair,needs_repair',
            'description' => 'nullable|string',
        ]);

        // Tambahkan bulan dan tahun dari tanggal pengecekan
        $checkDate = Carbon::parse($validatedData['check_date']);
        $validatedData['month'] = $checkDate->format('m');
        $validatedData['year'] = $checkDate->format('Y');
        $validatedData['created_at'] = Carbon::now();

        // Buat catatan baru
        MonthlyChecks::create($validatedData);

        return redirect()->route('monthly-checks.index')
            ->with('message', 'Pengecekan berhasil ditambahkan')
            ->with('message_type', 'success');
    }

    public function edit($id)
    {
        $check = MonthlyChecks::findOrFail($id);
        $trucks = Trucks::orderBy('plate_number')->get();

        return view('monthly-checks.edit', compact('check', 'trucks'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data permintaan
        $validatedData = $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'check_date' => 'required|date',
            'tire_condition' => 'required|in:good,fair,needs_repair',
            'current_km' => 'required|numeric',
            'service_km_remaining' => 'required|numeric',
            'brake_condition' => 'required|in:good,fair,needs_repair',
            'cabin_condition' => 'required|in:good,fair,needs_repair',
            'cargo_area_condition' => 'required|in:good,fair,needs_repair',
            'lights_condition' => 'required|in:good,fair,needs_repair',
            'description' => 'nullable|string',
        ]);

        // Perbarui bulan dan tahun dari tanggal pengecekan
        $checkDate = Carbon::parse($validatedData['check_date']);
        $validatedData['month'] = $checkDate->format('m');
        $validatedData['year'] = $checkDate->format('Y');

        // Perbarui catatan
        $check = MonthlyChecks::findOrFail($id);
        $check->update($validatedData);

        return redirect()->route('monthly-checks.index')
            ->with('message', 'Pengecekan berhasil diperbarui')
            ->with('message_type', 'success');
    }

    public function destroy($id)
    {
        $check = MonthlyChecks::findOrFail($id);
        $check->delete();

        return redirect()->route('monthly-checks.index')
            ->with('message', 'Pengecekan berhasil dihapus')
            ->with('message_type', 'success');
    }
}