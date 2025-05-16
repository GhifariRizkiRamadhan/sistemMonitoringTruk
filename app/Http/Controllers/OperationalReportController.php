<?php
namespace App\Http\Controllers;

use App\Models\OperationalExpenses;
use App\Models\OperationalTypes;
use App\Models\Trucks;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperationalReportExport;
use App\Services\OperationalPdfService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperationalReportController extends Controller
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

        // Query for operational expenses data
        $query = OperationalExpenses::select(
                'operational_expenses.*', 
                'trucks.plate_number', 
                'trucks.category',
                'operational_types.name as operational_type'
            )
            ->join('trucks', 'operational_expenses.truck_id', '=', 'trucks.id')
            ->join('operational_types', 'operational_expenses.operational_type_id', '=', 'operational_types.id')
            ->whereMonth('operational_expenses.date', $month_filter)
            ->whereYear('operational_expenses.date', $year_filter);

        // Apply filters
        if (!empty($truck_filter)) {
            $query->where('operational_expenses.truck_id', $truck_filter);
        }
        if (!empty($category_filter)) {
            $query->where('trucks.category', $category_filter);
        }

        $expenses = $query->orderBy('operational_expenses.date', 'desc')->get();

        // Calculate chart data
        $total_operasional = $expenses->count();
        $total_pengeluaran = 0;

        foreach ($expenses as $expense) {
            $total_pengeluaran += ($expense->quantity * $expense->price);
        }

        // Data for expense type chart
        $expense_by_type_query = OperationalTypes::select(
                'operational_types.id',
                'operational_types.name',
                DB::raw('SUM(operational_expenses.quantity * operational_expenses.price) as total_expense')
            )
            ->join('operational_expenses', 'operational_types.id', '=', 'operational_expenses.operational_type_id')
            ->join('trucks', 'operational_expenses.truck_id', '=', 'trucks.id')
            ->whereMonth('operational_expenses.date', $month_filter)
            ->whereYear('operational_expenses.date', $year_filter);

        if (!empty($truck_filter)) {
            $expense_by_type_query->where('operational_expenses.truck_id', $truck_filter);
        }
        if (!empty($category_filter)) {
            $expense_by_type_query->where('trucks.category', $category_filter);
        }

        $expense_by_type = $expense_by_type_query
            ->groupBy('operational_types.id', 'operational_types.name') // Menambahkan name ke GROUP BY clause
            ->orderBy('total_expense', 'desc')
            ->limit(5)
            ->get();

        // Handle export requests
        if ($request->has('export')) {
            $exportData = $this->prepareExportData($expenses);
            $filename = $this->generateFilename($month_filter, $year_filter, $truck_info, $category_filter);

            if ($request->export === 'excel') {
                return Excel::download(
                    new OperationalReportExport($exportData, $filename),
                    $filename . '.xlsx'
                );
            } elseif ($request->export === 'pdf') {
                $pdfService = new OperationalPdfService();
                return $pdfService->generate(
                    $exportData, 
                    $filename, 
                    $total_operasional, 
                    $total_pengeluaran, 
                    $expense_by_type, 
                    $month_filter, 
                    $year_filter, 
                    $truck_info
                );
            }
        }

        return view('reports.operational', compact(
            'expenses', 
            'trucks', 
            'truck_filter', 
            'category_filter',
            'month_filter', 
            'year_filter', 
            'truck_info', 
            'total_operasional',
            'total_pengeluaran', 
            'expense_by_type'
        ));
    }

    private function prepareExportData($expenses)
    {
        $data = [];
        
        foreach ($expenses as $expense) {
            $jumlah = $expense->quantity * $expense->price;
            
            $data[] = [
                'No. Plat' => $expense->plate_number,
                'Item' => $expense->operational_type,
                'Keterangan' => $expense->description,
                'Tanggal' => Carbon::parse($expense->date)->format('d/m/Y'),
                'Quantity' => $expense->quantity,
                'Harga' => number_format($expense->price, 0),
                'Jumlah' => number_format($jumlah, 0)
            ];
        }
        
        return $data;
    }

    private function generateFilename($month, $year, $truck_info, $category_filter)
    {
        $bulan = Carbon::createFromFormat('m', $month)->format('F');
        
        if ($truck_info) {
            return "Laporan_Operasional_{$truck_info->plate_number}_{$bulan}_{$year}";
        } elseif (!empty($category_filter)) {
            $cat_text = $category_filter == 'own' ? 'Milik_Sendiri' : 'TEP';
            return "Laporan_Operasional_Truk_{$cat_text}_{$bulan}_{$year}";
        } else {
            return "Laporan_Operasional_Semua_Truk_{$bulan}_{$year}";
        }
    }
}