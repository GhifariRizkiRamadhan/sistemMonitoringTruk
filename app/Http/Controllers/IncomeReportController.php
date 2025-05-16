<?php

namespace App\Http\Controllers;

use App\Models\Shipments;
use App\Models\Trucks;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomeReportExport;
use App\Services\IncomePdfService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class IncomeReportController extends Controller
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

        // Query for shipments data
        $query = Shipments::select('shipments.*', 'trucks.plate_number', 'trucks.category', 'cargo_types.name as cargo_type')
            ->join('trucks', 'shipments.truck_id', '=', 'trucks.id')
            ->join('cargo_types', 'shipments.cargo_type_id', '=', 'cargo_types.id')
            ->whereMonth('shipments.unloading_date', $month_filter)
            ->whereYear('shipments.unloading_date', $year_filter);

        // Apply filters
        if (!empty($truck_filter)) {
            $query->where('shipments.truck_id', $truck_filter);
        }

        if (!empty($category_filter)) {
            $query->where('trucks.category', $category_filter);
        }

        $shipments = $query->orderBy('shipments.unloading_date', 'desc')->get();

        // Calculate chart data
        $chart_data = [
            'total_shipments' => $shipments->count(),
            'total_transport_cost' => 0,
            'total_results' => 0
        ];

        foreach ($shipments as $shipment) {
            $ongkos_angkut = $shipment->tonnage * $shipment->wage_per_ton;
            $hasil_truk = $ongkos_angkut - $shipment->travel_money;
            
            $chart_data['total_transport_cost'] += $ongkos_angkut;
            $chart_data['total_results'] += $hasil_truk;
        }

        // Daily income trend data
        $daily_income_query = Shipments::select(
            DB::raw("DATE_FORMAT(unloading_date, '%d/%m') as date"),
            DB::raw("SUM(tonnage * wage_per_ton - travel_money) as hasil")
        )
        ->join('trucks', 'shipments.truck_id', '=', 'trucks.id')
        ->whereMonth('unloading_date', $month_filter)
        ->whereYear('unloading_date', $year_filter);

        if (!empty($truck_filter)) {
            $daily_income_query->where('shipments.truck_id', $truck_filter);
        }

        if (!empty($category_filter)) {
            $daily_income_query->where('trucks.category', $category_filter);
        }

        $daily_income = $daily_income_query
            ->groupBy(DB::raw("DATE_FORMAT(unloading_date, '%d/%m')"))
            ->orderBy('unloading_date')
            ->get();

        // Calculate totals for display
        $total_ongkos = 0;
        $total_uang_jalan = 0;
        $total_hasil = 0;

        foreach ($shipments as $shipment) {
            $ongkos_angkut = $shipment->tonnage * $shipment->wage_per_ton;
            $hasil_truk = $ongkos_angkut - $shipment->travel_money;
            
            $total_ongkos += $ongkos_angkut;
            $total_uang_jalan += $shipment->travel_money;
            $total_hasil += $hasil_truk;
        }

        // Handle export requests
        if ($request->has('export')) {
            $exportData = $this->prepareExportData($shipments);
            $filename = $this->generateFilename($month_filter, $year_filter, $truck_info, $category_filter);

            if ($request->export === 'excel') {
                return Excel::download(new IncomeReportExport($exportData, $filename), $filename . '.xlsx');
            } elseif ($request->export === 'pdf') {
                $pdfService = new IncomePdfService();
                return $pdfService->generate($exportData, $filename, $chart_data, $month_filter, $year_filter, $truck_info, $total_ongkos, $total_uang_jalan, $total_hasil);
            }
        }

        return view('reports.income', compact(
            'shipments', 'trucks', 'truck_filter', 'category_filter',
            'month_filter', 'year_filter', 'truck_info', 'chart_data',
            'daily_income', 'total_ongkos', 'total_uang_jalan', 'total_hasil'
        ));
    }

    private function prepareExportData($shipments)
    {
        $data = [];
        foreach ($shipments as $shipment) {
            $ongkos_angkut = $shipment->tonnage * $shipment->wage_per_ton;
            $hasil_truk = $ongkos_angkut - $shipment->travel_money;
            
            $data[] = [
                'No. Plat' => $shipment->plate_number,
                'Muatan' => $shipment->cargo_type,
                'Tanggal Uang Jalan' => Carbon::parse($shipment->travel_money_date)->format('d/m/Y'),
                'Tanggal Muat' => Carbon::parse($shipment->loading_date)->format('d/m/Y'),
                'Tanggal Bongkar' => Carbon::parse($shipment->unloading_date)->format('d/m/Y'),
                'Tonase' => number_format($shipment->tonnage, 2),
                'Upah/Ton' => number_format($shipment->wage_per_ton, 0),
                'Ongkos Angkut' => number_format($ongkos_angkut, 0),
                'Uang Jalan' => number_format($shipment->travel_money, 0),
                'Hasil Truk' => number_format($hasil_truk, 0)
            ];
        }
        return $data;
    }

    private function generateFilename($month, $year, $truck_info, $category_filter)
    {
        $bulan = Carbon::createFromFormat('m', $month)->format('F');
        
        if ($truck_info) {
            return "Laporan_Pendapatan_{$truck_info->plate_number}_{$bulan}_{$year}";
        } elseif (!empty($category_filter)) {
            $cat_text = $category_filter == 'own' ? 'Milik_Sendiri' : 'TEP';
            return "Laporan_Pendapatan_Truk_{$cat_text}_{$bulan}_{$year}";
        } else {
            return "Laporan_Pendapatan_Semua_Truk_{$bulan}_{$year}";
        }
    }
}