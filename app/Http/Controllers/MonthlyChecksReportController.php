<?php
namespace App\Http\Controllers;

use App\Models\MonthlyChecks;
use App\Models\Trucks;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyChecksExport;
use App\Services\MonthlyChecksPdfService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyChecksReportController extends Controller
{
    public function index(Request $request)
    {
        // Filter parameters
        $truck_filter = $request->input('truck_id', '');
        $category_filter = $request->input('category', '');
        $month_filter = $request->input('month', date('m'));
        $year_filter = $request->input('year', date('Y'));

        // Get list of trucks
        $trucks = Trucks::orderBy('plate_number')->get();

        // Get truck info if filtered
        $truck_info = null;
        if (!empty($truck_filter)) {
            $truck_info = Trucks::find($truck_filter);
        }

        // Query for monthly checks data
        $query = MonthlyChecks::select('monthly_checks.*', 'trucks.plate_number', 'trucks.category')
            ->join('trucks', 'monthly_checks.truck_id', '=', 'trucks.id')
            ->where('monthly_checks.month', $month_filter)
            ->where('monthly_checks.year', $year_filter);

        // Apply filters
        if (!empty($truck_filter)) {
            $query->where('monthly_checks.truck_id', $truck_filter);
        }
        if (!empty($category_filter)) {
            $query->where('trucks.category', $category_filter);
        }

        $checks = $query->orderBy('monthly_checks.check_date', 'desc')->get();

        // Trucks with KM > 50000
        $high_km_query = MonthlyChecks::select('trucks.plate_number', 'monthly_checks.current_km')
            ->join('trucks', 'monthly_checks.truck_id', '=', 'trucks.id')
            ->where('monthly_checks.month', $month_filter)
            ->where('monthly_checks.year', $year_filter)
            ->where('monthly_checks.current_km', '>', 50000);

        if (!empty($truck_filter)) {
            $high_km_query->where('monthly_checks.truck_id', $truck_filter);
        }
        if (!empty($category_filter)) {
            $high_km_query->where('trucks.category', $category_filter);
        }

        $high_km_trucks = $high_km_query->orderBy('monthly_checks.current_km', 'desc')->get();

        // Trucks with service_km_remaining < 1000
        $low_service_km_query = MonthlyChecks::select('trucks.plate_number', 'monthly_checks.service_km_remaining')
            ->join('trucks', 'monthly_checks.truck_id', '=', 'trucks.id')
            ->where('monthly_checks.month', $month_filter)
            ->where('monthly_checks.year', $year_filter)
            ->where('monthly_checks.service_km_remaining', '<', 1000);

        if (!empty($truck_filter)) {
            $low_service_km_query->where('monthly_checks.truck_id', $truck_filter);
        }
        if (!empty($category_filter)) {
            $low_service_km_query->where('trucks.category', $category_filter);
        }

        $low_service_km_trucks = $low_service_km_query->orderBy('monthly_checks.service_km_remaining')->get();

        // Get condition statistics
        $condition_stats_query = MonthlyChecks::select(
                DB::raw('SUM(CASE WHEN tire_condition = "good" THEN 1 ELSE 0 END) as good_tire'),
                DB::raw('SUM(CASE WHEN tire_condition = "fair" THEN 1 ELSE 0 END) as fair_tire'),
                DB::raw('SUM(CASE WHEN tire_condition = "needs_repair" THEN 1 ELSE 0 END) as bad_tire'),
                
                DB::raw('SUM(CASE WHEN brake_condition = "good" THEN 1 ELSE 0 END) as good_brake'),
                DB::raw('SUM(CASE WHEN brake_condition = "fair" THEN 1 ELSE 0 END) as fair_brake'),
                DB::raw('SUM(CASE WHEN brake_condition = "needs_repair" THEN 1 ELSE 0 END) as bad_brake'),
                
                DB::raw('SUM(CASE WHEN cabin_condition = "good" THEN 1 ELSE 0 END) as good_cabin'),
                DB::raw('SUM(CASE WHEN cabin_condition = "fair" THEN 1 ELSE 0 END) as fair_cabin'),
                DB::raw('SUM(CASE WHEN cabin_condition = "needs_repair" THEN 1 ELSE 0 END) as bad_cabin'),
                
                DB::raw('SUM(CASE WHEN cargo_area_condition = "good" THEN 1 ELSE 0 END) as good_cargo'),
                DB::raw('SUM(CASE WHEN cargo_area_condition = "fair" THEN 1 ELSE 0 END) as fair_cargo'),
                DB::raw('SUM(CASE WHEN cargo_area_condition = "needs_repair" THEN 1 ELSE 0 END) as bad_cargo'),
                
                DB::raw('SUM(CASE WHEN lights_condition = "good" THEN 1 ELSE 0 END) as good_lights'),
                DB::raw('SUM(CASE WHEN lights_condition = "fair" THEN 1 ELSE 0 END) as fair_lights'),
                DB::raw('SUM(CASE WHEN lights_condition = "needs_repair" THEN 1 ELSE 0 END) as bad_lights')
            )
            ->join('trucks', 'monthly_checks.truck_id', '=', 'trucks.id')
            ->where('monthly_checks.month', $month_filter)
            ->where('monthly_checks.year', $year_filter);

        if (!empty($truck_filter)) {
            $condition_stats_query->where('monthly_checks.truck_id', $truck_filter);
        }
        if (!empty($category_filter)) {
            $condition_stats_query->where('trucks.category', $category_filter);
        }

        $condition_stats = $condition_stats_query->first();

        // Handle export requests
        if ($request->has('export')) {
            $exportData = $this->prepareExportData($checks);
            $filename = $this->generateFilename($month_filter, $year_filter, $truck_info, $category_filter);

            if ($request->export === 'excel') {
                return Excel::download(
                    new MonthlyChecksExport($exportData, $filename),
                    $filename . '.xlsx'
                );
            } elseif ($request->export === 'pdf') {
                $pdfService = new MonthlyChecksPdfService();
                return $pdfService->generate(
                    $exportData, 
                    $filename, 
                    $condition_stats, 
                    $high_km_trucks, 
                    $low_service_km_trucks, 
                    $month_filter, 
                    $year_filter, 
                    $truck_info
                );
            }
        }

        return view('reports.monthly_checks', compact(
            'checks',
            'trucks',
            'truck_filter',
            'category_filter',
            'month_filter',
            'year_filter',
            'truck_info',
            'high_km_trucks',
            'low_service_km_trucks',
            'condition_stats'
        ));
    }

    private function prepareExportData($checks)
    {
        $data = [];
        
        foreach ($checks as $check) {
            $data[] = [
                'No. Plat' => $check->plate_number,
                'Tanggal Pengecekan' => Carbon::parse($check->check_date)->format('d/m/Y'),
                'Kondisi Ban' => $this->getConditionLabel($check->tire_condition, false),
                'KM Saat Ini' => number_format($check->current_km),
                'Sisa KM Servis' => number_format($check->service_km_remaining),
                'Kondisi Rem' => $this->getConditionLabel($check->brake_condition, false),
                'Kondisi Kabin' => $this->getConditionLabel($check->cabin_condition, false),
                'Kondisi Bak' => $this->getConditionLabel($check->cargo_area_condition, false),
                'Kondisi Lampu' => $this->getConditionLabel($check->lights_condition, false),
                'Keterangan' => $check->description
            ];
        }
        
        return $data;
    }

    private function generateFilename($month, $year, $truck_info, $category_filter)
    {
        $bulan = Carbon::createFromFormat('m', $month)->format('F');
        
        if ($truck_info) {
            return "Laporan_Pengecekan_{$truck_info->plate_number}_{$bulan}_{$year}";
        } elseif (!empty($category_filter)) {
            $cat_text = $category_filter == 'own' ? 'Milik_Sendiri' : 'TEP';
            return "Laporan_Pengecekan_Truk_{$cat_text}_{$bulan}_{$year}";
        } else {
            return "Laporan_Pengecekan_Semua_Truk_{$bulan}_{$year}";
        }
    }

    public function getConditionLabel($condition, $useBadge = true)
    {
        $labels = [
            'good' => ['text' => 'Baik', 'class' => 'bg-success'],
            'fair' => ['text' => 'Kurang', 'class' => 'bg-warning'],
            'needs_repair' => ['text' => 'Perlu Perbaikan', 'class' => 'bg-danger']
        ];
        
        if (!isset($labels[$condition])) {
            return '';
        }
        
        if ($useBadge) {
            return '<span class="badge ' . $labels[$condition]['class'] . '">' . $labels[$condition]['text'] . '</span>';
        }
        
        return $labels[$condition]['text'];
    }
}