<?php

namespace App\Http\Controllers;

use App\Models\CargoTypes;
use App\Models\Drivers;
use App\Models\MonthlyChecks;
use App\Models\OperationalExpenses;
use App\Models\OperationalTypes;
use App\Models\Shipments;
use App\Models\TruckDrivers;
use App\Models\Trucks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Lifetime Data
        // Total Income Since Beginning (Lifetime)
        $lifetime_income = Shipments::join('trucks', 'shipments.truck_id', '=', 'trucks.id')
            ->selectRaw('SUM(
                CASE WHEN trucks.category = "own" THEN (shipments.tonnage * shipments.wage_per_ton - shipments.travel_money)
                ELSE (shipments.tonnage * shipments.wage_per_ton - shipments.travel_money) * 0.1 END
            ) as lifetime_total_income')
            ->first();

        // Total Expenses Since Beginning (Lifetime)
        $lifetime_expense = OperationalExpenses::selectRaw('SUM(quantity * price) as lifetime_total_expense')
            ->first();

        // Total Tonnage
        $total_tonnage_data = Shipments::selectRaw('SUM(tonnage) as total_tonnage')
            ->first();

        $lifetime_profit = ($lifetime_income->lifetime_total_income ?? 0) - ($lifetime_expense->lifetime_total_expense ?? 0);

        // 2. Current Month Data
        $current_month = Carbon::now()->month;
        $current_year = Carbon::now()->year;

        // Total Income This Month
        $income_stats = Shipments::join('trucks', 'shipments.truck_id', '=', 'trucks.id')
            ->whereMonth('shipments.travel_money_date', $current_month)
            ->whereYear('shipments.travel_money_date', $current_year)
            ->selectRaw('SUM(
                CASE WHEN trucks.category = "own" THEN (shipments.tonnage * shipments.wage_per_ton - shipments.travel_money)
                ELSE (shipments.tonnage * shipments.wage_per_ton - shipments.travel_money) * 0.1 END
            ) as total_income')
            ->first();

        // Total Expenses This Month
        $expense_stats = OperationalExpenses::whereMonth('date', $current_month)
            ->whereYear('date', $current_year)
            ->selectRaw('SUM(quantity * price) as total_expense')
            ->first();

        $profit_this_month = ($income_stats->total_income ?? 0) - ($expense_stats->total_expense ?? 0);

        // 3. Total Trucks
        $truck_stats = Trucks::selectRaw('COUNT(*) as total, 
                                            SUM(CASE WHEN category = "own" THEN 1 ELSE 0 END) as own_trucks, 
                                            SUM(CASE WHEN category = "tep" THEN 1 ELSE 0 END) as tep_trucks')
            ->first();

        // 4. Last 6 Months Income Data
        $monthly_stats = Shipments::join('trucks', 'shipments.truck_id', '=', 'trucks.id')
            ->where('shipments.travel_money_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy(DB::raw('DATE_FORMAT(shipments.travel_money_date, "%Y-%m")'))
            ->selectRaw('DATE_FORMAT(shipments.travel_money_date, "%Y-%m") as month, 
                        SUM(CASE WHEN trucks.category = "own" THEN (shipments.tonnage * shipments.wage_per_ton - shipments.travel_money)
                        ELSE (shipments.tonnage * shipments.wage_per_ton - shipments.travel_money) * 0.1 END) as income')
            ->get();

        // 5. Most Popular Cargo This Month
        $popular_cargo = Shipments::join('cargo_types', 'shipments.cargo_type_id', '=', 'cargo_types.id')
            ->whereMonth('shipments.travel_money_date', $current_month)
            ->whereYear('shipments.travel_money_date', $current_year)
            ->groupBy('cargo_types.name')
            ->selectRaw('cargo_types.name, COUNT(*) as total_shipments, SUM(shipments.tonnage) as total_tonnage')
            ->orderByDesc('total_shipments')
            ->limit(5)
            ->get();

        // 6. Recent Activities (Shipments and Expenses)
        $recent_activities = DB::table(DB::raw('(
            SELECT "shipment" as type, shipments.travel_money_date as date, trucks.plate_number, cargo_types.name as description, (shipments.tonnage * shipments.wage_per_ton - shipments.travel_money) as amount
            FROM shipments
            JOIN trucks ON shipments.truck_id = trucks.id
            JOIN cargo_types ON shipments.cargo_type_id = cargo_types.id
            UNION ALL
            SELECT "expense" as type, operational_expenses.date, trucks.plate_number, operational_types.name as description, -(operational_expenses.quantity * operational_expenses.price) as amount
            FROM operational_expenses
            JOIN trucks ON operational_expenses.truck_id = trucks.id
            JOIN operational_types ON operational_expenses.operational_type_id = operational_types.id
        ) as temp_activities'))
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        // 7. Service Alerts
        $service_alerts = Trucks::join('monthly_checks', 'trucks.id', '=', 'monthly_checks.truck_id')
            ->where('monthly_checks.service_km_remaining', '<', 1000)
            ->select('trucks.plate_number', 'monthly_checks.service_km_remaining')
            ->get();

        // List of Trucks for Calendar
        $truck_list = Trucks::select('id', 'plate_number')->orderBy('plate_number')->get();

        return view('dashboard', compact(
            'lifetime_income', 'lifetime_expense', 'total_tonnage_data', 'lifetime_profit',
            'income_stats', 'expense_stats', 'profit_this_month', 'truck_stats', 'monthly_stats',
            'popular_cargo', 'recent_activities', 'service_alerts', 'truck_list'
        ));
    }
}
