<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesTrendController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        
        $chartData = $this->getChartData($period);
        
        return view('admin.sales-trends.index', compact('chartData', 'period'));
    }

    private function getChartData($period)
    {
        switch ($period) {
            case 'daily':
                return $this->getDailyData();
            case 'weekly':
                return $this->getWeeklyData();
            case 'monthly':
                return $this->getMonthlyData();
            case 'yearly':
                return $this->getYearlyData();
            default:
                return $this->getDailyData();
        }
    }

    private function getDailyData()
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            // FIX: Exclude soft-deleted sales
            $daySales = Sale::whereNull('deleted_at')
                ->whereDate('sale_date', $date->format('Y-m-d'))
                ->get();
            
            $revenue[] = $daySales->sum('total_price');
            $transactions[] = $daySales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 7 Days Sales',
            'subtitle' => 'Daily sales performance'
        ];
    }

    private function getWeeklyData()
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d');
            
            // FIX: Exclude soft-deleted sales
            $weekSales = Sale::whereNull('deleted_at')
                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                ->get();
            
            $revenue[] = $weekSales->sum('total_price');
            $transactions[] = $weekSales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 4 Weeks Sales',
            'subtitle' => 'Weekly sales performance'
        ];
    }

    private function getMonthlyData()
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('F Y');
            
            // FIX: Exclude soft-deleted sales
            $monthSales = Sale::whereNull('deleted_at')
                ->whereMonth('sale_date', $month->month)
                ->whereYear('sale_date', $month->year)
                ->get();
            
            $revenue[] = $monthSales->sum('total_price');
            $transactions[] = $monthSales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 6 Months Sales',
            'subtitle' => 'Monthly sales performance'
        ];
    }

    private function getYearlyData()
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i);
            $labels[] = $year->format('Y');
            
            // FIX: Exclude soft-deleted sales
            $yearSales = Sale::whereNull('deleted_at')
                ->whereYear('sale_date', $year->year)
                ->get();
            
            $revenue[] = $yearSales->sum('total_price');
            $transactions[] = $yearSales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 5 Years Sales',
            'subtitle' => 'Yearly sales performance'
        ];
    }
}