<?php
namespace App\Http\Controllers;

use App\Models\Trucks;
use App\Models\Shipments;
use App\Models\OperationalExpenses;
use App\Models\OperationalTypes;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancialReportExport;
use App\Services\FinancialPdfService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
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

        // Array to store financial data
        $financial_data = [];
        $total_income = 0;
        $total_expense = 0;
        $total_net_income = 0;
        $total_installment = 0;

        // Query untuk setiap truk
        $query = Trucks::query();
        
        // Apply filters
        if (!empty($truck_filter)) {
            $query->where('id', $truck_filter);
        }
        if (!empty($category_filter)) {
            $query->where('category', $category_filter);
        }
        
        $trucks_to_process = $query->get();

        // Process data for each truck
        foreach ($trucks_to_process as $truck) {
            // Data pendapatan (shipments)
            $income_data = Shipments::selectRaw('SUM(tonnage * wage_per_ton) as total_transport_cost, SUM(travel_money) as total_travel_money')
                ->where('truck_id', $truck->id)
                ->whereMonth('unloading_date', $month_filter)
                ->whereYear('unloading_date', $year_filter)
                ->first();
            
            // Data pengeluaran (operational_expenses)
            $expense_data = DB::table('operational_expenses')
                ->join('operational_types', 'operational_expenses.operational_type_id', '=', 'operational_types.id')
                ->selectRaw('SUM(quantity * price) as total_expense, SUM(CASE WHEN operational_types.name = "Cicilan" THEN quantity * price ELSE 0 END) as installment')
                ->where('truck_id', $truck->id)
                ->whereMonth('date', $month_filter)
                ->whereYear('date', $year_filter)
                ->first();
            
            // Hitung penghasilan kotor
            $gross_income = ($income_data->total_transport_cost ?? 0) - ($income_data->total_travel_money ?? 0);
            $total_expense_amount = $expense_data->total_expense ?? 0;
            $installment_amount = $expense_data->installment ?? 0;
            
            // Hitung penghasilan bersih sebelum pembagian (untuk truk TEP)
            $net_before_share = $gross_income - $total_expense_amount;
            
            // Hitung penghasilan bersih berdasarkan kategori
            if ($truck->category == 'tep') {
                if ($net_before_share > 0) {
                    // Jika profit positif, PT Harry mendapat 10%
                    $net_income = $net_before_share * 0.1;
                } else {
                    // Jika rugi (negatif), PT Harry menanggung kerugian penuh
                    $net_income = $net_before_share;
                }
            } else {
                // Untuk truk milik sendiri, semua penghasilan/kerugian masuk
                $net_income = $net_before_share;
            }
            
            $financial_data[] = [
                'truck_id' => $truck->id,
                'plate_number' => $truck->plate_number,
                'category' => $truck->category,
                'gross_income' => $gross_income,
                'total_expense' => $total_expense_amount,
                'installment' => $installment_amount,
                'net_income' => $net_income
            ];
            
            $total_income += $gross_income;
            $total_expense += $total_expense_amount;
            $total_installment += $installment_amount;
            $total_net_income += $net_income;
        }

        // Handle export requests
        if ($request->has('export')) {
            $exportData = $this->prepareExportData($financial_data);
            $filename = $this->generateFilename($month_filter, $year_filter, $truck_info, $category_filter);

            if ($request->export === 'excel') {
                return Excel::download(
                    new FinancialReportExport($exportData, $filename),
                    $filename . '.xlsx'
                );
            } elseif ($request->export === 'pdf') {
                $pdfService = new FinancialPdfService();
                return $pdfService->generate(
                    $exportData, 
                    $filename, 
                    $total_income, 
                    $total_expense, 
                    $total_net_income, 
                    $month_filter, 
                    $year_filter, 
                    $truck_info
                );
            }
        }

        return view('reports.financial', compact(
            'financial_data',
            'trucks',
            'truck_filter',
            'category_filter',
            'month_filter',
            'year_filter',
            'truck_info',
            'total_income',
            'total_expense',
            'total_net_income',
            'total_installment'
        ));
    }

    private function prepareExportData($financial_data)
    {
        $data = [];
        
        foreach ($financial_data as $row) {
            $data[] = [
                'No. Plat' => $row['plate_number'],
                'Kategori' => $row['category'] == 'own' ? 'Milik Sendiri' : 'TEP',
                'Penghasilan Kotor' => number_format($row['gross_income'], 0),
                'Total Pengeluaran' => number_format($row['total_expense'], 0),
                'Cicilan' => number_format($row['installment'], 0),
                'Penghasilan Bersih' => number_format($row['net_income'], 0)
            ];
        }
        
        return $data;
    }

    private function generateFilename($month, $year, $truck_info, $category_filter)
    {
        $bulan = Carbon::createFromFormat('m', $month)->format('F');
        
        if ($truck_info) {
            return "Laporan_Keuangan_{$truck_info->plate_number}_{$bulan}_{$year}";
        } elseif (!empty($category_filter)) {
            $cat_text = $category_filter == 'own' ? 'Milik_Sendiri' : 'TEP';
            return "Laporan_Keuangan_Truk_{$cat_text}_{$bulan}_{$year}";
        } else {
            return "Laporan_Keuangan_Semua_Truk_{$bulan}_{$year}";
        }
    }
}